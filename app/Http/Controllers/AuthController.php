<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Requests\RegisterRequest;
use App\Services\GamificationService;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // STEP 1: Validate credentials
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // STEP 2: Attempt authentication
        if (Auth::attempt($credentials)) {
            // STEP 3: Regenerate session for security
            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        // STEP 4: Return error if authentication fails
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        // STEP 1: Get validated and sanitized data from FormRequest
        $validated = $request->validated();
        $specialCode = $validated['special_code'] ?? '';
        $referralCode = $validated['referral_code'] ?? '';
        $role = 'member';

        // STEP 2: Determine role based on special code
        // Special codes (ADMIN123, TREASURER123) grant admin/treasurer roles
        if (!empty($specialCode)) {
            $validCodes = config('roles.special_codes', []);
            if (isset($validCodes[$specialCode])) {
                $role = $validCodes[$specialCode];
            } else {
                return redirect()->back()
                    ->withInput($request->except('special_code'))
                    ->withErrors(['special_code' => 'Invalid Special Code. Please contact the committee for the correct code.']);
            }
        }

        // STEP 3: Validate referral code BEFORE creating user (only for member roles)
        // If referral code is provided, verify it exists in database
        if (!empty($referralCode) && !in_array($role, ['admin', 'treasurer'])) {
            $referrer = User::where('referred_code', strtoupper(trim($referralCode)))->first();
            if (!$referrer) {
                // Invalid code: Block registration with clear error message
                return redirect()->back()
                    ->withInput($request->except('referral_code'))
                    ->withErrors(['referral_code' => 'The referral code you entered does not exist. Please check with your friend for the correct code, or leave this field empty.']);
            }
        }

        // STEP 4: Create user with sanitized data (only after all validations pass)
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'],
            'role' => $role,
        ]);

        // STEP 5: Process referral (code is guaranteed valid at this point)
        if (!empty($referralCode) && !in_array($role, ['admin', 'treasurer'])) {
            $referrer = User::where('referred_code', strtoupper(trim($referralCode)))->first();
            app(GamificationService::class)->processReferral($referrer, $user);
            return redirect('/login')->with('success', 'Registration successful! Your friend will receive 15 bonus points!');
        }

        return redirect('/login')->with('success', 'Registration successful! Please login.');
    }
}