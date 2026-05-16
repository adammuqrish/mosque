@extends('layouts.landing')

@section('content')

{{-- ============================================================
     NAVIGATION — Sticky, transparent-to-solid on scroll
     ============================================================ --}}
<nav class="fixed top-0 left-0 right-0 z-50 bg-[#0B6E4F]/95 backdrop-blur-sm shadow-lg pattern-islamic" x-data="{ mobileOpen: false }">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            {{-- Logo --}}
            <a href="#" class="flex items-center gap-3 group">
                <div class="w-9 h-9 bg-white/10 rounded-lg flex items-center justify-center group-hover:bg-white/20 transition">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                    </svg>
                </div>
                <div class="hidden sm:block">
                    <span class="text-white font-bold text-lg">Al-Mukminun</span>
                    <span class="text-emerald-200 text-xs block -mt-1">Mosque Platform</span>
                </div>
            </a>

            {{-- Desktop Nav --}}
            <div class="hidden md:flex items-center gap-6">
                <a href="#how-it-works" class="text-emerald-100 hover:text-white text-sm font-medium transition">How It Works</a>
                <a href="#impact" class="text-emerald-100 hover:text-white text-sm font-medium transition">Impact</a>
                <a href="#categories" class="text-emerald-100 hover:text-white text-sm font-medium transition">Give</a>
                <a href="#events" class="text-emerald-100 hover:text-white text-sm font-medium transition">Events</a>
                <a href="#transparency" class="text-emerald-100 hover:text-white text-sm font-medium transition">Transparency</a>

                @auth
                    <a href="{{ route('dashboard') }}" class="bg-white text-[#0B6E4F] hover:bg-emerald-50 font-semibold text-sm px-5 py-2 rounded-lg transition shadow">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-white hover:text-emerald-100 text-sm font-medium transition">
                        Sign In
                    </a>
                    <a href="{{ route('register') }}" class="bg-white text-[#0B6E4F] hover:bg-emerald-50 font-semibold text-sm px-5 py-2 rounded-lg transition shadow">
                        Get Started
                    </a>
                @endauth
            </div>

            {{-- Mobile hamburger --}}
            <button @click="mobileOpen = !mobileOpen" class="md:hidden text-white p-2 rounded-lg hover:bg-white/10 transition" aria-label="Toggle menu">
                <svg x-show="!mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
                <svg x-show="mobileOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        {{-- Mobile menu --}}
        <div x-show="mobileOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" class="md:hidden pb-4 border-t border-white/10 pt-4" style="display: none;">
            <div class="flex flex-col gap-2">
                <a href="#how-it-works" @click="mobileOpen = false" class="text-emerald-100 hover:text-white hover:bg-white/10 text-sm font-medium px-4 py-2 rounded-lg transition">How It Works</a>
                <a href="#impact" @click="mobileOpen = false" class="text-emerald-100 hover:text-white hover:bg-white/10 text-sm font-medium px-4 py-2 rounded-lg transition">Impact</a>
                <a href="#categories" @click="mobileOpen = false" class="text-emerald-100 hover:text-white hover:bg-white/10 text-sm font-medium px-4 py-2 rounded-lg transition">Give</a>
                <a href="#events" @click="mobileOpen = false" class="text-emerald-100 hover:text-white hover:bg-white/10 text-sm font-medium px-4 py-2 rounded-lg transition">Events</a>
                <a href="#transparency" @click="mobileOpen = false" class="text-emerald-100 hover:text-white hover:bg-white/10 text-sm font-medium px-4 py-2 rounded-lg transition">Transparency</a>
                <div class="border-t border-white/10 my-2"></div>
                @auth
                    <a href="{{ route('dashboard') }}" class="bg-white text-[#0B6E4F] font-semibold text-sm px-4 py-2.5 rounded-lg transition text-center">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="text-white hover:bg-white/10 text-sm font-medium px-4 py-2 rounded-lg transition text-center">Sign In</a>
                    <a href="{{ route('register') }}" class="bg-white text-[#0B6E4F] font-semibold text-sm px-4 py-2.5 rounded-lg transition text-center">Get Started</a>
                @endauth
            </div>
        </div>
    </div>
</nav>

{{-- ============================================================
     SECTION 1: HERO
     ============================================================ --}}
