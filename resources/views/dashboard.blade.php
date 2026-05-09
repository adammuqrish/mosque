@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    <!--SMART RECOMMENDATION (Hanya Member) -->
    @if(Auth::user()->role == 'member' && $recommendedEvents->count() > 0)
        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl shadow-lg overflow-hidden mb-6">
            <div class="p-6 text-white">
                <h2 class="text-xl font-bold mb-2 flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z">
                        </path>
                    </svg>
                    Recommended For You
                </h2>
                <p class="text-blue-100 text-sm mb-4">Based on your skills, you might be interested in these events:</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($recommendedEvents->take(4) as $recEvent)
                        <div
                            class="bg-white bg-opacity-20 rounded-lg p-4 backdrop-blur-sm flex flex-col sm:flex-row justify-between items-center gap-4">
                            <div class="text-left">
                                <h3 class="font-bold text-lg text-black w-full">{{ $recEvent->title }}</h3>
                                <p class="text-xs text-black mt-1">{{ $recEvent->event_date->format('d M Y') }}</p>
                            </div>
                            <form action="{{ route('volunteer.join', $recEvent->id) }}" method="POST" class="flex-shrink-0">
                                @csrf
                                <button type="submit"
                                    class="bg-white text-blue-600 hover:bg-gray-100 font-bold py-1 px-4 rounded text-sm shadow-sm transition whitespace-nowrap">
                                    Join Now
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-lg overflow-hidden">

        <!-- Header Card -->
        <div class="bg-emerald-600 p-6">
            <h1 class="text-2xl font-bold text-white flex items-center">
                <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                    </path>
                </svg>
                Upcoming Events
            </h1>
        </div>

        <!-- Table Content -->
        <div class="p-6 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Event Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Max Volunteers</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($events as $event)
                        <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">{{ $event->title }}</div>
                                <div class="text-xs text-gray-500 truncate max-w-xs">{{ $event->description }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $event->event_date->format('d M Y - h:i A') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $event->location }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-emerald-100 text-emerald-800">
                                    {{ $event->max_volunteers }} pax
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No events found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection