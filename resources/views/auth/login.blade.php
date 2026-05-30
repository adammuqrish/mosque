@extends('layouts.auth')

@section('title', 'Login')

@section('card')
    <div class="w-full max-w-6xl flex flex-col md:flex-row gap-6 lg:gap-8">

        <!-- LEFT COLUMN: LOGIN FORM -->
        <div class="w-full md:w-2/3 bg-white rounded-lg shadow-xl overflow-hidden">
            <div class="bg-emerald-800 p-6 text-center pattern-islamic">
                <h1 class="text-2xl font-bold text-white">
                    <span class="font-islamic text-emerald-200 text-lg mr-2">بِسْمِ ٱللَّهِ</span>Smart Mosque System
                </h1>
                <p class="text-emerald-200 text-sm">Assalamu Alaikum — Silakan log masuk untuk akses sistem.</p>
            </div>

            <div class="p-8">
                <form method="POST" action="/login" data-loading>
                    @csrf

                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 rounded-lg flex items-start gap-3 animate-slideIn">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <p class="text-green-800">{{ session('success') }}</p>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg flex items-start gap-3 animate-slideIn">
                            <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <p class="text-red-800 font-semibold">Login Failed</p>
                                <p class="text-red-700 text-sm">{{ $errors->first() }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Email Address</label>
                        <input id="email" type="email" name="email"
                            class="shadow appearance-none border rounded-lg w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition @error('email') border-red-500 ring-2 ring-red-200 @enderror"
                            placeholder="admin@mosque.com" value="{{ old('email') }}" required>
                        @error('email')
                            <p class="text-red-500 text-sm mt-1 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-gray-700 text-sm font-bold">Password</label>
                            <a href="{{ route('password.request') }}" class="text-xs text-emerald-600 hover:underline">Forgot Password?</a>
                        </div>
                        <div class="relative">
                            <input id="password" type="password" name="password"
                                class="shadow appearance-none border rounded-lg w-full py-3 pr-10 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition @error('password') border-red-500 ring-2 ring-red-200 @enderror"
                                placeholder="********" required>
                            <button type="button" onclick="togglePassword(this)"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none">
                                <svg class="w-5 h-5 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg class="w-5 h-5 eye-off-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-red-500 text-sm mt-1 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="mb-4 text-center">
                        <a href="{{ route('verification.resend.form') }}" class="text-sm text-amber-600 hover:underline font-medium">Resend Verification Email?</a>
                    </div>

                    <button
                        class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2.5 px-6 rounded-lg transition duration-200 flex items-center justify-center gap-2 min-h-[48px]"
                        type="submit">
                        <span>Sign In</span>
                    </button>
                </form>

                <div class="mt-6 text-center text-xs text-gray-500">
                    <details class="cursor-pointer">
                        <summary class="text-gray-400 hover:text-gray-600">Demo credentials (click to expand)</summary>
                        <p class="mt-2">Default password for all accounts: <strong>password</strong></p>
                    </details>
                </div>

                <div class="mt-6 text-center text-xs text-gray-500">
                    <p>Don't have an account? <a href="/register"
                            class="text-emerald-600 font-bold hover:underline cursor-pointer">Register Here</a></p>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: QUICK LOGIN (DEMO PURPOSES ONLY) -->
        <div class="w-full md:w-1/3">
            <div class="bg-white rounded-lg shadow-xl overflow-hidden border-t-4 border-gray-400">
                <div class="bg-gray-800 p-4 text-center">
                    <h2 class="text-lg font-bold text-white">Quick Login</h2>
                    <p class="text-gray-400 text-xs mt-1">(For Demo)</p>
                </div>

                <div class="p-4 space-y-4">

                    <div class="border rounded p-4 hover:bg-gray-50 cursor-pointer transition flex items-center justify-between group"
                        onclick="fillLogin('admin@mosque.com', 'password')">
                        <div class="flex items-center">
                            <div class="bg-red-100 text-red-600 rounded-full p-2 mr-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-bold text-gray-800 text-sm">Administrator</p>
                                <p class="text-xs text-gray-500">Manage Donations, Events</p>
                            </div>
                        </div>
                        <span class="text-gray-400 group-hover:text-emerald-600">➔</span>
                    </div>

                    <div class="border rounded p-4 hover:bg-gray-50 cursor-pointer transition flex items-center justify-between group"
                        onclick="fillLogin('treasurer@mosque.com', 'password')">
                        <div class="flex items-center">
                            <div class="bg-yellow-100 text-yellow-600 rounded-full p-2 mr-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-bold text-gray-800 text-sm">Bendahari</p>
                                <p class="text-xs text-gray-500">Approve Withdrawals</p>
                            </div>
                        </div>
                        <span class="text-gray-400 group-hover:text-emerald-600">➔</span>
                    </div>

                    <div class="border rounded p-4 hover:bg-gray-50 cursor-pointer transition flex items-center justify-between group"
                        onclick="fillLogin('ali@mosque.com', 'password')">
                        <div class="flex items-center">
                            <div class="bg-blue-100 text-blue-600 rounded-full p-2 mr-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-bold text-gray-800 text-sm">Jemaah / Member</p>
                                <p class="text-xs text-gray-500">Join Events, Update Skills</p>
                            </div>
                        </div>
                        <span class="text-gray-400 group-hover:text-emerald-600">➔</span>
                    </div>

                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
<script>
    function fillLogin(email, password) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = password;
    }

    function togglePassword(btn) {
        const input = btn.closest('.relative').querySelector('input');
        const isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';
        btn.querySelector('.eye-icon').classList.toggle('hidden');
        btn.querySelector('.eye-off-icon').classList.toggle('hidden');
    }
</script>
@endpush