<section class="hero-gradient relative min-h-screen flex items-center pt-16 overflow-hidden">
    {{-- Texture overlay --}}
    <div class="absolute inset-0 hero-texture"></div>

    {{-- Decorative geometric circles --}}
    <div class="absolute top-20 right-10 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
    <div class="absolute bottom-20 left-10 w-96 h-96 bg-[#C5A059]/10 rounded-full blur-3xl"></div>

    <div class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">
            {{-- Left: Content --}}
            <div class="text-center lg:text-left">
                {{-- Badge --}}
                <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm text-emerald-100 text-xs font-medium px-4 py-1.5 rounded-full mb-6 animate-fadeInUp">
                    <span class="w-2 h-2 bg-[#C5A059] rounded-full animate-pulse-soft"></span>
                    Now Serving Al-Mukminun Mosque
                </div>

                {{-- Bismillah --}}
                <p class="font-arabic text-[#C5A059] text-xl sm:text-2xl mb-4 animate-fadeInUp delay-100" dir="rtl">
                    بِسْمِ ٱللَّهِ ٱلرَّحْمَٰنِ ٱلرَّحِيمِ
                </p>

                {{-- Heading --}}
                <h1 class="font-heading text-3xl sm:text-4xl lg:text-5xl xl:text-6xl font-bold text-white leading-tight animate-fadeInUp delay-200">
                    Empowering Our<br>
                    <span class="text-[#C5A059]">Mosque Community</span>
                </h1>

                {{-- Subtitle --}}
                <p class="text-emerald-100 text-base sm:text-lg mt-6 max-w-lg mx-auto lg:mx-0 animate-fadeInUp delay-300">
                    Contribute through Zakat & Sadaqah. Volunteer your time. Track impact with full transparency — all in one platform.
                </p>

                {{-- CTAs --}}
                <div class="flex flex-col sm:flex-row gap-4 mt-8 justify-center lg:justify-start animate-fadeInUp delay-400">
                    @auth
                        <a href="{{ route('dashboard') }}" class="bg-white text-[#0B6E4F] hover:bg-emerald-50 font-semibold px-8 py-3.5 rounded-lg shadow-lg hover:shadow-xl transition flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            Go to Dashboard
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="bg-white text-[#0B6E4F] hover:bg-emerald-50 font-semibold px-8 py-3.5 rounded-lg shadow-lg hover:shadow-xl transition flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Start Contributing
                        </a>
                        <a href="#impact" class="border-2 border-[#C5A059] text-[#C5A059] hover:bg-[#C5A059] hover:text-white font-semibold px-8 py-3.5 rounded-lg transition flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                            Explore Transparency
                        </a>
                    @endauth
                </div>
            </div>

            {{-- Right: Visual / Stats --}}
            <div class="hidden lg:flex justify-center animate-fadeInUp delay-500">
                <div class="relative">
                    {{-- Decorative mosque silhouette card --}}
                    <div class="bg-white/10 backdrop-blur-md rounded-2xl p-8 w-80 border border-white/20">
                        {{-- Mosque icon --}}
                        <div class="w-16 h-16 bg-[#C5A059]/20 rounded-xl flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10 text-[#C5A059]" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                            </svg>
                        </div>

                        {{-- Mini stats --}}
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-emerald-100 text-sm">Zakat</span>
                                <span class="text-[#C5A059] font-bold text-base">RM {{ number_format($zakatTotal ?? 0, 0) }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-emerald-100 text-sm">Zakat Fitr</span>
                                <span class="text-amber-300 font-bold text-base">RM {{ number_format($zakatFitrTotal ?? 0, 0) }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-emerald-100 text-sm">Sadaqah</span>
                                <span class="text-blue-300 font-bold text-base">RM {{ number_format($sadaqahTotal ?? 0, 0) }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-emerald-100 text-sm">Waqf</span>
                                <span class="text-purple-300 font-bold text-base">RM {{ number_format($waqfTotal ?? 0, 0) }}</span>
                            </div>
                            <div class="flex items-center justify-between text-xs pt-2 border-t border-white/10">
                                <span class="text-emerald-200">{{ $donationCount ?? 0 }} donations</span>
                                <span class="text-[#C5A059]">{{ $volunteerCount ?? 0 }} volunteers</span>
                            </div>
                        </div>
                    </div>

                    {{-- Floating badges --}}
                    <div class="absolute -top-4 -right-4 bg-white rounded-xl shadow-xl px-4 py-2 animate-pulse-soft">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-emerald-500 rounded-full"></div>
                            <span class="text-xs font-semibold text-[#1A1A2E]">Live Updates</span>
                        </div>
                    </div>
                    <div class="absolute -bottom-4 -left-4 bg-[#C5A059] rounded-xl shadow-xl px-4 py-2">
                        <span class="text-xs font-bold text-white">Alhamdulillah</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Trust bar --}}
        <div class="mt-16 lg:mt-24 border-t border-white/10 pt-8 animate-fadeInUp delay-500">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-4 text-center">
                <div>
                    <p class="text-xl sm:text-2xl font-bold text-white">{{ $donationCount ?? 0 }}+</p>
                    <p class="text-emerald-200 text-xs sm:text-sm mt-1">Donations</p>
                </div>
                <div>
                    <p class="text-xl sm:text-2xl font-bold text-[#C5A059]">RM {{ number_format($zakatTotal ?? 0, 0) }}</p>
                    <p class="text-emerald-200 text-xs sm:text-sm mt-1">Zakat</p>
                </div>
                <div>
                    <p class="text-xl sm:text-2xl font-bold text-amber-300">RM {{ number_format($zakatFitrTotal ?? 0, 0) }}</p>
                    <p class="text-emerald-200 text-xs sm:text-sm mt-1">Zakat Fitr</p>
                </div>
                <div>
                    <p class="text-xl sm:text-2xl font-bold text-blue-300">RM {{ number_format($sadaqahTotal ?? 0, 0) }}</p>
                    <p class="text-emerald-200 text-xs sm:text-sm mt-1">Sadaqah</p>
                </div>
                <div>
                    <p class="text-xl sm:text-2xl font-bold text-purple-300">RM {{ number_format($waqfTotal ?? 0, 0) }}</p>
                    <p class="text-emerald-200 text-xs sm:text-sm mt-1">Waqf</p>
                </div>
                <div>
                    <p class="text-xl sm:text-2xl font-bold text-[#C5A059]">{{ $eventCount ?? 0 }}+</p>
                    <p class="text-emerald-200 text-xs sm:text-sm mt-1">Events</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Scroll indicator --}}
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 animate-bounce">
        <svg class="w-6 h-6 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
        </svg>
    </div>
