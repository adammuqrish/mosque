<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email - Smart Mosque System</title>
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
        <div class="bg-emerald-800 p-6 text-center pattern-islamic">
            <h1 class="text-2xl font-bold text-white">
                <span class="font-islamic text-emerald-200 text-lg mr-2">بِسْمِ ٱللَّهِ</span>Verify Email
            </h1>
            <p class="text-emerald-200 text-sm">Sila sahkan alamat e-mel anda.</p>
        </div>

        <div class="p-8">
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 rounded-lg animate-slideIn">
                    <p class="text-green-800">{{ session('success') }}</p>
                </div>
            @endif

            <div class="text-center mb-6">
                <div class="w-16 h-16 mx-auto bg-emerald-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <p class="text-gray-700 mb-2">A verification link has been sent to:</p>
                <p class="font-bold text-emerald-700">{{ Auth::user()->email }}</p>
            </div>

            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6">
                <p class="text-amber-800 text-sm">If you did not receive the email, click the button below to resend.</p>
            </div>

            <form method="POST" action="{{ route('verification.resend') }}" data-loading>
                @csrf
                <input type="hidden" name="email" value="{{ Auth::user()->email }}">

                <button class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 px-6 rounded-lg transition min-h-[48px]" type="submit">
                    Resend Verification Email
                </button>
            </form>

            <div class="mt-6 flex items-center justify-between text-sm">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-gray-500 hover:text-gray-700">Logout</button>
                </form>
                <a href="{{ route('login') }}" class="text-emerald-600 font-bold hover:underline">Back to Login</a>
            </div>
        </div>
    </div>

</body>

</html>
