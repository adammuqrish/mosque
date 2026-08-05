<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Smart Mosque System')</title>

    <link rel="icon" type="image/svg+xml" href="{{ asset('images/mosque-logo.svg') }}">

    <!-- Google Fonts: Amiri for Islamic-inspired headings -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&display=swap" rel="stylesheet">
    
    <!-- STEP 1: Tailwind CSS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- STEP 2: Application CSS (Design System) -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    
    <!-- STEP 3: Custom animations & scrollbar styling -->
    <style>
        /* Alpine.js cloak to prevent FOUC */
        [x-cloak] { display: none !important; }

        /* STEP 1: Slide-in animation for snackbar */
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        /* STEP 2: Slide-out animation for snackbar */
        @keyframes slideOut {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
        
        /* STEP 3: Shake animation for invalid fields */
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        
        /* STEP 4: Fade animation for modal */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        /* STEP 5: Scale animation for modal content */
        @keyframes scaleIn {
            from { transform: scale(0.95); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
        
        .animate-slideIn { animation: slideIn 0.3s ease-out forwards; }
        .animate-slideOut { animation: slideOut 0.3s ease-in forwards; }
        .animate-shake { animation: shake 0.3s ease-in-out; }
        .animate-fadeIn { animation: fadeIn 0.2s ease-out forwards; }
        .animate-scaleIn { animation: scaleIn 0.2s ease-out forwards; }

        /* Amiri font for headings — Arabic-inspired serif */
        .font-islamic {
            font-family: 'Amiri', serif;
        }
    </style>
</head>

<body class="bg-[#FAFAF5] font-sans antialiased flex flex-col min-h-screen pt-16">

    <!-- NAVBAR (Shared) -->
    <nav class="fixed top-0 left-0 right-0 bg-emerald-800 text-white shadow-lg z-50 pattern-islamic" x-data="{ mobileOpen: false, dropdownOpen: false, finOpen: false, mgmtOpen: false, acctOpen: false }">
        <div class="container mx-auto px-4 py-3">
            <div class="flex justify-between items-center">
                <!-- Left: Logo/Title -->
                <div class="flex items-center gap-3">
                    <a href="/" class="flex items-center gap-2 font-bold text-lg sm:text-xl hover:opacity-90 transition whitespace-nowrap">
                        <img src="{{ asset('images/mosque-logo.svg') }}" alt="Al-Mukminun Mosque Logo"
                            class="w-9 h-9 rounded-lg object-cover ring-2 ring-white/20">
                        <span class="text-emerald-200 text-sm mr-1 font-islamic">بِسْمِ ٱللَّهِ</span>Smart Mosque System
                    </a>
                </div>

                <!-- Desktop Navigation -->
                @auth
                    <div class="hidden lg:flex items-center gap-4">
                        <!-- Navigation Links (Role-based) -->
                        <div class="flex items-center gap-2">
                            @if(Auth::user()->role == 'admin')
                                <a href="{{ route('dashboard') }}" class="text-white hover:text-emerald-200 text-xs border border-transparent hover:border-emerald-400 px-3 py-1.5 rounded transition flex items-center gap-1" title="Dashboard">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h4v-6h4v6h4a1 1 0 001-1V10"></path></svg>
                                    Dashboard
                                </a>
                                <a href="{{ route('donations.index') }}" class="bg-blue-500 hover:bg-blue-600 text-white text-xs px-3 py-1.5 rounded transition shadow-sm flex items-center gap-1" title="Donations">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    {{ __('islamic.donations.nav_label') }}
                                </a>
                                <a href="{{ route('events.manage') }}" class="bg-purple-500 hover:bg-purple-600 text-white text-xs px-3 py-1.5 rounded transition shadow-sm flex items-center gap-1" title="Events">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    {{ __('islamic.events.nav_label') }}
                                </a>
                                {{-- Administration dropdown — consolidates admin-only tools --}}
                                <div class="relative" x-data="{ manageOpen: false }">
                                    <button @click="manageOpen = !manageOpen" class="bg-teal-500 hover:bg-teal-600 text-white text-xs px-3 py-1.5 rounded transition shadow-sm flex items-center gap-1" title="Administration">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        Administration
                                        <svg class="w-3 h-3 transition-transform" :class="manageOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>
                                    <div x-show="manageOpen" @click.away="manageOpen = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-2xl border border-gray-100 z-50 py-1" style="display: none;">
                                        <a href="{{ route('admin.gamification.index') }}" class="flex items-center px-4 py-3 hover:bg-gray-50 transition text-sm text-gray-700 gap-3">
                                            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                                            {{ __('islamic.navigation.gamification') }}
                                        </a>
                                        <a href="{{ route('admin.amils') }}" class="flex items-center px-4 py-3 hover:bg-gray-50 transition text-sm text-gray-700 gap-3">
                                            <svg class="w-5 h-5 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                            Amil Management
                                        </a>
                                        <div class="border-t border-gray-100 my-1"></div>
                                        <a href="{{ route('admin.settings') }}" class="flex items-center px-4 py-3 hover:bg-gray-50 transition text-sm text-gray-700 gap-3">
                                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            Settings
                                        </a>
                                    </div>
                                </div>
                                <div class="w-px h-6 bg-white/30 mx-1"></div>
                                <a href="{{ route('withdrawals.index') }}" class="text-white hover:text-emerald-200 text-xs border border-transparent hover:border-emerald-400 px-3 py-1.5 rounded transition flex items-center gap-1" title="Requests">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                    {{ __('islamic.navigation.requests') }}
                                </a>
                                <a href="{{ route('reports.index') }}" class="text-white hover:text-emerald-200 text-xs border border-transparent hover:border-emerald-400 px-3 py-1.5 rounded transition flex items-center gap-1" title="Reports">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                    {{ __('islamic.navigation.reports') }}
                                </a>
                            @elseif(Auth::user()->role == 'treasurer')
                                <a href="{{ route('dashboard') }}" class="text-white hover:text-emerald-200 text-xs border border-transparent hover:border-emerald-400 px-3 py-1.5 rounded transition flex items-center gap-1" title="Dashboard">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h4v-6h4v6h4a1 1 0 001-1V10"></path></svg>
                                    Dashboard
                                </a>
                                <a href="{{ route('donations.index') }}" class="text-white hover:text-emerald-200 text-xs border border-transparent hover:border-emerald-400 px-3 py-1.5 rounded transition flex items-center gap-1" title="Donations">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    {{ __('islamic.donations.nav_label') }}
                                </a>
                                <a href="{{ route('withdrawals.index') }}" class="relative text-white hover:text-emerald-200 text-xs border border-transparent hover:border-emerald-400 px-3 py-1.5 rounded transition flex items-center gap-1" title="Requests">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                    {{ __('islamic.navigation.requests') }}
                                    @if(isset($pendingCount) && $pendingCount > 0)
                                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold rounded-full h-4 w-4 flex items-center justify-center">{{ $pendingCount > 9 ? '9+' : $pendingCount }}</span>
                                    @endif
                                </a>
                                <a href="{{ route('reports.index') }}" class="text-white hover:text-emerald-200 text-xs border border-transparent hover:border-emerald-400 px-3 py-1.5 rounded transition flex items-center gap-1" title="Reports">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                    {{ __('islamic.navigation.reports') }}
                                </a>
                            @elseif(Auth::user()->role == 'member')
                                <a href="{{ route('dashboard') }}" class="text-white hover:text-emerald-200 text-xs border border-transparent hover:border-emerald-400 px-3 py-1.5 rounded transition flex items-center gap-1" title="Dashboard">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h4v-6h4v6h4a1 1 0 001-1V10"></path></svg>
                                    Dashboard
                                </a>
                                <a href="{{ route('gamification.dashboard') }}" class="text-white hover:text-emerald-200 text-xs border border-transparent hover:border-emerald-400 px-3 py-1.5 rounded transition flex items-center gap-1" title="Gamification">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                                    {{ __('islamic.navigation.gamification') }}
                                </a>
                                <a href="{{ route('gamification.my-redemptions') }}" class="text-white hover:text-emerald-200 text-xs border border-transparent hover:border-emerald-400 px-3 py-1.5 rounded transition flex items-center gap-1" title="My Redemptions">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0l3-3m-3 3l-3-3M5 3v18m14 0V5a2 2 0 00-2-2h-8a2 2 0 00-2 2v14"></path></svg>
                                    My Redemptions
                                </a>
                                <a href="{{ route('volunteer.my-events') }}" class="text-white hover:text-emerald-200 text-xs border border-transparent hover:border-emerald-400 px-3 py-1.5 rounded transition flex items-center gap-1" title="My Events">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    My Events
                                </a>
                                <a href="{{ route('transparency.index') }}" class="text-white hover:text-emerald-200 text-xs border border-transparent hover:border-emerald-400 px-3 py-1.5 rounded transition flex items-center gap-1" title="Transparency">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                    {{ __('islamic.navigation.transparency') }}
                                </a>
                            @endif
                        </div>

                        <!-- User Menu Dropdown -->
                        <div class="relative">
                            @php
                                $unreadNotifications = Auth::user()->unreadNotifications ?? collect();
                                $notificationCount = $unreadNotifications->count();
                            @endphp
                            <button @click="dropdownOpen = !dropdownOpen" class="flex items-center gap-2 text-white hover:text-emerald-200 transition group" aria-label="User menu">
                                @if(Auth::user()->avatar_url)
                                    <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="w-9 h-9 rounded-full object-cover shadow-md group-hover:shadow-lg transition-shadow">
                                @else
                                    <div class="w-9 h-9 bg-emerald-500/80 hover:bg-emerald-400 rounded-full flex items-center justify-center font-semibold shadow-md group-hover:shadow-lg transition-shadow">
                                        {{ Auth::user()->initials }}
                                    </div>
                                @endif
                                <div class="hidden sm:block">
                                    <span class="text-xs font-semibold opacity-90 group-hover:opacity-100 transition">{{ Auth::user()->role }}</span>
                                </div>
                                <svg class="w-3 h-3 transition-transform" :class="dropdownOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="dropdownOpen" @click.away="dropdownOpen = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 mt-2 w-72 bg-white rounded-xl shadow-2xl border border-gray-100 z-50 py-1" style="display: none;">
                                <a href="{{ route('profile.index') }}" class="flex items-center px-4 py-3 hover:bg-gray-50 transition text-sm font-medium text-gray-900 gap-3">
                                    @if(Auth::user()->avatar_url)
                                        <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="w-10 h-10 rounded-full object-cover flex-shrink-0">
                                    @else
                                        <div class="w-10 h-10 bg-emerald-500 rounded-full flex items-center justify-center font-bold text-white flex-shrink-0">
                                            {{ Auth::user()->initials }}
                                        </div>
                                    @endif
                                    <div>
                                        <p>{{ Auth::user()->name }}</p>
                                        <p class="text-xs text-gray-500">{{ ucfirst(Auth::user()->role) }}</p>
                                    </div>
                                </a>
                                <div class="border-t border-gray-100 my-1"></div>
                                <a href="{{ route('notifications.index') }}" class="flex items-center px-4 py-3 hover:bg-gray-50 transition text-sm text-gray-700 gap-3 relative">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                    </svg>
                                    <span>{{ __('islamic.navigation.notifications') }}</span>
                                    @if($notificationCount > 0)
                                        <span class="ml-auto bg-red-500 text-white text-xs px-2 py-0.5 rounded-full font-bold">{{ $notificationCount > 99 ? '99+' : $notificationCount }}</span>
                                    @endif
                                </a>
                                <div class="border-t border-gray-100"></div>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center px-4 py-3 hover:bg-red-50 transition text-sm text-red-700 font-medium gap-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                        </svg>
                                        <span>{{ __('islamic.navigation.logout') }}</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile: Hamburger Menu -->
                    <div class="flex items-center gap-2 lg:hidden">
                        <button @click="mobileOpen = !mobileOpen" class="p-2 text-white hover:text-emerald-200 transition rounded hover:bg-emerald-600" aria-label="Toggle menu" :aria-expanded="mobileOpen">
                            <svg x-show="!mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                            <svg x-show="mobileOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                @endauth
            </div>

            <!-- Mobile Navigation Menu -->
            @auth
                <div x-show="mobileOpen" x-transition class="lg:hidden mt-4 border-t border-emerald-600 pt-4 max-h-[calc(100dvh-5rem)] overflow-y-auto overscroll-contain">
                    <div class="flex items-center gap-3 mb-4 pb-4 border-b border-emerald-600 px-1">
                        @if(Auth::user()->avatar_url)
                            <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="w-10 h-10 rounded-full object-cover">
                        @else
                            <div class="w-10 h-10 bg-emerald-500 rounded-full flex items-center justify-center font-bold text-lg">
                                {{ Auth::user()->initials }}
                            </div>
                        @endif
                        <div>
                            <p class="font-bold">{{ Auth::user()->name }}</p>
                            <span class="text-xs opacity-75">{{ ucfirst(Auth::user()->role) }}</span>
                        </div>
                    </div>

                    <div class="pb-6 space-y-3">
                        @if(in_array(Auth::user()->role, ['admin', 'treasurer']))
                            <div>
                                <button @click="finOpen = !finOpen" class="w-full flex items-center justify-between px-4 py-2.5 text-emerald-200 text-xs font-semibold uppercase tracking-wider hover:text-white transition">
                                    <span>Finance</span>
                                    <svg class="w-4 h-4 transition-transform" :class="finOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <div x-show="finOpen" class="space-y-1 mt-1">
                                    <a href="{{ route('dashboard') }}" class="block px-4 py-3 rounded hover:bg-emerald-600 transition flex items-center gap-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h4v-6h4v6h4a1 1 0 001-1V10"></path></svg>
                                        Dashboard
                                    </a>
                                    <a href="{{ route('donations.index') }}" class="block px-4 py-3 rounded hover:bg-emerald-600 transition flex items-center gap-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        {{ __('islamic.donations.nav_label') }}
                                    </a>
                                    <a href="{{ route('withdrawals.index') }}" class="block px-4 py-3 rounded hover:bg-emerald-600 transition flex items-center gap-3 relative">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                        {{ Auth::user()->role == 'treasurer' ? __('islamic.navigation.approve_requests') : __('islamic.navigation.requests') }}
                                        @if(isset($pendingCount) && $pendingCount > 0)
                                            <span class="ml-auto bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $pendingCount > 9 ? '9+' : $pendingCount }}</span>
                                        @endif
                                    </a>
                                    <a href="{{ route('reports.index') }}" class="block px-4 py-3 rounded hover:bg-emerald-600 transition flex items-center gap-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                        {{ __('islamic.navigation.reports') }}
                                    </a>
                                </div>
                            </div>
                        @endif

                        @if(Auth::user()->role == 'admin')
                            <div>
                                <button @click="mgmtOpen = !mgmtOpen" class="w-full flex items-center justify-between px-4 py-2.5 text-emerald-200 text-xs font-semibold uppercase tracking-wider hover:text-white transition">
                                    <span>Management</span>
                                    <svg class="w-4 h-4 transition-transform" :class="mgmtOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <div x-show="mgmtOpen" class="space-y-1 mt-1">
                                    <a href="{{ route('events.manage') }}" class="block px-4 py-3 rounded hover:bg-emerald-600 transition flex items-center gap-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        {{ __('islamic.events.nav_label') }}
                                    </a>
                                    <a href="{{ route('admin.gamification.index') }}" class="block px-4 py-3 rounded hover:bg-emerald-600 transition flex items-center gap-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                                        {{ __('islamic.navigation.gamification') }}
                                    </a>
                                    <a href="{{ route('gamification.my-redemptions') }}" class="block px-4 py-3 rounded hover:bg-emerald-600 transition flex items-center gap-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0l3-3m-3 3l-3-3M5 3v18m14 0V5a2 2 0 00-2-2h-8a2 2 0 00-2 2v14"></path></svg>
                                        My Redemptions
                                    </a>
                                    <a href="{{ route('admin.amils') }}" class="block px-4 py-3 rounded hover:bg-emerald-600 transition flex items-center gap-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                        Amil Management
                                    </a>
                                    <a href="{{ route('admin.settings') }}" class="block px-4 py-3 rounded hover:bg-emerald-600 transition flex items-center gap-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        Settings
                                    </a>
                                </div>
                            </div>
                        @elseif(Auth::user()->role == 'member')
                            <div>
                                <button @click="mgmtOpen = !mgmtOpen" class="w-full flex items-center justify-between px-4 py-2.5 text-emerald-200 text-xs font-semibold uppercase tracking-wider hover:text-white transition">
                                    <span>Community</span>
                                    <svg class="w-4 h-4 transition-transform" :class="mgmtOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <div x-show="mgmtOpen" class="space-y-1 mt-1">
                                    <a href="{{ route('dashboard') }}" class="block px-4 py-3 rounded hover:bg-emerald-600 transition flex items-center gap-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h4v-6h4v6h4a1 1 0 001-1V10"></path></svg>
                                        Dashboard
                                    </a>
                                    <a href="{{ route('gamification.dashboard') }}" class="block px-4 py-3 rounded hover:bg-emerald-600 transition flex items-center gap-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                                        {{ __('islamic.navigation.gamification') }}
                                    </a>
                                    <a href="{{ route('gamification.my-redemptions') }}" class="block px-4 py-3 rounded hover:bg-emerald-600 transition flex items-center gap-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0l3-3m-3 3l-3-3M5 3v18m14 0V5a2 2 0 00-2-2h-8a2 2 0 00-2 2v14"></path></svg>
                                        My Redemptions
                                    </a>
                                    <a href="{{ route('volunteer.my-events') }}" class="block px-4 py-3 rounded hover:bg-emerald-600 transition flex items-center gap-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        My Events
                                    </a>
                                    <a href="{{ route('transparency.index') }}" class="block px-4 py-3 rounded hover:bg-emerald-600 transition flex items-center gap-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                        {{ __('islamic.navigation.transparency') }}
                                    </a>
                                </div>
                            </div>
                        @endif

                        <div>
                            <button @click="acctOpen = !acctOpen" class="w-full flex items-center justify-between px-4 py-2.5 text-emerald-200 text-xs font-semibold uppercase tracking-wider hover:text-white transition">
                                <span>Account</span>
                                <svg class="w-4 h-4 transition-transform" :class="acctOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div x-show="acctOpen" class="space-y-1 mt-1">
                                <a href="{{ route('notifications.index') }}" class="block px-4 py-3 rounded hover:bg-emerald-600 transition flex items-center gap-3 relative">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                    {{ __('islamic.navigation.notifications') }}
                                    @if(isset($notificationCount) && $notificationCount > 0)
                                        <span class="ml-auto bg-red-500 text-white text-xs px-2 py-0.5 rounded-full font-bold">{{ $notificationCount > 99 ? '99+' : $notificationCount }}</span>
                                    @endif
                                </a>
                                <a href="{{ route('profile.index') }}" class="block px-4 py-3 rounded hover:bg-emerald-600 transition flex items-center gap-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    {{ __('islamic.navigation.profile') }}
                                </a>
                                <form action="{{ route('logout') }}" method="POST" class="pt-2">
                                    @csrf
                                    <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white font-bold px-4 py-3 rounded transition text-left flex items-center gap-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                        {{ __('islamic.navigation.logout') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endauth
        </div>
    </nav>

    <!-- STEP 6: Main Content Area -->
    <main class="flex-grow container mx-auto mt-6 px-4 sm:px-6 lg:px-8 mb-8 max-w-7xl">
        
        <!-- Breadcrumbs -->
        @if(isset($breadcrumbs) && count($breadcrumbs))
            @sectionMissing('breadcrumbs_handled')
                @include('partials.breadcrumbs')
            @endif
        @endif
        
        <!-- STEP 7: Flash Messages (Snackbar style) -->
        <div id="notification-container" class="fixed top-16 left-4 right-4 sm:top-4 sm:left-auto sm:right-4 z-50 space-y-2 max-w-sm">
            @if(session('success'))
                <div class="notification-snackbar bg-green-50 border-l-4 border-green-500 text-green-800 p-4 rounded-lg shadow-lg animate-slideIn flex items-start gap-3" role="alert">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <div class="flex-1">
                        <p class="font-semibold">{{ __('islamic.flash_messages.success') }}</p>
                        <p class="text-sm">{{ session('success') }}</p>
                    </div>
                    <button onclick="dismissNotification(this)" class="text-green-600 hover:text-green-800 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="notification-snackbar bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded-lg shadow-lg animate-slideIn flex items-start gap-3" role="alert">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="flex-1">
                        <p class="font-semibold">{{ __('islamic.flash_messages.error') }}</p>
                        <p class="text-sm">{{ session('error') }}</p>
                    </div>
                    <button onclick="dismissNotification(this)" class="text-red-600 hover:text-red-800 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            @endif

            @if(session('warning'))
                <div class="notification-snackbar bg-yellow-50 border-l-4 border-yellow-500 text-yellow-800 p-4 rounded-lg shadow-lg animate-slideIn flex items-start gap-3" role="alert">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <div class="flex-1">
                        <p class="font-semibold">{{ __('islamic.flash_messages.warning') }}</p>
                        <p class="text-sm">{{ session('warning') }}</p>
                    </div>
                    <button onclick="dismissNotification(this)" class="text-yellow-600 hover:text-yellow-800 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            @endif
        </div>

        @hasSection('back')
        <div class="mb-4">
            <a href="@yield('back')" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 transition font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back
            </a>
        </div>
        @endif
        @yield('content')
    </main>

    <!-- STEP 16: Footer -->
    <footer class="bg-emerald-900 text-white py-6 mt-auto pattern-islamic">
        <div class="container mx-auto px-4 text-center">
            <p class="text-sm text-emerald-200">
                &copy; {{ date('Y') }} {{ __('islamic.footer.copyright') }}. {{ __('islamic.footer.project') }}.
            </p>
        </div>
    </footer>

    <!-- Global Modal Container -->
    <div id="global-modal" class="fixed inset-0 z-50 hidden" aria-hidden="true">
        <!-- STEP 8: Modal backdrop -->
        <div class="fixed inset-0 bg-black bg-opacity-50 animate-fadeIn" onclick="closeModal()"></div>
        <!-- Modal content -->
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div id="modal-content" class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6 animate-scaleIn">
                <div id="modal-body"></div>
            </div>
        </div>
    </div>

    <!-- STEP 9: Notification System JavaScript -->
    <script>
        // Auto-dismiss notifications after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const notifications = document.querySelectorAll('.notification-snackbar');
            notifications.forEach(function(notification) {
                setTimeout(function() {
                    dismissNotification(notification.querySelector('button'));
                }, 5000);
            });
        });

        // STEP 10: Dismiss single notification
        function dismissNotification(button) {
            const notification = button.closest('.notification-snackbar');
            if (notification) {
                notification.classList.remove('animate-slideIn');
                notification.classList.add('animate-slideOut');
                setTimeout(function() {
                    notification.remove();
                }, 300);
            }
        }

        // STEP 11: Show custom notification (for AJAX responses)
        function showNotification(type, title, message) {
            const container = document.getElementById('notification-container');
            const colors = {
                success: { bg: 'bg-green-50', border: 'border-green-500', text: 'text-green-800', icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>' },
                error: { bg: 'bg-red-50', border: 'border-red-500', text: 'text-red-800', icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>' },
                warning: { bg: 'bg-yellow-50', border: 'border-yellow-500', text: 'text-yellow-800', icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>' },
                info: { bg: 'bg-blue-50', border: 'border-blue-500', text: 'text-blue-800', icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>' }
            };
            
            const style = colors[type] || colors.info;
            const html = `
                <div class="notification-snackbar ${style.bg} border-l-4 ${style.border} ${style.text} p-4 rounded-lg shadow-lg animate-slideIn flex items-start gap-3" role="alert">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">${style.icon}</svg>
                    <div class="flex-1">
                        <p class="font-semibold">${title}</p>
                        <p class="text-sm">${message}</p>
                    </div>
                    <button onclick="dismissNotification(this)" class="${style.text.replace('text-', 'hover:text-')} transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', html);
            
            // Auto-dismiss after 5 seconds
            setTimeout(function() {
                const newNotif = container.lastElementChild;
                if (newNotif) {
                    dismissNotification(newNotif.querySelector('button'));
                }
            }, 5000);
        }

        // STEP 12: Show confirmation dialog (for non-form actions)
        function showConfirmDialog(title, message, confirmText, confirmClass, onConfirm) {
            const modal = document.getElementById('global-modal');
            const modalBody = document.getElementById('modal-body');
            modalBody.innerHTML = `
                <h3 class="text-xl font-bold text-gray-800 mb-2">${title}</h3>
                <p class="text-gray-600 mb-6">${message}</p>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition font-medium">Cancel</button>
                    <button type="button" id="confirm-dialog-btn" class="px-4 py-2 ${confirmClass} text-white rounded-lg transition font-medium">${confirmText}</button>
                </div>
            `;
            modal.classList.remove('hidden');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            document.getElementById('confirm-dialog-btn').addEventListener('click', function() {
                closeModal();
                if (onConfirm) onConfirm();
            });
        }

        // STEP 12b: Show confirmation modal (for form-based actions)
        function showConfirmModal(title, message, confirmText, confirmClass, formId, method = 'POST', reasonRequired = false) {
            const modal = document.getElementById('global-modal');
            const modalBody = document.getElementById('modal-body');
            
            // Add method override if not POST
            const methodField = method !== 'POST' ? `<input type="hidden" name="_method" value="${method}">` : '';
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Add reason input field if required
            const reasonField = reasonRequired ? `
                <div class="mb-4">
                    <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-1">Reason for rejection</label>
                    <textarea id="rejection_reason" name="rejection_reason" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="Enter reason..." required></textarea>
                </div>
            ` : '';
            
            modalBody.innerHTML = `
                <h3 class="text-xl font-bold text-gray-800 mb-2">${title}</h3>
                <p class="text-gray-600 mb-4">${message}</p>
                <form action="" id="modal-confirm-form" method="POST">
                    <input type="hidden" name="_token" value="${csrfToken}">
                    ${methodField}
                    ${reasonField}
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition font-medium">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 ${confirmClass} text-white rounded-lg transition font-medium">
                            ${confirmText}
                        </button>
                    </div>
                </form>
            `;
            
            if (formId) {
                const form = document.getElementById('modal-confirm-form');
                form.action = formId;
            }
            
            modal.classList.remove('hidden');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }

        // STEP 13: Close modal
        function closeModal() {
            const modal = document.getElementById('global-modal');
            modal.classList.add('hidden');
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }

        // STEP 14: Highlight invalid fields with shake animation
        function highlightInvalidFields(errors) {
            Object.keys(errors).forEach(function(fieldName) {
                const input = document.querySelector(`[name="${fieldName}"]`);
                if (input) {
                    input.classList.add('border-red-500', 'ring-2', 'ring-red-200', 'animate-shake');
                    setTimeout(function() {
                        input.classList.remove('animate-shake');
                    }, 300);
                }
            });
        }

        // STEP 15: Form submission loading state
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('form[data-loading]');
            forms.forEach(function(form) {
                form.addEventListener('submit', function() {
                    const buttons = form.querySelectorAll('button[type="submit"]');
                    buttons.forEach(function(btn) {
                        btn.disabled = true;
                        btn.classList.add('opacity-75', 'cursor-not-allowed');
                        btn.innerHTML = '<svg class="animate-spin inline w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Processing...';
                    });
                });
            });
        });

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
        </script>
    <script src="{{ asset('js/scroll-position.js') }}"></script>
    </body>
    @yield('scripts')
</html>