</section>

{{-- ============================================================
     SECTION 2: HOW IT WORKS
     ============================================================ --}}
<section id="how-it-works" class="py-20 lg:py-28 bg-white">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Section header --}}
        <div class="text-center max-w-2xl mx-auto mb-16 animate-on-scroll">
            <span class="text-[#C5A059] text-sm font-semibold tracking-wider uppercase">Simple Process</span>
            <h2 class="font-heading text-3xl sm:text-4xl font-bold text-[#1A1A2E] mt-3">How It Works</h2>
            <p class="text-[#64748B] mt-4">Three simple steps to make a meaningful impact in your community.</p>
        </div>

        {{-- Steps --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-12 relative">
            {{-- Step 1 --}}
            <div class="relative text-center animate-on-scroll">
                <div class="relative inline-flex mb-6">
                    <div class="w-16 h-16 bg-[#E8F5E9] rounded-2xl flex items-center justify-center icon-scale">
                        <svg class="w-8 h-8 text-[#0B6E4F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                    </div>
                    <div class="absolute -top-2 -right-2 w-7 h-7 bg-[#0B6E4F] rounded-full flex items-center justify-center text-white text-xs font-bold">1</div>
                </div>
                <h3 class="font-heading text-xl font-bold text-[#1A1A2E] mb-2">Choose</h3>
                <p class="text-[#64748B] text-sm leading-relaxed">Select Zakat, Sadaqah, Infaq, or browse volunteer opportunities that match your skills.</p>
            </div>

            {{-- Step 2 --}}
            <div class="relative text-center animate-on-scroll">
                <div class="relative inline-flex mb-6">
                    <div class="w-16 h-16 bg-[#FDF6E3] rounded-2xl flex items-center justify-center icon-scale">
                        <svg class="w-8 h-8 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="absolute -top-2 -right-2 w-7 h-7 bg-[#C5A059] rounded-full flex items-center justify-center text-white text-xs font-bold">2</div>
                </div>
                <h3 class="font-heading text-xl font-bold text-[#1A1A2E] mb-2">Contribute</h3>
                <p class="text-[#64748B] text-sm leading-relaxed">Contribute through cash or bank transfer — the mosque records every donation with full Shariah compliance.</p>
            </div>

            {{-- Step 3 --}}
            <div class="relative text-center animate-on-scroll">
                <div class="relative inline-flex mb-6">
                    <div class="w-16 h-16 bg-[#E8F5E9] rounded-2xl flex items-center justify-center icon-scale">
                        <svg class="w-8 h-8 text-[#0B6E4F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div class="absolute -top-2 -right-2 w-7 h-7 bg-[#0B6E4F] rounded-full flex items-center justify-center text-white text-xs font-bold">3</div>
                </div>
                <h3 class="font-heading text-xl font-bold text-[#1A1A2E] mb-2">See Impact</h3>
                <p class="text-[#64748B] text-sm leading-relaxed">Track where your contribution goes with full transparency and real-time reports.</p>
            </div>
        </div>
    </div>
</section>

{{-- ============================================================
     SECTION 3: LIVE IMPACT DASHBOARD
     ============================================================ --}}
<section id="impact" class="py-20 lg:py-28 bg-[#FAFAF5]">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Section header --}}
        <div class="text-center max-w-2xl mx-auto mb-12 animate-on-scroll">
            <span class="text-[#0B6E4F] text-sm font-semibold tracking-wider uppercase">Real-Time Transparency</span>
            <h2 class="font-heading text-3xl sm:text-4xl font-bold text-[#1A1A2E] mt-3">Live Impact Dashboard</h2>
            <p class="text-[#64748B] mt-4">Every ringgit accounted for. Updated in real-time and verified by the mosque committee.</p>
        </div>

        {{-- Stats grid --}}
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 sm:gap-6 mb-8">
            {{-- Zakat Fund --}}
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 card-lift animate-on-scroll">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-[#FDF6E3] rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <span class="text-[#64748B] text-xs font-medium">Zakat</span>
                </div>
                <p class="text-2xl sm:text-3xl font-bold text-[#C5A059]">RM {{ number_format($zakatTotal ?? 0, 2) }}</p>
            </div>

            {{-- Zakat Fitr Fund --}}
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 card-lift animate-on-scroll">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-amber-50 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <span class="text-[#64748B] text-xs font-medium">Zakat Fitr</span>
                </div>
                <p class="text-2xl sm:text-3xl font-bold text-amber-600">RM {{ number_format($zakatFitrTotal ?? 0, 2) }}</p>
            </div>

            {{-- Sadaqah Fund --}}
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 card-lift animate-on-scroll">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                    <span class="text-[#64748B] text-xs font-medium">Sadaqah</span>
                </div>
                <p class="text-2xl sm:text-3xl font-bold text-blue-600">RM {{ number_format($sadaqahTotal ?? 0, 2) }}</p>
            </div>

            {{-- Volunteers --}}
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 card-lift animate-on-scroll">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <span class="text-[#64748B] text-xs font-medium">Active Volunteers</span>
                </div>
                <p class="text-2xl sm:text-3xl font-bold text-purple-600">{{ $volunteerCount ?? 0 }}</p>
                <div class="flex items-center gap-1 mt-2">
                    <span class="text-purple-500 text-xs font-medium">{{ $eventCount ?? 0 }} events completed</span>
                </div>
            </div>

            {{-- Waqf Fund --}}
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 card-lift animate-on-scroll">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <span class="text-[#64748B] text-xs font-medium">Waqf Fund</span>
                </div>
                <p class="text-2xl sm:text-3xl font-bold text-purple-600">RM {{ number_format($waqfTotal ?? 0, 2) }}</p>
                <div class="flex items-center gap-1 mt-2">
                    <span class="text-purple-500 text-xs font-medium">Endowment</span>
                </div>
            </div>
        </div>

        {{-- Distribution progress --}}
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 animate-on-scroll">
            <div class="mb-4">
                <h3 class="font-heading font-bold text-[#1A1A2E]">Fund Distribution Progress</h3>
                <p class="text-[#64748B] text-sm mt-1">Tracking how each fund type is collected and distributed</p>
            </div>
            <div class="space-y-4">
                @foreach($distributions as $key => $d)
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm font-semibold text-gray-700">{{ $d['label'] }}</span>
                        <span class="text-xs text-gray-500">RM {{ number_format($d['distributed'], 0) }} / RM {{ number_format($d['collected'], 0) }}</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                        <div class="h-3 rounded-full progress-bar transition-all duration-1000
                            @if($key === 'zakat') bg-[#C5A059]
                            @elseif($key === 'zakat_fitr') bg-amber-500
                            @elseif($key === 'sadaqah') bg-blue-500
                            @else bg-purple-500
                            @endif"
                            style="width: {{ $d['percent'] }}%">
                        </div>
                    </div>
                    <div class="flex justify-between mt-0.5">
                        <span class="text-[10px] text-gray-400">{{ $d['percent'] }}% distributed</span>
                        @if($d['percent'] > 0)
                        <span class="text-[10px] text-gray-400">RM {{ number_format($d['collected'] - $d['distributed'], 0) }} remaining</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- ============================================================
     SECTION 4: DONATION CATEGORIES
     ============================================================ --}}
<section id="categories" class="py-20 lg:py-28 bg-white">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Section header --}}
        <div class="text-center max-w-2xl mx-auto mb-16 animate-on-scroll">
            <span class="text-[#C5A059] text-sm font-semibold tracking-wider uppercase">Ways to Give</span>
            <h2 class="font-heading text-3xl sm:text-4xl font-bold text-[#1A1A2E] mt-3">Donation Categories</h2>
            <p class="text-[#64748B] mt-4">Every contribution is a step toward building a stronger community. Choose how you'd like to give.</p>
        </div>

        {{-- Categories grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            {{-- Zakat --}}
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 card-lift animate-on-scroll group">
                <div class="w-14 h-14 bg-[#FDF6E3] rounded-xl flex items-center justify-center mb-5 icon-scale">
                    <svg class="w-7 h-7 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
                <div class="flex items-center gap-2 mb-2">
                    <h3 class="font-heading text-lg font-bold text-[#1A1A2E]">Zakat</h3>
                    <span class="bg-[#FDF6E3] text-[#C5A059] text-[10px] font-bold px-2 py-0.5 rounded-full">OBLIGATORY</span>
                </div>
                <p class="text-[#64748B] text-sm leading-relaxed">The obligatory annual contribution of 2.5% of surplus wealth — one of the Five Pillars of Islam.</p>
                <a href="{{ auth()->check() ? route('donations.index') : route('register') }}" class="inline-flex items-center gap-1 text-[#0B6E4F] text-sm font-semibold mt-4 group-hover:gap-2 transition-all">
                    Contribute Now
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>

            {{-- Sadaqah --}}
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 card-lift animate-on-scroll group">
                <div class="w-14 h-14 bg-[#E8F5E9] rounded-xl flex items-center justify-center mb-5 icon-scale">
                    <svg class="w-7 h-7 text-[#0B6E4F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </div>
                <div class="flex items-center gap-2 mb-2">
                    <h3 class="font-heading text-lg font-bold text-[#1A1A2E]">Sadaqah</h3>
                    <span class="bg-[#E8F5E9] text-[#0B6E4F] text-[10px] font-bold px-2 py-0.5 rounded-full">VOLUNTARY</span>
                </div>
                <p class="text-[#64748B] text-sm leading-relaxed">Voluntary charity given out of compassion. Every act of kindness is Sadaqah — no amount is too small.</p>
                <a href="{{ auth()->check() ? route('donations.index') : route('register') }}" class="inline-flex items-center gap-1 text-[#0B6E4F] text-sm font-semibold mt-4 group-hover:gap-2 transition-all">
                    Contribute Now
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>

            {{-- Sadaqah Jariyah --}}
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 card-lift animate-on-scroll group">
                <div class="w-14 h-14 bg-blue-50 rounded-xl flex items-center justify-center mb-5 icon-scale">
                    <svg class="w-7 h-7 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </div>
                <div class="flex items-center gap-2 mb-2">
                    <h3 class="font-heading text-lg font-bold text-[#1A1A2E]">Sadaqah Jariyah</h3>
                    <span class="bg-blue-50 text-blue-500 text-[10px] font-bold px-2 py-0.5 rounded-full">ONGOING</span>
                </div>
                <p class="text-[#64748B] text-sm leading-relaxed">Ongoing charity that continues to benefit others — your reward multiplies even after you're gone.</p>
                <a href="{{ auth()->check() ? route('donations.index') : route('register') }}" class="inline-flex items-center gap-1 text-[#0B6E4F] text-sm font-semibold mt-4 group-hover:gap-2 transition-all">
                    Contribute Now
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>

            {{-- Waqf --}}
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 card-lift animate-on-scroll group">
                <div class="w-14 h-14 bg-purple-50 rounded-xl flex items-center justify-center mb-5 icon-scale">
                    <svg class="w-7 h-7 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <div class="flex items-center gap-2 mb-2">
                    <h3 class="font-heading text-lg font-bold text-[#1A1A2E]">Waqf</h3>
                    <span class="bg-purple-50 text-purple-500 text-[10px] font-bold px-2 py-0.5 rounded-full">ENDOWMENT</span>
                </div>
                <p class="text-[#64748B] text-sm leading-relaxed">A permanent endowment that generates ongoing benefits for the community — a legacy of lasting impact.</p>
                <a href="{{ auth()->check() ? route('donations.index') : route('register') }}" class="inline-flex items-center gap-1 text-[#0B6E4F] text-sm font-semibold mt-4 group-hover:gap-2 transition-all">
                    Contribute Now
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>

            {{-- Infaq --}}
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 card-lift animate-on-scroll group">
                <div class="w-14 h-14 bg-amber-50 rounded-xl flex items-center justify-center mb-5 icon-scale">
                    <svg class="w-7 h-7 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0v3m0 0V11"></path>
                    </svg>
                </div>
                <div class="flex items-center gap-2 mb-2">
                    <h3 class="font-heading text-lg font-bold text-[#1A1A2E]">Infaq</h3>
                    <span class="bg-amber-50 text-amber-500 text-[10px] font-bold px-2 py-0.5 rounded-full">SPENDING</span>
                </div>
                <p class="text-[#64748B] text-sm leading-relaxed">Spending in the way of Allah — supporting mosque operations, education, and community programs.</p>
                <a href="{{ auth()->check() ? route('donations.index') : route('register') }}" class="inline-flex items-center gap-1 text-[#0B6E4F] text-sm font-semibold mt-4 group-hover:gap-2 transition-all">
                    Contribute Now
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>

            {{-- Volunteer --}}
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 card-lift animate-on-scroll group">
                <div class="w-14 h-14 bg-[#E8F5E9] rounded-xl flex items-center justify-center mb-5 icon-scale">
                    <svg class="w-7 h-7 text-[#0B6E4F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="flex items-center gap-2 mb-2">
                    <h3 class="font-heading text-lg font-bold text-[#1A1A2E]">Volunteer</h3>
                    <span class="bg-[#E8F5E9] text-[#0B6E4F] text-[10px] font-bold px-2 py-0.5 rounded-full">YOUR TIME</span>
                </div>
                <p class="text-[#64748B] text-sm leading-relaxed">Give your time and skills to the community. Every hour of service is a form of worship.</p>
                <a href="{{ auth()->check() ? route('volunteer.my-events') : route('register') }}" class="inline-flex items-center gap-1 text-[#0B6E4F] text-sm font-semibold mt-4 group-hover:gap-2 transition-all">
                    Join Events
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</section>

{{-- ============================================================
     SECTION 5: UPCOMING EVENTS
     ============================================================ --}}
<section id="events" class="py-20 lg:py-28 bg-[#FAFAF5]">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Section header --}}
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-12 animate-on-scroll">
            <div>
                <span class="text-[#0B6E4F] text-sm font-semibold tracking-wider uppercase">Get Involved</span>
                <h2 class="font-heading text-3xl sm:text-4xl font-bold text-[#1A1A2E] mt-3">Upcoming Events</h2>
                <p class="text-[#64748B] mt-2">Join our community events and earn rewards for your participation.</p>
            </div>
            <a href="{{ auth()->check() ? route('dashboard') : route('register') }}" class="inline-flex items-center gap-1 text-[#0B6E4F] font-semibold text-sm hover:gap-2 transition-all whitespace-nowrap">
                View All Events
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>

        {{-- Events grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($upcomingEvents as $event)
                @php
                    $volunteerCount = $event->volunteers()->count();
                    $spotsLeft = $event->max_volunteers - $volunteerCount;
                    $spotsPercent = $event->max_volunteers > 0 ? ($volunteerCount / $event->max_volunteers) * 100 : 0;
                @endphp
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden card-lift animate-on-scroll">
                    {{-- Date badge --}}
                    <div class="relative h-2 bg-[#0B6E4F]">
                        <div class="absolute top-0 left-0 h-full bg-[#0B6E4F]" style="width: {{ $spotsPercent }}%"></div>
                    </div>
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="bg-[#E8F5E9] rounded-lg px-3 py-2 text-center min-w-[56px]">
                                <p class="text-[#0B6E4F] text-xs font-semibold uppercase">{{ $event->event_date->format('M') }}</p>
                                <p class="text-[#0B6E4F] text-xl font-bold">{{ $event->event_date->format('d') }}</p>
                            </div>
                            @if($spotsLeft <= 3 && $spotsLeft > 0)
                                <span class="bg-red-50 text-red-600 text-xs font-medium px-2.5 py-1 rounded-full">{{ $spotsLeft }} spots left</span>
                            @elseif($spotsLeft <= 0)
                                <span class="bg-gray-100 text-gray-500 text-xs font-medium px-2.5 py-1 rounded-full">Full</span>
                            @else
                                <span class="bg-[#E8F5E9] text-[#0B6E4F] text-xs font-medium px-2.5 py-1 rounded-full">{{ $spotsLeft }} spots</span>
                            @endif
                        </div>
                        <h3 class="font-heading text-lg font-bold text-[#1A1A2E] mb-2">{{ $event->title }}</h3>
                        <p class="text-[#64748B] text-sm line-clamp-2 mb-4">{{ Str::limit($event->description, 80) }}</p>
                        <div class="flex items-center gap-4 text-xs text-[#64748B] mb-4">
                            <div class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $event->event_date->format('h:i A') }}
                            </div>
                            @if($event->location)
                                <div class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    {{ Str::limit($event->location, 20) }}
                                </div>
                            @endif
                        </div>
                        @auth
                            <div x-data="joinEvent({{ $event->id }}, {{ $event->volunteers->contains(auth()->id()) ? 'true' : 'false' }})">
                                <template x-if="joined">
                                    <span class="block w-full text-center bg-[#E8F5E9] text-[#0B6E4F] font-semibold text-sm py-2.5 rounded-lg">Joined</span>
                                </template>
                                <template x-if="!joined && {{ $spotsLeft > 0 ? 'true' : 'false' }}">
                                    <form :action="`{{ route('volunteer.join', $event->id) }}`" method="POST" @submit.prevent="submit">
                                        @csrf
                                        <button type="submit" :disabled="loading" class="w-full bg-[#0B6E4F] hover:bg-[#084B3B] text-white font-semibold text-sm py-2.5 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed" x-text="loading ? 'Joining...' : 'Join Event'">
                                        </button>
                                    </form>
                                </template>
                                <template x-if="!joined && !{{ $spotsLeft > 0 ? 'true' : 'false' }}">
                                    <span class="block w-full text-center bg-gray-100 text-gray-400 font-semibold text-sm py-2.5 rounded-lg">Unavailable</span>
                                </template>
                            </div>
                        @else
                            <a href="{{ route('register') }}" class="block w-full text-center bg-[#0B6E4F] hover:bg-[#084B3B] text-white font-semibold text-sm py-2.5 rounded-lg transition">
                                Sign Up to Join
                            </a>
                        @endauth
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-16 animate-on-scroll">
                    <div class="w-16 h-16 bg-[#E8F5E9] rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-[#0B6E4F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="font-heading text-lg font-bold text-[#1A1A2E] mb-2">No Upcoming Events</h3>
                    <p class="text-[#64748B] text-sm">Check back soon for new community events.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

{{-- ============================================================
     SECTION 6: TESTIMONIALS
     ============================================================ --}}
<section class="py-20 lg:py-28 bg-[#E8F5E9]">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Section header --}}
        <div class="text-center max-w-2xl mx-auto mb-16 animate-on-scroll">
            <span class="text-[#0B6E4F] text-sm font-semibold tracking-wider uppercase">Community Voices</span>
            <h2 class="font-heading text-3xl sm:text-4xl font-bold text-[#1A1A2E] mt-3">What Our Community Says</h2>
        </div>

        {{-- Testimonials grid --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Testimonial 1 --}}
            <div class="bg-white rounded-xl p-6 shadow-sm animate-on-scroll">
                <div class="flex items-center gap-1 mb-4">
                    @for($i = 0; $i < 5; $i++)
                        <svg class="w-4 h-4 text-[#C5A059]" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @endfor
                </div>
                <p class="text-[#64748B] text-sm leading-relaxed mb-6">"This platform has made it so easy to track my Zakat contributions. I can see exactly where my money goes — it gives me peace of mind."</p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-[#0B6E4F] rounded-full flex items-center justify-center text-white font-bold text-sm">AH</div>
                    <div>
                        <p class="font-semibold text-[#1A1A2E] text-sm">Ahmad Hassan</p>
                        <p class="text-[#64748B] text-xs">Community Member</p>
                    </div>
                </div>
            </div>

            {{-- Testimonial 2 --}}
            <div class="bg-white rounded-xl p-6 shadow-sm animate-on-scroll">
                <div class="flex items-center gap-1 mb-4">
                    @for($i = 0; $i < 5; $i++)
                        <svg class="w-4 h-4 text-[#C5A059]" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @endfor
                </div>
                <p class="text-[#64748B] text-sm leading-relaxed mb-6">"Volunteering through this system has connected me with amazing people. The events are well-organized and the rewards keep me motivated."</p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-[#C5A059] rounded-full flex items-center justify-center text-white font-bold text-sm">SF</div>
                    <div>
                        <p class="font-semibold text-[#1A1A2E] text-sm">Siti Fatimah</p>
                        <p class="text-[#64748B] text-xs">Active Volunteer</p>
                    </div>
                </div>
            </div>

            {{-- Testimonial 3 --}}
            <div class="bg-white rounded-xl p-6 shadow-sm animate-on-scroll">
                <div class="flex items-center gap-1 mb-4">
                    @for($i = 0; $i < 5; $i++)
                        <svg class="w-4 h-4 text-[#C5A059]" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @endfor
                </div>
                <p class="text-[#64748B] text-sm leading-relaxed mb-6">"As a mosque committee member, the transparency features are invaluable. We can show our community exactly how funds are managed."</p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-[#0B6E4F] rounded-full flex items-center justify-center text-white font-bold text-sm">MI</div>
                    <div>
                        <p class="font-semibold text-[#1A1A2E] text-sm">Mohamad Isa</p>
                        <p class="text-[#64748B] text-xs">Mosque Committee</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============================================================
     SECTION 7: TRANSPARENCY REPORT PREVIEW
     ============================================================ --}}
<section id="transparency" class="py-20 lg:py-28 bg-white">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">
            {{-- Left: Text --}}
            <div class="animate-on-scroll">
                <span class="text-[#C5A059] text-sm font-semibold tracking-wider uppercase">Full Transparency</span>
                <h2 class="font-heading text-3xl sm:text-4xl font-bold text-[#1A1A2E] mt-3">Every Ringgit,<br>Accounted For</h2>
                <p class="text-[#64748B] mt-4 leading-relaxed">
                    Guided by the Islamic principle of <strong class="text-[#1A1A2E]">Sidq</strong> (truthfulness), our platform provides complete visibility into how donations are collected and distributed.
                </p>
                <p class="text-[#64748B] mt-3 leading-relaxed">
                    Every transaction is recorded, categorized, and made available for the community to review. Zakat funds are tracked separately from voluntary donations to ensure Shariah compliance.
                </p>
                <ul class="mt-6 space-y-3">
                    <li class="flex items-start gap-3">
                        <div class="w-6 h-6 bg-[#E8F5E9] rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-3.5 h-3.5 text-[#0B6E4F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <span class="text-[#64748B] text-sm">Real-time donation tracking and reporting</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-6 h-6 bg-[#E8F5E9] rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-3.5 h-3.5 text-[#0B6E4F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <span class="text-[#64748B] text-sm">Separate Zakat and Sadaqah fund tracking</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-6 h-6 bg-[#E8F5E9] rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-3.5 h-3.5 text-[#0B6E4F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <span class="text-[#64748B] text-sm">Verified by mosque committee members</span>
                    </li>
                </ul>
                <a href="{{ auth()->check() ? route('transparency.index') : route('register') }}" class="inline-flex items-center gap-2 bg-[#0B6E4F] hover:bg-[#084B3B] text-white font-semibold px-6 py-3 rounded-lg shadow-md hover:shadow-lg transition mt-8">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    View Full Report
                </a>
            </div>

            {{-- Right: Preview mockup --}}
            <div class="animate-on-scroll">
                <div class="bg-[#FAFAF5] rounded-2xl p-6 shadow-sm border border-gray-100">
                    <div class="bg-white rounded-xl p-4 shadow-sm mb-4">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="font-heading font-bold text-[#1A1A2E] text-sm">Monthly Summary</h4>
                            <span class="text-[#64748B] text-xs">{{ date('F Y') }}</span>
                        </div>
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between bg-[#FDF6E3] rounded-lg p-2">
                                <span class="text-xs text-[#64748B]">Zakat</span>
                                <span class="text-sm font-bold text-[#C5A059]">RM {{ number_format($zakatTotal ?? 0, 0) }}</span>
                            </div>
                            <div class="flex items-center justify-between bg-amber-50 rounded-lg p-2">
                                <span class="text-xs text-[#64748B]">Zakat Fitr</span>
                                <span class="text-sm font-bold text-amber-600">RM {{ number_format($zakatFitrTotal ?? 0, 0) }}</span>
                            </div>
                            <div class="flex items-center justify-between bg-[#E8F5E9] rounded-lg p-2">
                                <span class="text-xs text-[#64748B]">Sadaqah</span>
                                <span class="text-sm font-bold text-[#0B6E4F]">RM {{ number_format($sadaqahTotal ?? 0, 0) }}</span>
                            </div>
                            <div class="flex items-center justify-between bg-purple-50 rounded-lg p-2">
                                <span class="text-xs text-[#64748B]">Waqf</span>
                                <span class="text-sm font-bold text-purple-600">RM {{ number_format($waqfTotal ?? 0, 0) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl p-4 shadow-sm">
                        <h4 class="font-heading font-bold text-[#1A1A2E] text-sm mb-3">Recent Activity</h4>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-[#E8F5E9] rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-[#0B6E4F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-[#1A1A2E] text-xs font-medium">Sadaqah received</p>
                                        <p class="text-[#64748B] text-[10px]">2 hours ago</p>
                                    </div>
                                </div>
                                <span class="text-[#0B6E4F] text-xs font-bold">+RM 50</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-[#FDF6E3] rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-[#1A1A2E] text-xs font-medium">Zakat distributed</p>
                                        <p class="text-[#64748B] text-[10px]">Yesterday</p>
                                    </div>
                                </div>
                                <span class="text-[#C5A059] text-xs font-bold">-RM 500</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-[#1A1A2E] text-xs font-medium">New volunteer joined</p>
                                        <p class="text-[#64748B] text-[10px]">2 days ago</p>
                                    </div>
                                </div>
                                <span class="text-blue-500 text-xs font-bold">+1</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============================================================
     SECTION 8: FINAL CTA
     ============================================================ --}}
<section class="hero-gradient relative py-20 lg:py-28 overflow-hidden">
    <div class="absolute inset-0 hero-texture"></div>
    <div class="absolute top-10 right-10 w-48 h-48 bg-white/5 rounded-full blur-3xl"></div>
    <div class="absolute bottom-10 left-10 w-64 h-64 bg-[#C5A059]/10 rounded-full blur-3xl"></div>

    <div class="relative max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="animate-on-scroll">
            <p class="font-arabic text-[#C5A059] text-xl mb-4" dir="rtl">
                جَزَاكُمُ ٱللَّهُ خَيْرًا
            </p>
            <h2 class="font-heading text-3xl sm:text-4xl lg:text-5xl font-bold text-white">Join Our Community Today</h2>
            <p class="text-emerald-100 text-lg mt-4 max-w-xl mx-auto">Whether you give RM 1 or your time — every contribution matters. Together, we build a stronger mosque community.</p>

            <div class="flex flex-col sm:flex-row gap-4 mt-8 justify-center">
                @auth
                    <a href="{{ route('dashboard') }}" class="bg-white text-[#0B6E4F] hover:bg-emerald-50 font-semibold px-8 py-3.5 rounded-lg shadow-lg hover:shadow-xl transition">
                        Go to Dashboard
                    </a>
                @else
                    <a href="{{ route('register') }}" class="bg-white text-[#0B6E4F] hover:bg-emerald-50 font-semibold px-8 py-3.5 rounded-lg shadow-lg hover:shadow-xl transition">
                        Create Account
                    </a>
                    <a href="{{ route('login') }}" class="border-2 border-white text-white hover:bg-white hover:text-[#0B6E4F] font-semibold px-8 py-3.5 rounded-lg transition">
                        Browse Events
                    </a>
                @endauth
            </div>
        </div>
    </div>
</section>

{{-- ============================================================
     FOOTER
     ============================================================ --}}
<footer class="bg-[#1A1A2E] text-white py-12 pattern-islamic">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
            {{-- Brand --}}
            <div class="md:col-span-2">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-[#C5A059]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                        </svg>
                    </div>
                    <div>
                        <span class="font-bold text-lg">Al-Mukminun Mosque</span>
                        <span class="text-[#C5A059] text-xs block -mt-1">Smart Platform</span>
                    </div>
                </div>
                <p class="font-arabic text-[#C5A059]/80 text-sm mb-3" dir="rtl">بِسْمِ ٱللَّهِ ٱلرَّحْمَٰنِ ٱلرَّحِيمِ</p>
                <p class="text-gray-400 text-sm leading-relaxed max-w-sm">Empowering our mosque community through transparent donations, meaningful volunteer engagement, and real-time accountability.</p>
            </div>

            {{-- Quick Links --}}
            <div>
                <h4 class="font-semibold text-sm mb-4 text-[#C5A059]">Quick Links</h4>
                <ul class="space-y-2">
                    <li><a href="#how-it-works" class="text-gray-400 hover:text-white text-sm transition">How It Works</a></li>
                    <li><a href="#impact" class="text-gray-400 hover:text-white text-sm transition">Impact Dashboard</a></li>
                    <li><a href="#categories" class="text-gray-400 hover:text-white text-sm transition">Give</a></li>
                    <li><a href="#events" class="text-gray-400 hover:text-white text-sm transition">Events</a></li>
                    <li><a href="#transparency" class="text-gray-400 hover:text-white text-sm transition">Transparency</a></li>
                </ul>
            </div>

            {{-- For Members --}}
            <div>
                <h4 class="font-semibold text-sm mb-4 text-[#C5A059]">For Members</h4>
                <ul class="space-y-2">
                    @auth
                        <li><a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-white text-sm transition">Dashboard</a></li>
                        <li><a href="{{ route('donations.index') }}" class="text-gray-400 hover:text-white text-sm transition">My Donations</a></li>
                        <li><a href="{{ route('volunteer.my-events') }}" class="text-gray-400 hover:text-white text-sm transition">My Events</a></li>
                        <li><a href="{{ route('profile.index') }}" class="text-gray-400 hover:text-white text-sm transition">Profile</a></li>
                    @else
                        <li><a href="{{ route('login') }}" class="text-gray-400 hover:text-white text-sm transition">Sign In</a></li>
                        <li><a href="{{ route('register') }}" class="text-gray-400 hover:text-white text-sm transition">Register</a></li>
                    @endauth
                </ul>
            </div>
        </div>

        {{-- Bottom bar --}}
        <div class="border-t border-white/10 pt-6 flex flex-col sm:flex-row justify-between items-center gap-4">
            <p class="text-gray-500 text-xs">&copy; {{ date('Y') }} Al-Mukminun Mosque. Built with Ihsan.</p>
            <p class="text-gray-500 text-xs font-arabic">جَزَاكُمُ ٱللَّهُ خَيْرًا</p>
        </div>
    </div>
</footer>

@endsection
