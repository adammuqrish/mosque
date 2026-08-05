@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

{{-- ============================================================
         SECTION 1: Profile completion banner (only for members
         WITHOUT criteria). Dismissible via Alpine.js.
         ============================================================ --}}
@if(Auth::user()->role === 'member' && !$hasCriteria)
<div x-data="{ dismissed: false }" x-show="!dismissed"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 -translate-y-2"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 -translate-y-2"
    class="bg-amber-50 border-l-4 border-amber-400 rounded-lg shadow-sm p-4 mb-6 flex items-start gap-3">
    {{-- Info icon --}}
    <svg class="w-6 h-6 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
    </svg>
    <div class="flex-1">
        <p class="font-semibold text-amber-800 text-sm">Complete Your Profile</p>
        <p class="text-amber-700 text-xs mt-1">
            Set your volunteer preferences to get event recommendations.
        </p>
        <a href="{{ route('profile.index') }}"
            class="inline-block mt-2 text-xs font-bold text-amber-900 underline hover:no-underline">
            Complete Profile &rarr;
        </a>
    </div>
    {{-- Dismiss button --}}
    <button @click="dismissed = true" class="text-amber-400 hover:text-amber-600 transition flex-shrink-0"
        aria-label="Dismiss">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </button>
</div>
@endif

{{-- ============================================================
         SECTION 1.5: Donation Summary (admin & treasurer only)
         ============================================================ --}}
