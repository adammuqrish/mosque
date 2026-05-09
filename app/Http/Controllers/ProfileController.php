<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\VolunteerProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. Get Volunteer Profile
        $profile = VolunteerProfile::where('user_id', $user->id)->first();

        // 2. SMART RECOMMENDATION LOGIC (Copy dari DashboardController)
        $recommendedEvents = collect();

        if ($user->role == 'member') { // Hanya member boleh nampak recommendation
            if ($profile && !empty($profile->skills)) {
                $rawSkills = $profile->skills;

                // Manual Decode (Fix untuk String issue)
                if (is_string($rawSkills)) {
                    $userSkills = json_decode($rawSkills, true);
                } else {
                    $userSkills = (array) $rawSkills;
                }

                $allEvents = Event::all();

                foreach ($userSkills as $userSkill) {
                    foreach ($allEvents as $event) {
                        $rawEventSkills = $event->required_skills;
                        if (is_string($rawEventSkills)) {
                            $eventSkills = json_decode($rawEventSkills, true);
                        } else {
                            $eventSkills = (array) $rawEventSkills;
                        }

                        foreach ($eventSkills as $eventSkill) {
                            if (strtolower(trim($userSkill)) === strtolower(trim($eventSkill))) {
                                if (!$recommendedEvents->contains('id', $event->id)) {
                                    $recommendedEvents->push($event);
                                }
                            }
                        }
                    }
                }

                // Buang event yang dah join
                $joinedEventIds = $user->events->pluck('id')->toArray();
                $recommendedEvents = $recommendedEvents->reject(function ($event) use ($joinedEventIds) {
                    return in_array($event->id, $joinedEventIds);
                });
            }
        }

        return view('profile.index', compact('user', 'profile', 'recommendedEvents'));
    }

    // Update Personal Info
    public function updateInfo(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'age' => 'nullable|integer|min:1|max:120',
            'address' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        $user->update([
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'age' => $request->input('age'),
            'address' => $request->input('address'),
        ]);

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    // Update Skills (Dipindah dari VolunteerController)
    public function updateSkills(Request $request)
    {
        $request->validate([
            'skills' => 'required|string',
            'availability' => 'array',
            'hobbies' => 'nullable|string',
            'interests' => 'nullable|string',
            'languages' => 'nullable|string',
            'location' => 'nullable|string',
            'health_status' => 'nullable|string',
            'experience' => 'nullable|string',
            'long_term_availability' => 'nullable|string',
        ]);

        // Helper function untuk parse comma string ke array
        $parse = function ($str) {
            return array_filter(array_map('trim', explode(',', $str)));
        };

        $skillsArray = $parse($request->input('skills'));
        $hobbiesArray = $parse($request->input('hobbies', ''));
        $interestsArray = $parse($request->input('interests', ''));
        $languagesArray = $parse($request->input('languages', ''));

        VolunteerProfile::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'skills' => json_encode($skillsArray),
                'availability' => json_encode($request->input('availability', [])),
                'hobbies' => json_encode($hobbiesArray),
                'interests' => json_encode($interestsArray),
                'languages' => json_encode($languagesArray),
                'location' => $request->input('location'),
                'health_status' => $request->input('health_status'),
                'experience' => $request->input('experience'),
                'long_term_availability' => $request->input('long_term_availability'),
            ]
        );

        return redirect()->back()->with('success', 'Skills updated successfully!');
    }

    // Update Password
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Current password is incorrect.');
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with('success', 'Password changed successfully!');
    }
}