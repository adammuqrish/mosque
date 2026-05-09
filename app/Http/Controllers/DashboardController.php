<?php

namespace App\Http\Controllers;

use App\Models\Event; // Import Event Model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $events = Event::all();

        // --- SMART LOGIC: Rule-Based Recommendation ---
        $recommendedEvents = collect(); // Empty collection

        if (Auth::check()) {
            $profile = Auth::user()->volunteerProfile;

            if ($profile && !empty($profile->skills)) {
                // --- MANUAL FIX START ---
                // Ambil data mentah
                $rawSkills = $profile->skills;

                // Jika data tu adalah String (seperti error tadi), kita decode jadi Array
                if (is_string($rawSkills)) {
                    $userSkills = json_decode($rawSkills, true);
                } else {
                    // Jika dah Array (normal), guna je
                    $userSkills = (array) $rawSkills;
                }
                // --- MANUAL FIX END ---

                // TAMPAN KOD NI UNTUK TENGOK APA SYSTEM BACA
                // dd($userSkills);

                // Ambil semua events dulu
                $allEvents = Event::all();

                foreach ($allEvents as $event) {
                    $matchScore = 0;

                    // 1. Check Location (Simple String Matching)
                    $userLoc = strtolower($profile->location ?? '');
                    $eventLoc = strtolower($event->event_location ?? '');
                    if ($userLoc && $eventLoc && (strpos($userLoc, $eventLoc) !== false || strpos($eventLoc, $userLoc) !== false)) {
                        $matchScore++;
                    }

                    // 2. Check Languages
                    $rawEventLangs = $event->required_languages;
                    $eventLangs = is_string($rawEventLangs) ? json_decode($rawEventLangs, true) : (array) $rawEventLangs;

                    if (!empty($eventLangs)) {
                        foreach ($userSkills as $lang) {
                            if (in_array(strtolower($lang), array_map('strtolower', $eventLangs))) {
                                $matchScore++; // Or use specific logic
                            }
                        }
                    }

                    // 3. Check Skills (Original Logic)
                    $rawEventSkills = $event->required_skills;
                    $eventSkills = is_string($rawEventSkills) ? json_decode($rawEventSkills, true) : (array) $rawEventSkills;

                    foreach ($eventSkills as $eventSkill) {
                        if (in_array(strtolower($eventSkill), array_map('strtolower', $userSkills))) {
                            $matchScore++;
                        }
                    }

                    // Add event if match score > 0
                    if ($matchScore > 0) {
                        if (!$recommendedEvents->contains('id', $event->id)) {
                            $recommendedEvents->push($event);
                        }
                    }
                }

                // Buang event yang user dah join
                $joinedEventIds = Auth::user()->events->pluck('id')->toArray();
                $recommendedEvents = $recommendedEvents->reject(function ($event) use ($joinedEventIds) {
                    return in_array($event->id, $joinedEventIds);
                });
            }
        }
        // ------------------------------------------------

        // TAMBAH LINE NI UNTUK DEBUG
        // dd($recommendedEvents);

        // return view('dashboard', compact('events'));
        return view('dashboard', compact('events', 'recommendedEvents'));
    }
}