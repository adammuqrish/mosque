@extends('layouts.app')

{{-- Back goes to previous page via layout default --}}

@section('title', 'My Volunteer Activities')

@php
    $breadcrumbs = [
        ['label' => 'My Events'],
    ];
@endphp

@section('content')

<!-- STEP 1: Page Header -->
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
        <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
            </path>
        </svg>
        My Volunteer Activities
    </h1>
    <p class="text-gray-600 mt-1">Track your contributions to the community and upcoming events.</p>
</div>

<!-- STEP 2: Stats Summary Card -->
<div class="bg-gradient-to-r from-emerald-600 to-emerald-800 rounded-xl shadow-sm p-4 sm:p-6 mb-6 pattern-islamic">
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div>
            <h2 class="text-white font-semibold text-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Your Volunteering Stats
            </h2>
        </div>
        <div class="flex items-center gap-4 sm:p-6 text-white">
            <div class="text-center">
                <p class="text-2xl font-bold">{{ $stats['total'] }}</p>
                <p class="text-xs opacity-80">Total</p>
            </div>
            <div class="h-10 w-px bg-white/30"></div>
            <div class="text-center">
                <p class="text-2xl font-bold text-white">{{ $stats['confirmed'] }}</p>
                <p class="text-xs opacity-80">Confirmed</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-bold text-white">{{ $stats['completed'] }}</p>
                <p class="text-xs opacity-80">Completed</p>
            </div>
            <div class="text-center relative" x-data="{ showTip: false }">
                <p class="text-2xl font-bold text-white">{{ $stats['absent'] }}</p>
                <p class="text-xs opacity-80 flex items-center justify-center gap-1">
                    Absent
                    <span @mouseenter="showTip = true" @mouseleave="showTip = false" class="relative cursor-help">
                        <svg class="w-3 h-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span x-show="showTip" x-transition.opacity.duration.100ms class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-3 py-1.5 bg-gray-900 text-white text-xs rounded-lg whitespace-nowrap shadow-lg z-10" @click.away="showTip = false">
                            Registered but did not attend
                        </span>
                    </span>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- STEP 3: Events Card -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <!-- Card Header with Tabs and Sort -->
    <div class="px-4 sm:px-6 py-4 border-b border-gray-100 bg-gray-50/50">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                Joined Events
            </h2>
            
            <!-- Sort Toggle -->
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-500">Sort:</span>
                <a href="{{ request()->fullUrlWithQuery(['sort' => 'newest', 'filter' => $filter]) }}" 
                   class="px-3 py-1 text-sm rounded-lg transition {{ $sort === 'newest' ? 'bg-emerald-100 text-emerald-700 font-medium' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Newest First
                </a>
                <a href="{{ request()->fullUrlWithQuery(['sort' => 'oldest', 'filter' => $filter]) }}" 
                   class="px-3 py-1 text-sm rounded-lg transition {{ $sort === 'oldest' ? 'bg-emerald-100 text-emerald-700 font-medium' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Oldest First
                </a>
            </div>
        </div>
        
        <!-- Filter Tabs -->
        <div class="flex items-center gap-2 mt-4">
            <a href="{{ request()->fullUrlWithQuery(['filter' => 'all', 'sort' => $sort]) }}" 
               class="px-4 py-2 text-sm rounded-lg transition flex items-center gap-2 {{ $filter === 'all' ? 'bg-emerald-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' }}">
                All <span class="bg-gray-200/50 px-2 py-0.5 rounded text-xs">{{ $stats['total'] }}</span>
            </a>
            <a href="{{ request()->fullUrlWithQuery(['filter' => 'upcoming', 'sort' => $sort]) }}" 
               class="px-4 py-2 text-sm rounded-lg transition flex items-center gap-2 {{ $filter === 'upcoming' ? 'bg-emerald-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Upcoming <span class="bg-gray-200/50 px-2 py-0.5 rounded text-xs">{{ $stats['confirmed'] }}</span>
            </a>
            <a href="{{ request()->fullUrlWithQuery(['filter' => 'past', 'sort' => $sort]) }}" 
               class="px-4 py-2 text-sm rounded-lg transition flex items-center gap-2 {{ $filter === 'past' ? 'bg-emerald-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Past <span class="bg-gray-200/50 px-2 py-0.5 rounded text-xs">{{ $stats['completed'] + $stats['absent'] }}</span>
            </a>
        </div>
    </div>

    <div class="p-6">
        @if($myEvents->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:p-6">
                @foreach($myEvents as $event)
                    <!-- STEP 4: Event Card -->
                    <div class="border border-gray-200 rounded-xl p-4 sm:p-6 hover:shadow-md transition bg-white">

                        <!-- Event Title & Date -->
                        <div class="border-b pb-4 mb-4">
                            <h3 class="text-lg font-bold text-gray-800">{{ $event->title }}</h3>
                            <p class="text-gray-500 text-sm mt-1 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                {{ $event->event_date->format('D, d M Y') }}
                                @if($event->status == 'cancelled')
                                    <span class="ml-2 text-xs text-red-600 font-semibold">(Cancelled)</span>
                                @elseif($event->isPast())
                                    <span class="ml-2 text-xs text-gray-400">(Past)</span>
                                @else
                                    <span class="ml-2 text-xs text-emerald-600">(Upcoming)</span>
                                @endif
                            </p>
                        </div>

                        @if($event->status == 'cancelled')
                            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                                <div class="flex items-center gap-2 text-red-700">
                                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                    <span class="text-sm font-semibold">This event has been cancelled.</span>
                                </div>
                            </div>
                        @endif

                        <!-- Pivot Status (Status dari table event_volunteer) -->
                        <div class="mb-4">
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</span>
                            @php
                                $joinStatus = $event->pivot->attendance_status; 
                            @endphp

                            @if($joinStatus == 'confirmed')
                                <span
                                    class="ml-2 inline-flex items-center gap-1 bg-blue-100 text-blue-800 text-xs px-2.5 py-0.5 rounded-full font-medium">
                                    Confirmed
                                </span>
                            @elseif($joinStatus == 'completed')
                                <span
                                    class="ml-2 inline-flex items-center gap-1 bg-green-100 text-green-800 text-xs px-2.5 py-0.5 rounded-full font-medium">
                                    Completed
                                </span>
                            @elseif($joinStatus == 'absent')
                                <span
                                    class="ml-2 inline-flex items-center gap-1 bg-red-100 text-red-800 text-xs px-2.5 py-0.5 rounded-full font-medium">
                                    Absent
                                </span>
                            @endif
                        </div>

                        <!-- Location -->
                        <div class="mb-6">
                            <p class="text-gray-600 text-sm">
                                <span class="font-semibold">Location:</span> {{ $event->location }}
                            </p>
                        </div>

                        <!-- Description -->
                        <div class="mb-6" x-data="{ expanded: false }">
                            <p class="text-gray-600 text-sm" :class="expanded ? '' : 'line-clamp-3'">
                                {{ $event->description }}
                            </p>
                            <button @click="expanded = !expanded" class="text-emerald-600 hover:text-emerald-700 text-xs font-medium mt-1 focus:outline-none" x-show="$el.previousElementSibling.scrollHeight > $el.previousElementSibling.clientHeight || expanded">
                                <span x-text="expanded ? 'Show less' : 'Read more'"></span>
                            </button>
                        </div>

                        <!-- Joined At Info -->
                        <div class="border-t pt-3 text-xs text-gray-400">
                            You joined on: {{ \Carbon\Carbon::parse($event->pivot->joined_at)->format('d M Y') }}
                        </div>

                        <!-- Leave Button (only for upcoming confirmed events, not for cancelled) -->
                        @if($joinStatus == 'confirmed' && !$event->isPast() && $event->status != 'cancelled')
                            <div class="mt-4">
                                <button type="button" 
                                    data-action="{{ route('volunteer.leave', $event->id) }}" 
                                    data-method="DELETE"
                                    data-title="Leave Event" 
                                    data-message="Are you sure you want to leave this event?" 
                                    data-btn-text="Leave" 
                                    data-btn-class="bg-red-600 hover:bg-red-700"
                                    onclick="showConfirmModal(this.dataset.title, this.dataset.message, this.dataset.btnText, this.dataset.btnClass, this.dataset.action, this.dataset.method)"
                                    class="w-full bg-red-50 hover:bg-red-100 text-red-700 font-medium py-2 px-4 rounded-lg transition flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                    Leave Event
                                </button>
                            </div>
                        @endif

                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                    </path>
                </svg>
                <p class="text-gray-500 font-medium">No activities found.</p>
                @if($filter !== 'all')
                    <a href="{{ request()->fullUrlWithQuery(['filter' => 'all']) }}" class="text-emerald-600 hover:text-emerald-500 font-semibold mt-2 inline-block">View All Events</a>
                @else
                    <a href="/" class="text-emerald-600 hover:text-emerald-500 font-semibold mt-2 inline-block">Browse Events</a>
                @endif
            </div>
        @endif
    </div>
</div>

@endsection

