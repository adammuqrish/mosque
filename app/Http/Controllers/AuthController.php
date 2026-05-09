<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    // Tunjuk Login Page
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Proses Login
    public function login(Request $request)
    {
        // 1. Validate Data
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Cuba Login
        if (Auth::attempt($credentials)) {
            // 3. Regenerate Session (Security)
            $request->session()->regenerate();

            // 4. Redirect ke Dashboard
            return redirect()->intended('/');
        }

        // 5. Kalau Gagal
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    // Register
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // 1. Validate Data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'required|string|max:10',
            'special_code' => 'nullable|string',
        ]);

        // 2. Logic Penentuan Role (STRICT MODE)
        $code = trim($request->special_code); // Buang ruang kosong depan/belakang
        $role = 'member'; // Default (Kalau field kosong)

        // Cek jika user memasukkan sesuatu dalam kotak code
        if (!empty($code)) {
            if ($code === 'ADMIN123') {
                $role = 'admin';
            } elseif ($code === 'TREASURER123') {
                $role = 'treasurer';
            } else {
                // --- SCENARIO ERROR: Kod salah ---
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['special_code' => 'Invalid Special Code. Please contact the committee for the correct code.']);
            }
        }

        // 3. Create User
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role' => $role,
        ]);

        return redirect('/login')->with('success', 'Registration successful! Please login.');
    }
}