<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Mosque System</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center h-screen p-4">

    <div class="w-full max-w-md bg-white rounded-lg shadow-xl overflow-hidden">

        <!-- Header -->
        <div class="bg-emerald-600 p-6 text-center">
            <h1 class="text-2xl font-bold text-white">Join Community</h1>
            <p class="text-emerald-100 text-sm">Create an account to start volunteering.</p>
        </div>

        <!-- Form -->
        <div class="p-6"> <!-- Kurangkan padding dari p-8 ke p-6 -->
            <form method="POST" action="/register">
                @csrf

                <!-- TAMBAHKAN BLOCK NI UNTUK PAPAR ERROR -->
                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded relative mb-4 text-xs">
                        <p class="font-bold">Oops! Something went wrong:</p>
                        <ul class="list-disc pl-4 mt-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <!-- TAMAT ERROR BLOCK -->

                <!-- Name -->
                <div class="mb-3"> <!-- Kurangkan margin dari mb-4 ke mb-3 -->
                    <label class="block text-gray-700 text-xs font-bold mb-1">Full Name</label>
                    <!-- Label lebih kecil -->
                    <input type="text" name="name" class="w-full border rounded px-2 py-1.5 text-sm" required>
                    <!-- Input lebih padat -->
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <label class="block text-gray-700 text-xs font-bold mb-1">Email Address</label>
                    <input type="email" name="email" class="w-full border rounded px-2 py-1.5 text-sm" required>
                </div>

                <!-- Phone -->
                <div class="mb-3">
                    <label class="block text-gray-700 text-xs font-bold mb-1">Phone Number</label>
                    <input type="tel" name="phone" pattern="[0-9\+\-\s]*"
                        class="w-full border rounded px-2 py-1.5 text-sm" required>
                </div>

                <!-- PASSWORDS BERSEBELAHAN (Jimat Tinggi) -->
                <div class="mb-3 grid grid-cols-1 md:grid-cols-2 gap-2">
                    <div>
                        <label class="block text-gray-700 text-xs font-bold mb-1">Password</label>
                        <input type="password" name="password" class="w-full border rounded px-2 py-1.5 text-sm"
                            required>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-xs font-bold mb-1">Confirm Password</label>
                        <input type="password" name="password_confirmation"
                            class="w-full border rounded px-2 py-1.5 text-sm" required>
                    </div>
                </div>

                <!-- Divider Ringkas -->
                <div class="flex items-center my-3"> <!-- Kurangkan margin -->
                    <div class="flex-grow border-t border-gray-200"></div>
                    <span class="flex-shrink-0 mx-2 text-gray-400 text-xs">Staff Only</span>
                    <div class="flex-grow border-t border-gray-200"></div>
                </div>

                <!-- Special Code -->
                <div class="mb-4">
                    <label class="block text-gray-600 text-xs font-medium mb-1">Special Code (Optional)</label>
                    <input type="text" name="special_code" value="{{ old('special_code') }}"
                        class="w-full border border-dashed border-gray-300 rounded px-2 py-1 text-xs italic {{ $errors->has('special_code') ? 'border-red-500' : '' }}"
                        placeholder="Enter code if you are Staff/Committee">

                    @error('special_code')
                        <p class="text-red-500 text-xs mt-0.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Button -->
                <button
                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded transition text-sm">
                    Register Now
                </button>

            </form>

            <div class="mt-4 text-center text-sm">
                <span class="text-gray-600">Already have an account?</span>
                <a href="/login" class="text-emerald-600 font-bold hover:underline">Login</a>
            </div>
        </div>

        <!-- DEVELOPER TOOLS (JANGAN TAMBAH KAT BAWAH FORM, PASTE SEBELUM </BODY> ONLY) -->
        <div class="fixed bottom-5 right-5 bg-white shadow-2xl border border-gray-300 rounded-lg p-4 z-50 w-64">
            <div class="flex justify-between items-center border-b pb-2 mb-2">
                <span class="font-bold text-gray-700 text-sm">Dev Tools</span>
                <span class="text-[10px] text-gray-400 bg-gray-100 px-1 rounded">Demo</span>
            </div>

            <div class="space-y-2">
                <!-- Option 1: Admin -->
                <button onclick="fillAdmin()"
                    class="w-full bg-red-500 hover:bg-red-600 text-white text-xs font-bold py-2 px-3 rounded transition">
                    Auto-Admin
                </button>

                <!-- Option 2: Treasurer -->
                <button onclick="fillTreasurer()"
                    class="w-full bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-bold py-2 px-3 rounded transition">
                    Auto-Treasurer
                </button>

                <!-- Option 3: Member (Reset) -->
                <button onclick="resetToMember()"
                    class="w-full bg-gray-500 hover:bg-gray-600 text-white text-xs font-bold py-2 px-3 rounded transition">
                    Clear (Member Mode)
                </button>
            </div>

            <p class="text-[10px] text-gray-400 mt-2 text-center">Phone & Email Randomized</p>
        </div>

        <script>
            // Helper: Random Phone
            function getRandomPhone() {
                return '01' + Math.floor(Math.random() * 80000000 + 10000000);
            }

            // Helper: Random Email
            function getRandomEmail(role) {
                const randomNum = Math.floor(Math.random() * 10000);
                return `${role}${randomNum}@mosque.com`;
            }

            // Option 1: Fill Admin
            function fillAdmin() {
                document.querySelector('input[name="name"]').value = 'Admin User';
                document.querySelector('input[name="email"]').value = getRandomEmail('admin');
                document.querySelector('input[name="phone"]').value = getRandomPhone();
                document.querySelector('input[name="password"]').value = 'password';
                document.querySelector('input[name="password_confirmation"]').value = 'password';
            }

            // Option 2: Fill Treasurer
            function fillTreasurer() {
                document.querySelector('input[name="name"]').value = 'Treasurer User';
                document.querySelector('input[name="email"]').value = getRandomEmail('treasurer');
                document.querySelector('input[name="phone"]').value = getRandomPhone();
                document.querySelector('input[name="password"]').value = 'password';
                document.querySelector('input[name="password_confirmation"]').value = 'password';
            }

            // Option 3: Clear (Member Mode)
            function resetToMember() {
                document.querySelector('input[name="name"]').value = '';
                document.querySelector('input[name="email"]').value = '';
                document.querySelector('input[name="phone"]').value = '';
                document.querySelector('input[name="password"]').value = '';
                document.querySelector('input[name="password_confirmation"]').value = '';
            }
        </script>

</body>

</html>