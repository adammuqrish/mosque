<?php

namespace App\Services;

use App\Models\Event;
use App\Models\User;
use App\Models\VolunteerProfile;
use Illuminate\Support\Collection;

class RecommendationService
{
    /**
     * Get recommended events for a user based on their profile.
     * NEVER returns an empty list for members — falls back to open events
     * when the user has no meaningful volunteer profile criteria.
     *
     * @return Collection<int, array{event: Event, score: int, reasons: array, hasCriteria: bool}>
     */
    public function getRecommendations(User $user, int $limit = null): Collection
    {
        // STEP 1: Only recommend to members
        if ($user->role !== 'member') {
            return collect();
        }

        // STEP 2: Get user profile and determine if meaningful criteria exist
        $profile = $user->volunteerProfile;

        $hasCriteria = $this->userHasCriteria($profile);

        // STEP 3: If no criteria, fall back to open events with available capacity
        // prioritizing events with NO strict requirements via MySQL CASE ordering.
        if (!$hasCriteria) {
            return $this->getFallbackEvents($user, $limit);
        }

        // STEP 4: User HAS criteria — run the full weighted matching logic
        return $this->getScoredRecommendations($user, $profile, $limit);
    }

    /**
     * Detect whether a volunteer profile contains meaningful matching criteria.
     * Returns true only if at least one of skills/hobbies/interests/languages
     * is non-null and non-empty.
     */
    private function userHasCriteria(?VolunteerProfile $profile): bool
    {
        if (!$profile) {
            return false;
        }

        $skills    = $this->parseToArray($profile->skills);
        $hobbies   = $this->parseToArray($profile->hobbies);
        $interests = $this->parseToArray($profile->interests);
        $languages = $this->parseToArray($profile->languages);

        return count($skills) > 0
            || count($hobbies) > 0
            || count($interests) > 0
            || count($languages) > 0;
    }

