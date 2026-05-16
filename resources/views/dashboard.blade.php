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
        <p class="font-semibold text-amber-800 text-sm">{{ __('islamic.events.profile_banner_title') }}</p>
        <p class="text-amber-700 text-xs mt-1">
            {{ __('islamic.events.profile_banner_desc') }}.
        </p>
        <a href="{{ route('profile.index') }}"
            class="inline-block mt-2 text-xs font-bold text-amber-900 underline hover:no-underline">
            {{ __('islamic.events.profile_banner_cta') }} &rarr;
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
         SECTION 3: Open Community Events (always visible)
         ============================================================ --}}
<div class="bg-white rounded-xl shadow-lg overflow-hidden">

    <!-- Header Card -->
    <div class="bg-emerald-700 p-4 sm:p-4 sm:p-6 pattern-islamic">
        <h1 class="text-xl sm:text-2xl font-bold text-white flex items-center gap-2">
            {{-- Mosque/building icon --}}
            <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                </path>
            </svg>
            {{ __('islamic.events.page_title') }}
        </h1>
        <p class="text-emerald-100 text-sm mt-1">{{ __('islamic.events.subtitle') }}</p>
    </div>

    {{-- Flash messages inside this card --}}
    @if(session('success'))
    <div class="mx-6 mt-4 bg-green-50 border-l-4 border-green-500 text-green-800 p-3 rounded text-sm">
        <strong>{{ __('islamic.flash_messages.success') }}</strong> {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="mx-6 mt-4 bg-red-50 border-l-4 border-red-500 text-red-800 p-3 rounded text-sm">
        <strong>{{ __('islamic.flash_messages.error') }}:</strong> {{ session('error') }}
    </div>
    @endif

    <!-- Desktop Table View -->
    <div id="events-table" class="hidden md:block p-4 sm:p-6 overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'title', 'direction' => $sort === 'title' && $direction === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-700">
                            Event Title
                            @if($sort === 'title')
                            <svg class="w-4 h-4 {{ $direction === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                            </svg>
                            @endif
                        </a>
                    </th>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'event_date', 'direction' => $sort === 'event_date' && $direction === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-700">
                            Date
                            @if($sort === 'event_date')
                            <svg class="w-4 h-4 {{ $direction === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                            </svg>
                            @endif
                        </a>
                    </th>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'max_volunteers', 'direction' => $sort === 'max_volunteers' && $direction === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-700">
                            Capacity
                            @if($sort === 'max_volunteers')
                            <svg class="w-4 h-4 {{ $direction === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                            </svg>
                            @endif
                        </a>
                    </th>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($openEvents as $event)
                @php
                $volunteerCount = $event->volunteers()->count();
                $spotsLeft = $event->max_volunteers - $volunteerCount;
                $alreadyJoined = Auth::check() && Auth::user()->events()->where('event_id', $event->id)->exists();
                @endphp
                <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loop->iteration }}</td>
                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-bold text-gray-900">{{ $event->title }}</div>
                        <div class="text-xs text-gray-500 truncate max-w-xs">{{ Str::limit($event->description, 60) }}</div>
                    </td>
                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $event->event_date->format('d M Y - h:i A') }}
                    </td>
                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $event->location ?? $event->event_location ?? '—' }}
                    </td>
                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                        @if($spotsLeft <= 3 && $spotsLeft> 0)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                {{ $spotsLeft }} spot{{ $spotsLeft > 1 ? 's' : '' }} left
                            </span>
                            @elseif($spotsLeft <= 0)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                Full
                                </span>
                                @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-emerald-100 text-emerald-800">
                                    {{ $spotsLeft }} spots
                                </span>
                                @endif
                    </td>
                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm">
                        @if($alreadyJoined)
                        <span class="text-gray-400 text-xs font-medium">✓ Joined</span>
                        @elseif(Auth::check() && Auth::user()->role === 'member')
                        @can('join', $event)
                        <form action="{{ route('volunteer.join', $event->id) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-1.5 px-4 rounded text-xs shadow-sm transition">
                                Join
                            </button>
                        </form>
                        @else
                        <span class="text-gray-400 text-xs">—</span>
                        @endcan
                        @else
                        <span class="text-gray-400 text-xs">Members only</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 sm:px-6 py-8 text-center text-sm text-gray-500">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        {{ __('islamic.events.empty') }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Card View -->
    <div class="md:hidden divide-y divide-gray-200">
        @forelse($openEvents as $event)
        @php
        $volunteerCount = $event->volunteers()->count();
        $spotsLeft = $event->max_volunteers - $volunteerCount;
        $alreadyJoined = Auth::check() && Auth::user()->events()->where('event_id', $event->id)->exists();
        @endphp
        <div class="p-4 hover:bg-gray-50 transition">
            <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="bg-gray-200 text-gray-700 text-xs font-medium px-2 py-0.5 rounded-full">{{ $loop->iteration }}</span>
                        @if($spotsLeft <= 3 && $spotsLeft> 0)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                {{ $spotsLeft }} spot{{ $spotsLeft > 1 ? 's' : '' }} left
                            </span>
                            @elseif($spotsLeft <= 0)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Full</span>
                                @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-emerald-100 text-emerald-800">
                                    {{ $spotsLeft }} spots
                                </span>
                                @endif
                    </div>
                    <h3 class="text-base font-bold text-gray-900">{{ $event->title }}</h3>
                    <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ Str::limit($event->description, 80) }}</p>
                </div>
            </div>
            <div class="mt-3 flex flex-col sm:flex-row sm:items-center gap-2 text-sm text-gray-600">
                <div class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span>{{ $event->event_date->format('d M Y, h:i A') }}</span>
                </div>
                <div class="flex items-center gap-1.5 sm:ml-4">
                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>{{ $event->location ?? $event->event_location ?? '—' }}</span>
                </div>
            </div>

            {{-- Mobile action button --}}
            <div class="mt-3 flex items-center justify-end">
                @if($alreadyJoined)
                <span class="text-gray-400 text-xs font-medium">✓ Joined</span>
                @elseif(Auth::check() && Auth::user()->role === 'member')
                @can('join', $event)
                <form action="{{ route('volunteer.join', $event->id) }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-5 rounded text-sm shadow-sm transition">
                        Join Event
                    </button>
                </form>
                @endcan
                @endif
            </div>
        </div>
        @empty
        <div class="p-8 text-center">
            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <p class="text-gray-500">{{ __('islamic.events.empty') }}</p>
        </div>
        @endforelse
    </div>

    @if($openEvents->hasPages())
    <div id="dashboard-pagination" class="px-4 sm:px-6 py-3 bg-gray-50 border-t border-gray-100 flex justify-center md:hidden">
        {{ $openEvents->appends(request()->except('page'))->links() }}
    </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('page')) {
            const table = document.getElementById('events-table');
            if (table) {
                setTimeout(() => {
                    table.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }, 100);
            }
        }
    });
</script>

@endsection