@if(in_array(Auth::user()->role, ['admin', 'treasurer']))
<div class="grid grid-cols-2 sm:grid-cols-6 gap-4 mb-6">
    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-200 border-t-2 border-t-[#C5A059]">
        <p class="text-[10px] font-semibold text-[#C5A059] uppercase tracking-wider">Zakat</p>
        <p class="text-xl font-bold text-gray-800 mt-1">RM {{ number_format($donationStats['zakat'], 0) }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-200 border-t-2 border-t-amber-400">
        <p class="text-[10px] font-semibold text-amber-600 uppercase tracking-wider">Zakat Fitr</p>
        <p class="text-xl font-bold text-gray-800 mt-1">RM {{ number_format($donationStats['zakat_fitr'], 0) }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-200 border-t-2 border-t-blue-500">
        <p class="text-[10px] font-semibold text-blue-600 uppercase tracking-wider">Sadaqah</p>
        <p class="text-xl font-bold text-gray-800 mt-1">RM {{ number_format($donationStats['sadaqah'], 0) }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-200 border-t-2 border-t-purple-500">
        <p class="text-[10px] font-semibold text-purple-600 uppercase tracking-wider">Waqf</p>
        <p class="text-xl font-bold text-gray-800 mt-1">RM {{ number_format($donationStats['waqf'], 0) }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
        <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider">This Month</p>
        <p class="text-xl font-bold text-blue-700 mt-1">RM {{ number_format($donationStats['thisMonth'], 0) }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
        <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Pending</p>
        <p class="text-xl font-bold text-yellow-600 mt-1">{{ $donationStats['pending'] }} entries</p>
        <p class="text-xs text-yellow-500">RM {{ number_format($donationStats['pendingAmount'], 0) }}</p>
    </div>
</div>
@endif

{{-- ============================================================
         SECTION 1.75: My Contributions (members only)
         ============================================================ --}}
@if(Auth::user()->role === 'member')
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
    <div class="px-5 py-3 border-b border-gray-100">
        <h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            My Contributions
        </h2>
    </div>
    <div class="px-5 py-4">
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="text-center">
                <p class="text-2xl font-bold text-gray-900">RM {{ number_format($myDonationStats['total'] ?? 0, 0) }}</p>
                <p class="text-[10px] text-gray-500 uppercase tracking-wider mt-0.5">Total Given</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-bold text-amber-600">{{ $myDonationStats['count'] ?? 0 }}</p>
                <p class="text-[10px] text-gray-500 uppercase tracking-wider mt-0.5">Donations</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-bold text-blue-600">RM {{ number_format($myDonationStats['thisMonth'] ?? 0, 0) }}</p>
                <p class="text-[10px] text-gray-500 uppercase tracking-wider mt-0.5">This Month</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-bold text-purple-600">RM {{ number_format(($myDonationStats['zakat'] ?? 0) + ($myDonationStats['waqf'] ?? 0), 0) }}</p>
                <p class="text-[10px] text-gray-500 uppercase tracking-wider mt-0.5">Zakat &amp; Waqf</p>
            </div>
        </div>
        @if(($myDonationStats['total'] ?? 0) > 0)
        <div class="mt-4 pt-3 border-t border-gray-100">
            <div class="flex items-center gap-3 text-xs text-gray-600">
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-[#C5A059]"></span> Zakat: RM {{ number_format($myDonationStats['zakat'] ?? 0, 0) }}</span>
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-blue-500"></span> Sadaqah: RM {{ number_format($myDonationStats['sadaqah'] ?? 0, 0) }}</span>
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-purple-500"></span> Waqf: RM {{ number_format($myDonationStats['waqf'] ?? 0, 0) }}</span>
            </div>
        </div>
        @endif
    </div>
</div>
@endif

{{-- ============================================================
         SECTION 2: Recommended events (only when user HAS criteria
         and there are matching results).
         ============================================================
 --}}
@if(Auth::user()->role === 'member' && $hasCriteria && $recommendedEvents->isNotEmpty())
<div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl shadow-lg overflow-hidden mb-6">
    <div class="p-6 text-white">
        <h2 class="text-xl font-bold mb-2 flex items-center">
            {{-- Lightning icon --}}
            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
            {{ __('islamic.events.recommended_title') }}
        </h2>
        <p class="text-blue-100 text-sm mb-4">{{ __('islamic.events.recommended_desc') }}:</p>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach($recommendedEvents->take(4) as $rec)
            @php $event = $rec['event']; @endphp
            <div class="bg-white bg-opacity-20 rounded-lg p-4 backdrop-blur-sm flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="text-left">
                    <h3 class="font-bold text-lg text-black w-full">{{ $event->title }}</h3>
                    <p class="text-xs text-black mt-1">{{ $event->event_date->format('d M Y, h:i A') }}</p>
                    {{-- Match reasons --}}
                    @if(!empty($rec['reasons']))
                    <div class="flex flex-wrap gap-1 mt-1">
                        @foreach($rec['reasons'] as $reason)
                        <span class="text-[10px] bg-blue-700 bg-opacity-40 text-white px-2 py-0.5 rounded-full capitalize">{{ $reason }}</span>
                        @endforeach
                    </div>
                    @endif
                </div>
                @can('join', $event)
                <form action="{{ route('volunteer.join', $event->id) }}" method="POST" class="flex-shrink-0">
                    @csrf
                    <button type="submit"
                        class="bg-white text-blue-600 hover:bg-gray-100 font-bold py-2 px-5 rounded text-sm shadow-sm transition whitespace-nowrap">
                        Join
                    </button>
                </form>
                @else
                <span class="text-xs bg-white bg-opacity-30 text-white px-3 py-1 rounded-full font-medium whitespace-nowrap">
                    {{ $event->isFull() ? 'Full' : ($event->isPast() ? 'Passed' : 'Unavailable') }}
                </span>
                @endcan
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- ============================================================
         SECTION 3: My Upcoming Events (members only)
         ============================================================ --}}
@if(Auth::user()->role === 'member' && $myEvents->isNotEmpty())
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
    <div class="bg-emerald-600 px-5 py-3 flex items-center justify-between">
        <h2 class="text-base font-bold text-white flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            My Upcoming Events
        </h2>
        <a href="{{ route('volunteer.my-events') }}" class="text-xs text-emerald-200 hover:text-white transition font-medium">
            View All &rarr;
        </a>
    </div>
    <div class="divide-y divide-gray-100">
        @foreach($myEvents->take(5) as $event)
        <div class="px-5 py-3 flex items-center justify-between gap-4 hover:bg-gray-50 transition">
            <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold text-gray-900 truncate">{{ $event->title }}</p>
                <p class="text-xs text-gray-500 mt-0.5">
                    {{ $event->event_date->format('d M Y, h:i A') }}
                    &middot; {{ $event->location ?? $event->event_location ?? '—' }}
                </p>
            </div>
            <form action="{{ route('volunteer.leave', $event->id) }}" method="POST" class="flex-shrink-0" id="leave-form-{{ $event->id }}">
                @csrf
                @method('DELETE')
                <button type="button" onclick="showConfirmDialog('Leave Event', 'Leave this event?', 'Yes, Leave', 'bg-red-500 hover:bg-red-600', function(){ document.getElementById('leave-form-{{ $event->id }}').submit(); })" class="text-xs text-red-500 hover:text-red-700 font-medium transition">Leave</button>
            </form>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- ============================================================
         SECTION 4: Open Community Events (always visible)
         ============================================================ --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="bg-emerald-50 px-5 py-3 border-b border-gray-200 flex items-center justify-between">
        <h2 class="text-base font-bold text-emerald-800 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
            Open Events
        </h2>
        <span class="text-xs text-gray-500">{{ $openEvents->count() }} of {{ $totalOpenCount }} available</span>
    </div>

    <div class="divide-y divide-gray-100">
        @forelse($openEvents as $event)
        @php
        $volunteerCount = $event->volunteers()->count();
        $spotsLeft = $event->max_volunteers - $volunteerCount;
        $alreadyJoined = Auth::check() && Auth::user()->events()->where('event_id', $event->id)->exists();
        @endphp
        <div class="px-5 py-3 flex flex-col sm:flex-row sm:items-center gap-3 hover:bg-gray-50 transition">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1">
                    <h3 class="text-sm font-semibold text-gray-900 truncate">{{ $event->title }}</h3>
                    @if($spotsLeft <= 3 && $spotsLeft> 0)
                        <span class="text-[10px] font-semibold bg-red-100 text-red-700 px-1.5 py-0.5 rounded-full flex-shrink-0">{{ $spotsLeft }} left</span>
                        @elseif($spotsLeft <= 0)
                            <span class="text-[10px] font-semibold bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded-full flex-shrink-0">Full</span>
                            @else
                            <span class="text-[10px] font-semibold bg-emerald-100 text-emerald-700 px-1.5 py-0.5 rounded-full flex-shrink-0">{{ $spotsLeft }} spots</span>
                            @endif
                </div>
                <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-gray-500">
                    <span>{{ $event->event_date->format('d M Y, h:i A') }}</span>
                    <span>{{ $event->location ?? $event->event_location ?? '—' }}</span>
                </div>
            </div>
            <div class="flex-shrink-0">
                @if($alreadyJoined)
                <span class="text-xs text-gray-400 font-medium">✓ Joined</span>
                @elseif(Auth::check() && Auth::user()->role === 'member')
                @can('join', $event)
                <form action="{{ route('volunteer.join', $event->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-1.5 px-4 rounded text-xs shadow-sm transition">
                        Join
                    </button>
                </form>
                @else
                <span class="text-xs text-gray-400">—</span>
                @endcan
                @else
                <span class="text-xs text-gray-400">Members only</span>
                @endif
            </div>
        </div>
        @empty
        <div class="px-5 py-8 text-center">
            <svg class="w-10 h-10 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <p class="text-sm text-gray-500">{{ __('islamic.events.empty') }}</p>
        </div>
        @endforelse
    </div>

    @if($totalOpenCount > $openEvents->count() && Auth::user()->role === 'admin')
    <div class="px-5 py-3 bg-gray-50 border-t border-gray-100 text-center">
        <a href="{{ route('events.manage') }}" class="text-sm text-emerald-600 hover:text-emerald-800 font-medium transition">
            View all {{ $totalOpenCount }} events &rarr;
        </a>
    </div>
    @endif
</div>

@endsection