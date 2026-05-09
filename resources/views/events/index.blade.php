@extends('layouts.app')

@section('title', 'Manage Events')

@section('content')

    <!-- SECTION 1: FORM CREATE EVENT -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8 border-l-4 border-purple-500">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Create New Event</h2>

        <form action="/events" method="POST">
            @csrf

            <!-- Validation Errors -->
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700">
                    <p class="font-bold">Please fix the following errors:</p>
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Title -->
                <div class="md:col-span-2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Event Title</label>
                    <input type="text" name="title" class="w-full border rounded px-3 py-2"
                        placeholder="e.g. Qurban Program" required>
                </div>

                <!-- Date -->
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Date & Time</label>
                    <input type="datetime-local" name="event_date" class="w-full border rounded px-3 py-2" required>
                </div>

                <!-- Max Volunteers -->
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Max Volunteers</label>
                    <input type="number" name="max_volunteers" class="w-full border rounded px-3 py-2" value="10" required>
                </div>

                <!-- Location -->
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Location</label>
                    <input type="text" name="location" class="w-full border rounded px-3 py-2" placeholder="e.g. Main Hall"
                        required>
                </div>

                <!-- Required Skills -->
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Required Skills</label>
                    <p class="text-xs text-gray-500 mb-1">Separate with comma (e.g., Cooking, Cleaning)</p>
                    <input type="text" name="required_skills" class="w-full border rounded px-3 py-2"
                        placeholder="Cooking, Serving" required>
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                    <textarea name="description" rows="3" class="w-full border rounded px-3 py-2"
                        placeholder="Event details..." required></textarea>
                </div>

                <!-- Required Hobbies -->
                <div class="md:col-span-2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Required Hobbies</label>
                    <p class="text-xs text-gray-500 mb-1">Separate with comma</p>
                    <input type="text" name="required_hobbies" class="w-full border rounded px-3 py-2"
                        placeholder="e.g. Reading, Gardening">
                </div>

                <!-- Required Languages -->
                <div class="md:col-span-2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Required Languages</label>
                    <p class="text-xs text-gray-500 mb-1">Separate with comma</p>
                    <input type="text" name="required_languages" class="w-full border rounded px-3 py-2"
                        placeholder="e.g. Malay, English">
                </div>

                <!-- Event Location (Khusus) -->
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Event Location</label>
                    <input type="text" name="event_location" class="w-full border rounded px-3 py-2"
                        placeholder="e.g. Melaka" required>
                </div>

                <!-- Health Requirement -->
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Physical Requirement</label>
                    <select name="health_requirement" class="w-full border rounded px-3 py-2">
                        <option value="Any">Any Health Status</option>
                        <option value="Fit">Must be Physically Fit</option>
                        <option value="Light">Light Tasks Only</option>
                    </select>
                </div>
            </div>

            <div class="mt-6">
                <button type="submit"
                    class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-6 rounded shadow">
                    Create Event
                </button>
            </div>
        </form>
    </div>

    <!-- SECTION 2: LIST EXISTING EVENTS -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-800">All Events</h2>
        </div>
        <div class="p-6 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Skills Needed</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Volunteers</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($events as $event)
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                {{ $event->event_date->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3 text-sm font-bold text-gray-900">
                                {{ $event->title }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                @if(is_array($event->required_skills))
                                    {{ implode(', ', $event->required_skills) }}
                                @else
                                    {{ $event->required_skills }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                {{ $event->volunteers()->count() }} / {{ $event->max_volunteers }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <form action="{{ route('events.destroy', $event->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 font-bold"
                                        onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-gray-500">No events found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection