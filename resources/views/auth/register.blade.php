<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Smart Mosque System</title>
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

    <div class="w-full max-w-md bg-white rounded-lg shadow-xl overflow-hidden">

        <!-- Header -->
        <div class="bg-emerald-800 p-6 text-center pattern-islamic">
            <h1 class="text-2xl font-bold text-white">
                <span class="font-islamic text-emerald-200 text-lg mr-2">بِسْمِ ٱللَّهِ</span>Join Community
            </h1>
            <p class="text-emerald-200 text-sm">Create an account to start volunteering and earn rewards.</p>
        </div>

        <!-- Form -->
        <div class="p-6">
            <form method="POST" action="/register" data-loading>
                @csrf

                <!-- STEP 1: Inline validation errors with icon styling -->
                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg animate-slideIn">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <p class="font-semibold text-red-800">Please fix the following errors:</p>
                                <ul class="list-disc list-inside text-sm text-red-700 mt-1 space-y-0.5">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Name -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Full Name</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="w-full border rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none transition @error('name') border-red-500 ring-2 ring-red-200 @enderror"
                        placeholder="Enter your full name" required>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Email -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="w-full border rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none transition @error('email') border-red-500 ring-2 ring-red-200 @enderror"
                        placeholder="you@example.com" required>
                    @error('email')
                        <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Phone -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Phone Number</label>
                    <input type="tel" name="phone" value="{{ old('phone') }}"
                        class="w-full border rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none transition @error('phone') border-red-500 ring-2 ring-red-200 @enderror"
                        placeholder="0123456789" required>
                    @error('phone')
                        <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- STEP 4: Password Fields -->
                <div class="mb-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                        <input type="password" name="password"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition @error('password') border-red-500 ring-2 ring-red-200 @enderror"
                            placeholder="Min 8 chars" required>
                        @error('password')
                            <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"></path></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Confirm Password</label>
                        <input type="password" name="password_confirmation"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                            placeholder="Re-enter password" required>
                    </div>
                </div>

                <!-- Divider -->
                <div class="flex items-center my-4">
                    <div class="flex-grow border-t border-gray-200"></div>
                    <span class="flex-shrink-0 mx-3 text-gray-400 text-xs bg-white px-2">Optional Codes</span>
                    <div class="flex-grow border-t border-gray-200"></div>
                </div>

                <!-- STEP 1: Referral Code Input (Member-to-Member Referrals) -->
                <div class="mb-4">
                    <label class="block text-gray-600 text-sm font-medium mb-2">Referral Code <span class="text-gray-400 font-normal">(Optional)</span></label>
                    <input type="text" name="referral_code" value="{{ old('referral_code') }}"
                        class="w-full border border-dashed rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none transition @error('referral_code') border-red-500 ring-2 ring-red-200 @enderror"
                        placeholder="Enter code from a friend (e.g., A3F9B2C1)">
                    @error('referral_code')
                        <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                    <p class="text-xs text-gray-400 mt-1">Got a referral code from a friend? Enter it here! Invalid codes will block registration.</p>
                </div>

                <!-- STEP 2: Special Code Input (Staff/Committee Registration) -->
                <div class="mb-6">
                    <label class="block text-gray-600 text-sm font-medium mb-2">Staff Special Code <span class="text-gray-400 font-normal">(Optional)</span></label>
                    <input type="text" name="special_code" value="{{ old('special_code') }}"
                        class="w-full border border-dashed rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none transition @error('special_code') border-red-500 ring-2 ring-red-200 @enderror"
                        placeholder="Enter code if you are Staff">
                    @error('special_code')
                        <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                    <p class="text-xs text-gray-400 mt-1">Contact committee for staff registration codes.</p>
                </div>

                <!-- Button -->
                <button type="button" onclick="autoFillRegister()"
                    class="w-full bg-blue-400 hover:bg-blue-500 text-white font-bold py-3 px-4 rounded-lg transition flex items-center justify-center gap-2 mb-3">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Auto Fill
                </button>
                <button type="submit"
                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-4 rounded-lg transition flex items-center justify-center gap-2">
                    <span>Register Now</span>
                </button>

            </form>

            <div class="mt-4 text-center text-sm">
                <span class="text-gray-600">Already have an account?</span>
                <a href="/login" class="text-emerald-600 font-bold hover:underline">Login</a>
            </div>
        </div>

</body>

<script>
function autoFillRegister() {
    const firstNames = ['Ahmad', 'Muhammad', 'Ali', 'Omar', 'Hassan', 'Ibrahim', 'Yusuf', 'Adam', 'Zayn', 'Farid', 'Aisha', 'Fatimah', 'Maryam', 'Khadijah', 'Nur', 'Siti', 'Amira', 'Zainab'];
    const lastNames = ['Abdullah', 'Rahman', 'Ismail', 'Hussein', 'Kamal', 'Razak', 'Harun', 'Sulaiman', 'Yahya', 'Malik', 'Aziz', 'Hassan', 'Ibrahim'];
    const phones = ['012', '013', '014', '016', '017', '018', '019'];

    const firstName = firstNames[Math.floor(Math.random() * firstNames.length)];
    const lastName = lastNames[Math.floor(Math.random() * lastNames.length)];
    const fullName = firstName + ' ' + lastName;
    const emailBase = firstName.toLowerCase() + '.' + Math.floor(Math.random() * 999);
    const phonePrefix = phones[Math.floor(Math.random() * phones.length)];
    const phoneSuffix = Math.floor(Math.random() * 9000000 + 1000000);

    document.querySelector('input[name="name"]').value = fullName;
    document.querySelector('input[name="email"]').value = emailBase + '@example.com';
    document.querySelector('input[name="phone"]').value = phonePrefix + phoneSuffix;
    document.querySelector('input[name="password"]').value = 'Password123!';
    document.querySelector('input[name="password_confirmation"]').value = 'Password123!';
}
</script>

</html>
