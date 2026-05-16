<?php

namespace App\Http\Controllers;

use App\Models\VolunteerProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\VolunteerProfileRequest;
use App\Services\GamificationService;
use App\Services\RecommendationService;

class ProfileController extends Controller
{
    protected $recommendationService;

    public function __construct(RecommendationService $recommendationService)
    {
        $this->recommendationService = $recommendationService;
    }

    public function index()
    {
        $user = Auth::user();
        $profile = VolunteerProfile::where('user_id', $user->id)->first();

        // STEP 1: Check if user can regenerate referral code (monthly limit)
        // If user already has a code and updated within the last month, prevent regeneration
        $canRegenerate = !$user->referred_code || $user->updated_at->lt(now()->subMonth());

        // STEP 2: Get recommended events using centralized service
        $recommendedEvents = collect();

        if ($user->role === 'member') {
            $recommendedEvents = $this->recommendationService->getRecommendations($user);
        }

        // STEP 3: Pass referral code and regeneration status to view
        return view('profile.index', [
            'user' => $user,
            'profile' => $profile,
            'recommendedEvents' => $recommendedEvents,
            'referralCode' => $user->referred_code,
            'canRegenerate' => $canRegenerate,
        ]);
    }

    /**
     * Generate or regenerate referral code for authenticated user.
     * Uses GamificationService to create unique 8-char code.
     * Monthly regeneration limit to prevent abuse.
     */
    public function generateReferralCode(Request $request, GamificationService $gamification)
    {
        $user = auth()->user();

        // STEP 1: Enforce monthly regeneration limit
        // If user has a code and updated it within the last month, reject request
        if ($user->referred_code && $user->updated_at->gt(now()->subMonth())) {
            return response()->json([
                'error' => 'Monthly regenerate limit reached. Please wait before generating a new code.',
                'success' => false,
            ], 429);
        }

        // STEP 2: Generate unique referral code via GamificationService
        // Code is 8-character MD5 hash based on user ID, email, and timestamp
        $code = $gamification->generateReferralCode($user);

        // STEP 3: Return success response with generated code
        return response()->json([
            'code' => $code,
            'success' => true,
        ]);
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,gif,webp|max:2048',
        ]);

        $user = Auth::user();

        // STEP 1: Handle existing avatar deletion
        if ($user->avatar) {
            $oldPath = storage_path('app/public/avatars/' . $user->avatar);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        // STEP 2: Store new avatar
        $file = $request->file('avatar');
        $filename = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
        $destinationDir = storage_path('app/public/avatars');

        if (!is_dir($destinationDir)) {
            mkdir($destinationDir, 0755, true);
        }

        $file->move($destinationDir, $filename);

        // STEP 3: Update user avatar
        $user->update(['avatar' => $filename]);

        return response()->json([
            'success' => true,
            'avatar_url' => $user->avatar_url,
            'initials' => $user->initials,
        ]);
    }

    public function deleteAvatar(Request $request)
    {
        $user = Auth::user();

        if ($user->avatar) {
            $oldPath = storage_path('app/public/avatars/' . $user->avatar);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
            $user->update(['avatar' => null]);
        }

        return response()->json(['success' => true]);
    }

    public function updateInfo(ProfileUpdateRequest $request)
    {
        // STEP 1: Get validated and sanitized data
        $validated = $request->validated();
        $user = Auth::user();

        // STEP 2: Update user profile
        $user->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'age' => $validated['age'] ?? null,
            'address' => $validated['address'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    public function updateSkills(VolunteerProfileRequest $request)
    {
        // STEP 1: Get validated and sanitized data
        $validated = $request->validated();

        // STEP 2: Arrays are already sanitized by VolunteerProfileRequest
        // Just filter out empty values
        $skillsArray = array_filter($validated['skills'] ?? [], function($v) {
            return is_string($v) && trim($v) !== '';
        });
        $hobbiesArray = array_filter($validated['hobbies'] ?? [], function($v) {
            return is_string($v) && trim($v) !== '';
        });
        $interestsArray = array_filter($validated['interests'] ?? [], function($v) {
            return is_string($v) && trim($v) !== '';
        });
        $languagesArray = array_filter($validated['languages'] ?? [], function($v) {
            return is_string($v) && trim($v) !== '';
        });

        // STEP 3: Update or create volunteer profile
        VolunteerProfile::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'skills' => json_encode($skillsArray),
                'availability' => json_encode($validated['availability'] ?? []),
                'hobbies' => json_encode($hobbiesArray),
                'interests' => json_encode($interestsArray),
                'languages' => json_encode($languagesArray),
                'location' => $validated['location'] ?? null,
                'health_status' => $validated['health_status'] ?? null,
                'experience' => $validated['experience'] ?? null,
                'long_term_availability' => $validated['long_term_availability'] ?? null,
            ]
        );

        return redirect()->back()->with('success', 'Skills updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        // STEP 1: Validate password change request
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|max:50|confirmed',
        ]);

        $user = Auth::user();

        // STEP 2: Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Current password is incorrect.');
        }

        // STEP 3: Update password
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with('success', 'Password changed successfully!');
    }
}