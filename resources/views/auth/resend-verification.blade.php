<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resend Verification - Smart Mosque System</title>
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
                <span class="font-islamic text-emerald-200 text-lg mr-2">بِسْمِ ٱللَّهِ</span>Resend Verification
            </h1>
            <p class="text-emerald-200 text-sm">Hantar semula pautan pengesahan e-mel.</p>
        </div>

        <div class="p-8">
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 rounded-lg animate-slideIn">
                    <p class="text-green-800">{{ session('success') }}</p>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg animate-slideIn">
                    <p class="text-red-800 font-semibold">Error</p>
                    <p class="text-red-700 text-sm">{{ $errors->first() }}</p>
                </div>
            @endif

            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6">
                <p class="text-amber-800 text-sm">Masukkan alamat e-mel anda. Jika akaun anda belum disahkan, kami akan hantar semula pautan pengesahan.</p>
            </div>

            <form method="POST" action="{{ route('verification.resend') }}" data-loading>
                @csrf

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Email Address</label>
                    <input type="email" name="email"
                        class="shadow appearance-none border rounded-lg w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-emerald-500 transition @error('email') border-red-500 ring-2 ring-red-200 @enderror"
                        placeholder="you@example.com" value="{{ old('email') }}" required autofocus>
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 px-6 rounded-lg transition min-h-[48px]" type="submit">
                    Resend Verification Email
                </button>
            </form>

            <div class="mt-6 text-center text-sm">
                <a href="{{ route('login') }}" class="text-emerald-600 font-bold hover:underline">Back to Login</a>
            </div>
        </div>
    </div>

</body>

</html>
