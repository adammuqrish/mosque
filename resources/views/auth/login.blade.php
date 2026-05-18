<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Smart Mosque System</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&display=swap" rel="stylesheet">
    <style>
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        .animate-slideIn { animation: slideIn 0.3s ease-out forwards; }
        .font-islamic { font-family: 'Amiri', serif; }
    </style>
</head>

<body class="bg-[#FAFAF5] flex items-center justify-center min-h-screen p-4">

    <!-- STEP 2: Login Container with responsive layout -->
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
                <!-- Login Form -->
                <form method="POST" action="/login" data-loading>
                    @csrf

                    <!-- STEP 1: Flash message with icon styling -->
                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 rounded-lg flex items-start gap-3 animate-slideIn">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <p class="text-green-800">{{ session('success') }}</p>
                        </div>
                    @endif

                    <!-- STEP 2: Error message with icon styling -->
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

                    <!-- Email Input -->
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

                    <!-- Password Input -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-gray-700 text-sm font-bold">Password</label>
                            <a href="{{ route('password.request') }}" class="text-xs text-emerald-600 hover:underline">Forgot Password?</a>
                        </div>
                        <div class="flex justify-end mt-1">
                            <a href="{{ route('verification.resend.form') }}" class="text-xs text-amber-600 hover:underline">Resend Verification Email?</a>
                        </div>
                        <input id="password" type="password" name="password"
                            class="shadow appearance-none border rounded-lg w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition @error('password') border-red-500 ring-2 ring-red-200 @enderror"
                            placeholder="********" required>
                        @error('password')
                            <p class="text-red-500 text-sm mt-1 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- STEP 1: Submit Button -->
                    <button
                        class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2.5 px-6 rounded-lg transition duration-200 flex items-center justify-center gap-2 min-h-[48px]"
                        type="submit">
                        <span>Sign In</span>
                    </button>
                </form>

                <div class="mt-6 text-center text-xs text-gray-500">
                    <p>Default password for all accounts: <strong>password</strong></p>
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

                    <!-- ADMIN CARD -->
                    <div class="border rounded p-4 hover:bg-gray-50 cursor-pointer transition flex items-center justify-between group"
                        onclick="fillLogin('admin@mosque.com', 'password')">
                        <div class="flex items-center">
                            <div class="bg-red-100 text-red-600 rounded-full p-2 mr-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-bold text-gray-800 text-sm">Administrator</p>
                                <p class="text-xs text-gray-500">Manage Donations, Events</p>
                            </div>
                        </div>
                        <span class="text-gray-400 group-hover:text-emerald-600">➔</span>
                    </div>

                    <!-- BENDAHARI CARD -->
                    <div class="border rounded p-4 hover:bg-gray-50 cursor-pointer transition flex items-center justify-between group"
                        onclick="fillLogin('treasurer@mosque.com', 'password')">
                        <div class="flex items-center">
                            <div class="bg-yellow-100 text-yellow-600 rounded-full p-2 mr-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-bold text-gray-800 text-sm">Bendahari</p>
                                <p class="text-xs text-gray-500">Approve Withdrawals</p>
                            </div>
                        </div>
                        <span class="text-gray-400 group-hover:text-emerald-600">➔</span>
                    </div>

                    <!-- MEMBER CARD -->
                    <div class="border rounded p-4 hover:bg-gray-50 cursor-pointer transition flex items-center justify-between group"
                        onclick="fillLogin('ali@mosque.com', 'password')">
                        <div class="flex items-center">
                            <div class="bg-blue-100 text-blue-600 rounded-full p-2 mr-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
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

    <!-- JAVASCRIPT LOGIC -->
    <script>
        function fillLogin(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
        }
    </script>

</body>

</html>
