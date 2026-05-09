<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Dynamic Title: Setiap page boleh tukar tajuk sendiri -->
    <title>@yield('title', 'Mosque System')</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-gray-100 font-sans antialiased flex flex-col min-h-screen">

    <!-- NAVBAR (Shared) -->
    <nav class="bg-emerald-700 text-white shadow-lg z-10">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">

            <!-- Kiri: Title & User Info -->
            <div class="flex items-center space-x-4">
                <a href="/" class="font-bold text-xl hover:opacity-90 transition">Mosque System</a>
                <div class="hidden md:block h-6 w-px bg-emerald-500"></div>

                <!-- Info User (Hanya nampak kalau login) -->
                @auth
                    <div class="text-sm hidden sm:flex items-center">
                        <span class="opacity-75 mr-2">Hi,</span>
                        <a href="{{ route('profile.index') }}"
                            class="font-semibold mr-2 hover:text-emerald-300 decoration-2">
                            {{ Auth::user()->name }}
                        </a>
                        <!-- Role Badge -->
                        <span
                            class="px-2 py-0.5 text-[10px] font-bold rounded bg-emerald-500 border border-emerald-400 uppercase tracking-wide">
                            {{ Auth::user()->role }}
                        </span>
                    </div>
                @endauth
            </div>

            <!-- Kanan: Links & Logout -->
            <div class="flex items-center space-x-3">
                @auth
                    <!-- Admin Links -->
                    @if(Auth::user()->role == 'admin')
                        <a href="{{ route('donations.index') }}"
                            class="bg-blue-500 hover:bg-blue-600 text-white text-xs px-3 py-1.5 rounded transition shadow-sm">
                            Manage Donations
                        </a>
                        <a href="{{ route('events.manage') }}"
                            class="bg-purple-500 hover:bg-purple-600 text-white text-xs px-3 py-1.5 rounded transition shadow-sm">
                            Manage Events
                        </a>
                        <a href="{{ route('withdrawals.index') }}"
                            class="text-white hover:text-emerald-200 text-xs ml-1 border border-transparent hover:border-emerald-400 px-2 py-1.5 rounded transition">
                            My Requests
                        </a>
                        <a href="{{ route('reports.index') }}"
                            class="text-white hover:text-emerald-200 text-xs ml-1 border border-transparent hover:border-emerald-400 px-2 py-1.5 rounded transition">
                            Financial Reports
                        </a>
                    @endif

                    <!-- Treasurer Links -->
                    @if(Auth::user()->role == 'treasurer')
                        <a href="{{ route('withdrawals.index') }}"
                            class="bg-yellow-500 hover:bg-yellow-600 text-white text-xs px-3 py-1.5 rounded transition shadow-sm">
                            Approve Requests
                        </a>
                    @endif

                    <!-- Member Links -->
                    @if(Auth::user()->role == 'member')
                        <a href="{{ route('volunteer.my-events') }}"
                            class="text-white hover:text-emerald-200 text-xs px-3 py-1.5 rounded transition border border-transparent hover:border-emerald-400">
                            My Events
                        </a>
                        <a href="{{ route('transparency.index') }}"
                            class="text-white hover:text-emerald-200 text-xs px-3 py-1.5 rounded transition border border-transparent hover:border-emerald-400">
                            Transparency
                        </a>
                    @endif

                    <!-- Logout Button -->
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                            class="bg-red-500 hover:bg-red-600 text-white text-xs font-bold px-3 py-1.5 rounded shadow-sm transition">
                            Logout
                        </button>
                    </form>
                @endauth
            </div>

        </div>
    </nav>

    <!-- MAIN CONTENT AREA (Bahagian ini berubah ikut page) -->
    <main class="flex-grow container mx-auto mt-6 px-4 sm:px-8 mb-8">
        <!-- FLASH MESSAGES (Shared - Akan muncul dalam semua page) -->
        <div class="container mx-auto my-4">
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm relative"
                    role="alert">
                    <p class="font-bold">Success!</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm relative" role="alert">
                    <p class="font-bold">Error!</p>
                    <p>{{ session('error') }}</p>
                </div>
            @endif
        </div>
        @yield('content')
    </main>

    <!-- FOOTER (Shared) -->
    <footer class="bg-emerald-800 text-white py-6 mt-auto">
        <div class="container mx-auto px-4 text-center">
            <p class="text-sm opacity-80">
                &copy; {{ date('Y') }} Smart Mosque System. Developed for Final Year Project.
            </p>
        </div>
    </footer>

</body>

</html>