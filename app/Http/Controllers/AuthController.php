<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Str;
use App\Models\User;
use App\Http\Requests\RegisterRequest;
use App\Services\GamificationService;
use App\Services\RegistrationCodeService;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();
            if (in_array($user->role, ['admin', 'treasurer'])) {
                if (!$user->hasVerifiedEmail()) {
                    $user->markEmailAsVerified();
                }
            } elseif (!$user->hasVerifiedEmail()) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Sila sahkan e-mel anda terlebih dahulu. Semak peti masuk e-mel anda untuk pautan pengesahan.',
                ])->onlyInput('email');
            }

            return redirect()->intended('/');
        }

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

    public function register(RegisterRequest $request, RegistrationCodeService $codeService)
    {
        $validated = $request->validated();
        $specialCode = $validated['special_code'] ?? '';
        $referralCode = $validated['referral_code'] ?? '';
        $role = 'member';

        if (!empty($specialCode)) {
            $role = $codeService->getRoleForCode($specialCode);
            if (!$role) {
                return redirect()->back()
                    ->withInput($request->except('special_code'))
                    ->withErrors(['special_code' => 'Invalid Special Code. Please contact the committee for the correct code.']);
            }
        }

        if (!empty($referralCode) && !in_array($role, ['admin', 'treasurer'])) {
            $referrer = User::where('referred_code', strtoupper(trim($referralCode)))->first();
            if (!$referrer) {
                return redirect()->back()
                    ->withInput($request->except('referral_code'))
                    ->withErrors(['referral_code' => 'The referral code you entered does not exist. Please check with your friend for the correct code, or leave this field empty.']);
            }
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'],
            'role' => $role,
        ]);

        $user->sendEmailVerificationNotification();

        if (!empty($referralCode) && !in_array($role, ['admin', 'treasurer'])) {
            $referrer = User::where('referred_code', strtoupper(trim($referralCode)))->first();
            app(GamificationService::class)->processReferral($referrer, $user);
            return redirect('/login')->with('success', 'Registration successful! Please verify your email, then login. Your friend will receive 15 bonus points!');
        }

        return redirect('/login')->with('success', 'Registration successful! Please check your email to verify your account before logging in.');
    }

    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.passwords.reset', ['token' => $token, 'email' => $request->email]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    public function showVerifyNotice()
    {
        if (Auth::user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        return view('auth.verify');
    }

    public function verify(Request $request)
    {
        $user = User::find($request->route('id'));

        if (!$user || !hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            return redirect()->route('login')->withErrors(['email' => 'Invalid verification link.']);
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('login')->with('success', 'Your email is already verified. Please login.');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return redirect()->route('login')->with('success', 'Email verified successfully! You can now login.');
    }

    public function showResendForm()
    {
        return view('auth.resend-verification');
    }

    public function resendVerification(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if ($user && !$user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
        }

        return back()->with('success', 'Jika alamat e-mel tersebut didaftarkan dan belum disahkan, pautan pengesahan telah dihantar.');
    }
}