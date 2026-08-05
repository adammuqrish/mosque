<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Smart Mosque System</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/mosque-logo.svg') }}">
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
        @stack('styles')
    </style>
    @stack('head')
</head>

<body class="bg-[#FAFAF5] flex items-center justify-center min-h-screen p-4">

    @hasSection('card')
        @yield('card')
    @else
        <div class="w-full max-w-md bg-white rounded-lg shadow-xl overflow-hidden">
            <div class="bg-emerald-800 p-6 text-center pattern-islamic">
                <h1 class="text-2xl font-bold text-white">
                    <span class="font-islamic text-emerald-200 text-lg mr-2">بِسْمِ ٱللَّهِ</span>@yield('heading')
                </h1>
                <p class="text-emerald-200 text-sm">@yield('subheading')</p>
            </div>

            <div class="p-8">
                @yield('content')
            </div>
        </div>
    @endif

    @stack('scripts')

</body>

</html>