    /**
     * Fallback: return open events with available capacity, prioritizing those
     * with NO strict requirements (skills/hobbies/languages are NULL/empty).
     * Uses MySQL-compatible CASE ordering.
     *
     * @return Collection<int, array{event: Event, score: int, reasons: array, hasCriteria: bool}>
     */
    private function getFallbackEvents(User $user, ?int $limit): Collection
    {
        $now = now();

        // Query open, future, not-full events, excluding already-joined ones.
        // NOTE: Use `where` (not `whereColumn`) because whereColumn does NOT
        // support Closure subqueries in Laravel 8 — it passes the Closure to
        // Grammar::wrap() which calls stripos() on it and crashes.
        $query = Event::query()
            ->where('status', 'open')
            ->where('event_date', '>', $now)
            ->where('max_volunteers', '>', function ($sub) {
                $sub->selectRaw('COUNT(*)')
                    ->from('event_volunteer')
                    ->whereColumn('event_id', 'events.id');
            })
            // Exclude events the user has already joined
            ->whereNotExists(function ($sub) use ($user) {
                $sub->selectRaw('1')
                    ->from('event_volunteer')
                    ->whereColumn('event_id', 'events.id')
                    ->where('user_id', $user->id);
            });

        // Use MySQL CASE to prioritize events with NO strict requirements
        // JSON columns stored as NULL or '[]' — check for both
        $query->orderByRaw("
            CASE
                WHEN (required_skills IS NULL OR JSON_LENGTH(required_skills) = 0)
                 AND (required_hobbies IS NULL OR JSON_LENGTH(required_hobbies) = 0)
                 AND (required_languages IS NULL OR JSON_LENGTH(required_languages) = 0)
                THEN 0
                ELSE 1
            END
        ")->orderBy('event_date', 'asc');

        if ($limit !== null) {
            $query->limit($limit);
        }

        $events = $query->get();

        return $events->map(function ($event) {
            return [
                'event' => $event,
                'score' => 0,
                'reasons' => [],
                'hasCriteria' => false,
            ];
        });
    }

    /**
     * Full weighted matching for users WITH profile criteria.
     *
     * @return Collection<int, array{event: Event, score: int, reasons: array, hasCriteria: bool}>
     */
    private function getScoredRecommendations(User $user, VolunteerProfile $profile, ?int $limit): Collection
    {
        // STEP 1: Parse user criteria
        $userSkills = $this->parseToArray($profile->skills);
        $userHobbies = $this->parseToArray($profile->hobbies);
        $userInterests = $this->parseToArray($profile->interests);
        $userLanguages = $this->parseToArray($profile->languages);
        $userLocation = strtolower(trim($profile->location ?? ''));

        // STEP 2: Get eligible events (open status, future date, not full)
        $eligibleEvents = $this->getEligibleEvents($user);

        // STEP 3: Calculate match scores for each event
        $scoredEvents = [];

        foreach ($eligibleEvents as $event) {
            $matchScore = 0;
            $matchReasons = [];

            // A. Check Location Match (+2 points if location matches)
            if ($userLocation && $this->locationMatches($userLocation, $event->event_location)) {
                $matchScore += 2;
                $matchReasons[] = 'location';
            }

            // B. Check Language Match (+1 point per language match, max 3)
            $eventLanguages = $this->parseToArray($event->required_languages);
            $langMatches = $this->countMatches($userLanguages, $eventLanguages);
            if ($langMatches > 0) {
                $matchScore += min($langMatches, 3);
                $matchReasons[] = 'languages';
            }

            // C. Check Skills Match (+2 points per skill match, max 6)
            $eventSkills = $this->parseToArray($event->required_skills);
            $skillMatches = $this->countMatches($userSkills, $eventSkills);
            if ($skillMatches > 0) {
                $matchScore += min($skillMatches * 2, 6);
                $matchReasons[] = 'skills';
            }

            // D. Check Hobbies Match (+1 point per hobby match, max 3)
            $eventHobbies = $this->parseToArray($event->required_hobbies);
            $hobbyMatches = $this->countMatches($userHobbies, $eventHobbies);
            if ($hobbyMatches > 0) {
                $matchScore += min($hobbyMatches, 3);
                $matchReasons[] = 'hobbies';
            }

            // E. Check Interests Match (+1 point per interest match, max 3)
            $interestMatches = $this->countMatches($userInterests, $eventHobbies);
            if ($interestMatches > 0) {
                $matchScore += min($interestMatches, 3);
                $matchReasons[] = 'interests';
            }

            // Only include if there's at least one match
            if ($matchScore > 0) {
                $scoredEvents[] = [
                    'event' => $event,
                    'score' => $matchScore,
                    'reasons' => $matchReasons,
                    'hasCriteria' => true,
                ];
            }
        }

        // STEP 4: Sort by score (descending) - highest matches first
        usort($scoredEvents, function ($a, $b) {
            return $b['score'] - $a['score'];
        });

        // STEP 5: Extract just the events (with scores for display)
        $recommended = collect($scoredEvents);

        // STEP 6: Apply limit if specified
        if ($limit !== null) {
            $recommended = $recommended->take($limit);
        }

        return $recommended;
    }

    /**
     * Get events that are eligible for recommendations.
     * Filters: open status, future events, not full, not already joined.
     */
    private function getEligibleEvents(User $user): Collection
    {
        $now = now();

        return Event::all()
            // Only open events
            ->filter(function ($event) {
                return $event->status === 'open';
            })
            // Only future events
            ->filter(function ($event) use ($now) {
                return $event->event_date->isFuture();
            })
            // Only events with capacity
            ->filter(function ($event) {
                return $event->volunteers()->count() < $event->max_volunteers;
            })
            // Exclude already joined events
            ->filter(function ($event) use ($user) {
                return !$user->events()->where('event_id', $event->id)->exists();
            });
    }

    /**
     * Parse field to array (handles JSON string or array).
     */
    private function parseToArray($value): array
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }
        
        return is_array($value) ? $value : [];
    }

    /**
     * Check if user location matches event location.
     */
    private function locationMatches(string $userLoc, ?string $eventLoc): bool
    {
        if (!$eventLoc) {
            return false;
        }

        $eventLoc = strtolower(trim($eventLoc));

        // Check if either contains the other (partial match)
        return strpos($userLoc, $eventLoc) !== false 
            || strpos($eventLoc, $userLoc) !== false;
    }

    /**
     * Count matching items between two arrays (case-insensitive).
     */
    private function countMatches(array $userItems, array $eventItems): int
    {
        $matches = 0;
        $normalizedEvent = array_map(function ($item) {
            return strtolower(trim($item));
        }, $eventItems);

        foreach ($userItems as $userItem) {
            $normalizedUser = strtolower(trim($userItem));
            if (in_array($normalizedUser, $normalizedEvent)) {
                $matches++;
            }
        }

        return $matches;
    }
}
