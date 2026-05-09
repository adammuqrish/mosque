@extends('layouts.app')

@section('title', 'My Volunteering History')

@section('content')

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-emerald-600 p-6">
            <h1 class="text-2xl font-bold text-white">My Volunteer Activities</h1>
            <p class="text-emerald-100 text-sm">Track your contributions and upcoming events.</p>
        </div>

        <div class="p-8">
            @if($myEvents->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($myEvents as $event)
                        <div class="border rounded-lg p-6 shadow-sm hover:shadow-md transition bg-white">

                            <!-- Event Title & Date -->
                            <div class="border-b pb-4 mb-4">
                                <h3 class="text-lg font-bold text-gray-800">{{ $event->title }}</h3>
                                <p class="text-gray-500 text-sm mt-1 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    {{ $event->event_date->format('D, d M Y') }}
                                </p>
                            </div>

                            <!-- Pivot Status (Status dari table event_volunteer) -->
                            <div class="mb-4">
                                <span class="text-xs font-bold uppercase text-gray-500">Status:</span>
                                @php
                                    // Ambil status dari pivot table
                                    $joinStatus = $event->pivot->status; 
                                @endphp

                                @if($joinStatus == 'confirmed')
                                    <span class="ml-2 bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                                        Confirmed
                                    </span>
                                @elseif($joinStatus == 'completed')
                                    <span class="ml-2 bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">
                                        Completed
                                    </span>
                                @elseif($joinStatus == 'absent')
                                    <span class="ml-2 bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full">
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
                            <div class="mb-6">
                                <p class="text-gray-600 text-sm line-clamp-3">
                                    {{ $event->description }}
                                </p>
                            </div>

                            <!-- Joined At Info -->
                            <div class="border-t pt-3 text-xs text-gray-400">
                                You joined on: {{ \Carbon\Carbon::parse($event->pivot->joined_at)->format('d M Y') }}
                            </div>

                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                        </path>
                    </svg>
                    <p class="mt-2 text-lg font-medium">No activities joined yet.</p>
                    <a href="/" class="text-emerald-600 hover:text-emerald-500 font-bold">Browse Events</a>
                </div>
            @endif
        </div>
    </div>

@endsection