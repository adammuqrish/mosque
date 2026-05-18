@extends('layouts.app')

@section('back', '/dashboard')

@section('title', __('islamic.events.nav_label'))

@section('content')

    <!-- SECTION 1: FORM CREATE / EDIT EVENT -->
    <div class="bg-white rounded-lg shadow-md p-4 sm:p-6 mb-8 border-l-4 {{ isset($event) ? 'border-[#C5A059]' : 'border-purple-500' }}">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-800">
                @if(isset($event))
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Event
                    </span>
                @else
                    Create New Event
                @endif
            </h2>
            
            @if(isset($event))
                <a href="{{ route('events.manage') }}" class="text-gray-500 hover:text-gray-700 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </a>
            @endif
        </div>

        @php
            $formAction = isset($event) ? route('events.update', $event->id) : route('events.store');
            $formMethod = isset($event) ? 'PUT' : 'POST';
            $submitText = isset($event) ? 'Update Event' : 'Create Event';
        @endphp

        <form action="{{ $formAction }}" method="POST" data-loading>
            @csrf
            @if(isset($event))
                @method('PUT')
            @endif

            <!-- STEP 1: Inline Validation Errors -->
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <p class="font-semibold text-red-800">Please fix the following errors:</p>
                            <ul class="list-disc list-inside text-sm text-red-700 mt-1 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:p-6">
                <!-- Title -->
                <div class="md:col-span-2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Event Title</label>
                    <input type="text" name="title" 
                        class="w-full border rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-purple-500 focus:outline-none transition @error('title') border-red-500 ring-2 ring-red-200 @enderror"
                        placeholder="e.g., Qurban Program" value="{{ isset($event) ? $event->title : old('title') }}" required>
                    @error('title')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Date -->
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Date & Time</label>
                    <input type="datetime-local" name="event_date" 
                        class="w-full border rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-purple-500 focus:outline-none transition @error('event_date') border-red-500 ring-2 ring-red-200 @enderror"
                        value="{{ isset($event) ? $event->event_date->format('Y-m-d\TH:i') : old('event_date') }}" required>
                    @error('event_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- End Time -->
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">End Date & Time</label>
                    <input type="datetime-local" name="end_time" 
                        class="w-full border rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-purple-500 focus:outline-none transition @error('end_time') border-red-500 ring-2 ring-red-200 @enderror"
                        value="{{ isset($event) && $event->end_time ? $event->end_time->format('Y-m-d\TH:i') : old('end_time') }}" required>
                    @error('end_time')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Max Volunteers -->
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Max Volunteers</label>
                    <input type="number" name="max_volunteers" 
                        class="w-full border rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-purple-500 focus:outline-none transition @error('max_volunteers') border-red-500 ring-2 ring-red-200 @enderror"
                        value="{{ isset($event) ? $event->max_volunteers : old('max_volunteers', 10) }}" required>
                    @if(isset($event))
                        <p class="text-xs text-gray-500 mt-1">Current: {{ $event->volunteerCount }} volunteers</p>
                    @endif
                    @error('max_volunteers')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Gamification Category -->
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Event Category</label>
                    <select name="gamification_category" 
                        class="w-full border rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-purple-500 focus:outline-none transition @error('gamification_category') border-red-500 ring-2 ring-red-200 @enderror"
                        required>
                        <option value="" {{ old('gamification_category') == '' ? 'selected' : '' }} disabled>Select a category</option>
                        <option value="religious" {{ (isset($event) && $event->gamification_category == 'religious') || old('gamification_category') == 'religious' ? 'selected' : '' }}>Religious</option>
                        <option value="charity" {{ (isset($event) && $event->gamification_category == 'charity') || old('gamification_category') == 'charity' ? 'selected' : '' }}>Charity</option>
                        <option value="education" {{ (isset($event) && $event->gamification_category == 'education') || old('gamification_category') == 'education' ? 'selected' : '' }}>Education</option>
                        <option value="community" {{ (isset($event) && $event->gamification_category == 'community') || old('gamification_category') == 'community' ? 'selected' : '' }}>Community</option>
                        <option value="youth" {{ (isset($event) && $event->gamification_category == 'youth') || old('gamification_category') == 'youth' ? 'selected' : '' }}>Youth</option>
                        <option value="elderly" {{ (isset($event) && $event->gamification_category == 'elderly') || old('gamification_category') == 'elderly' ? 'selected' : '' }}>Elderly</option>
                        <option value="maintenance" {{ (isset($event) && $event->gamification_category == 'maintenance') || old('gamification_category') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    </select>
                    @error('gamification_category')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Location -->
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Location</label>
                    <input type="text" name="location" 
                        class="w-full border rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-purple-500 focus:outline-none transition @error('location') border-red-500 ring-2 ring-red-200 @enderror"
                        placeholder="e.g., Main Hall" value="{{ isset($event) ? $event->location : old('location') }}" required>
                    @error('location')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Required Skills -->
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Required Skills</label>
                    <input type="text" name="required_skills" 
                        class="w-full border rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-purple-500 focus:outline-none transition @error('required_skills') border-red-500 ring-2 ring-red-200 @enderror"
                        placeholder="Cooking, Serving" value="{{ isset($event) ? (is_array($event->required_skills) ? implode(', ', $event->required_skills) : $event->required_skills) : old('required_skills') }}" required>
                    <p class="text-xs text-gray-500 mt-1">Separate with comma</p>
                    @error('required_skills')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                    <textarea name="description" rows="3" 
                        class="w-full border rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-purple-500 focus:outline-none transition resize-none @error('description') border-red-500 ring-2 ring-red-200 @enderror"
                        placeholder="Event details..." required>{{ isset($event) ? $event->description : old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Required Hobbies -->
                <div class="md:col-span-2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Required Hobbies <span class="text-gray-400 font-normal">(Optional)</span></label>
                    <input type="text" name="required_hobbies" 
                        class="w-full border rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-purple-500 focus:outline-none transition"
                        placeholder="e.g., Reading, Gardening" value="{{ isset($event) ? (is_array($event->required_hobbies) ? implode(', ', $event->required_hobbies) : $event->required_hobbies) : old('required_hobbies') }}">
                </div>

                <!-- Required Languages -->
                <div class="md:col-span-2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Required Languages <span class="text-gray-400 font-normal">(Optional)</span></label>
                    <input type="text" name="required_languages" 
                        class="w-full border rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-purple-500 focus:outline-none transition"
                        placeholder="e.g., Malay, English" value="{{ isset($event) ? (is_array($event->required_languages) ? implode(', ', $event->required_languages) : $event->required_languages) : old('required_languages') }}">
                </div>

                <!-- Event Location -->
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Event Location</label>
                    <input type="text" name="event_location" 
                        class="w-full border rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-purple-500 focus:outline-none transition @error('event_location') border-red-500 ring-2 ring-red-200 @enderror"
                        placeholder="e.g., Melaka" value="{{ isset($event) ? $event->event_location : old('event_location') }}" required>
                    @error('event_location')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Health Requirement -->
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Physical Requirement</label>
                    <select name="health_requirement" class="w-full border rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-purple-500 focus:outline-none transition">
                        <option value="Any" {{ (isset($event) && $event->health_requirement == 'Any') ? 'selected' : '' }}>Any Health Status</option>
                        <option value="Fit" {{ (isset($event) && $event->health_requirement == 'Fit') ? 'selected' : '' }}>Must be Physically Fit</option>
                        <option value="Light" {{ (isset($event) && $event->health_requirement == 'Light') ? 'selected' : '' }}>Light Tasks Only</option>
                    </select>
                </div>
            </div>

            <div class="mt-6 flex flex-col sm:flex-row gap-3">
                <button type="button" onclick="autoFillEvent()"
                    class="w-full sm:w-auto bg-blue-500 hover:bg-blue-600 text-white font-bold py-2.5 px-4 rounded-lg shadow transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Auto Fill
                </button>
                <button type="submit"
                    class="w-full sm:w-auto bg-purple-600 hover:bg-purple-700 text-white font-bold py-2.5 px-4 sm:px-6 rounded-lg shadow transition flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    {{ $submitText }}
                </button>
                
                @if(isset($event))
                    <a href="{{ route('events.manage') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2.5 px-4 sm:px-6 rounded-lg transition">
                        Cancel
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- STEP 2: List Existing Events -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Card Header -->
        <div class="px-4 sm:px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
            <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                All Events
            </h2>
            <span class="bg-gray-200 text-gray-700 text-xs px-2.5 py-0.5 rounded-full font-medium">{{ $events->count() }} Events</span>
        </div>

        <div id="events-table" class="hidden lg:block p-4 sm:p-6 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                <a href="{{ request()->fullUrlWithQuery(['sort_event' => 'status', 'direction_event' => $sortEvent === 'status' && $directionEvent === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-700">
                                    Status
                                    @if($sortEvent === 'status')
                                        <svg class="w-4 h-4 {{ $directionEvent === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                    @endif
                                </a>
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                <a href="{{ request()->fullUrlWithQuery(['sort_event' => 'event_date', 'direction_event' => $sortEvent === 'event_date' && $directionEvent === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-700">
                                    Date
                                    @if($sortEvent === 'event_date')
                                        <svg class="w-4 h-4 {{ $directionEvent === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                    @endif
                                </a>
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                <a href="{{ request()->fullUrlWithQuery(['sort_event' => 'title', 'direction_event' => $sortEvent === 'title' && $directionEvent === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-700">
                                    Title
                                    @if($sortEvent === 'title')
                                        <svg class="w-4 h-4 {{ $directionEvent === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                    @endif
                                </a>
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                <a href="{{ request()->fullUrlWithQuery(['sort_event' => 'volunteers_count', 'direction_event' => $sortEvent === 'volunteers_count' && $directionEvent === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-700">
                                    Volunteers
                                    @if($sortEvent === 'volunteers_count')
                                        <svg class="w-4 h-4 {{ $directionEvent === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                    @endif
                                </a>
                            </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($events as $evt)
                        @php
                            $volunteerCount = $evt->volunteers_count;
                            $percentage = min(100, ($volunteerCount / max(1, $evt->max_volunteers)) * 100);
                            $isFull = $volunteerCount >= $evt->max_volunteers;
                            $isPast = $evt->isPast();
                        @endphp
                        <tr class="hover:bg-gray-50 transition {{ isset($event) && $event->id == $evt->id ? 'bg-yellow-50' : '' }}">
                            <!-- Status Badge -->
                            <td class="px-4 py-3">
                                @if($evt->effective_status === 'open')
                                    @if($isFull)
                                        <span class="inline-flex items-center gap-1 bg-red-100 text-red-800 text-xs px-2.5 py-1 rounded-full font-medium">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                            Full
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 bg-green-100 text-green-800 text-xs px-2.5 py-1 rounded-full font-medium">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            Open
                                        </span>
                                    @endif
                                @elseif($evt->effective_status === 'closed')
                                    <span class="inline-flex items-center gap-1 bg-gray-100 text-gray-800 text-xs px-2.5 py-1 rounded-full font-medium">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                        Closed
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 bg-red-100 text-red-800 text-xs px-2.5 py-1 rounded-full font-medium">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        Cancelled
                                    </span>
                                @endif
                                @if($isPast && $evt->effective_status !== 'cancelled')
                                    <span class="inline-flex items-center gap-1 bg-gray-200 text-gray-600 text-xs px-2 py-0.5 rounded ml-1">Past</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">{{ $evt->event_date ? $evt->event_date->format('d M Y') : 'No date' }}</td>
                            <td class="px-4 py-3">
                                <span class="text-sm font-bold text-gray-900">{{ $evt->title }}</span>
                                @if(is_array($evt->required_skills) && count($evt->required_skills) > 0)
                                    <div class="flex flex-wrap gap-1 mt-1">
                                        @foreach(array_slice($evt->required_skills, 0, 2) as $skill)
                                            <span class="bg-purple-100 text-purple-800 text-xs px-1.5 py-0.5 rounded">{{ $skill }}</span>
                                        @endforeach
                                        @if(count($evt->required_skills) > 2)
                                            <span class="text-gray-400 text-xs">+{{ count($evt->required_skills) - 2 }}</span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <div class="flex items-center gap-2">
                                    <div class="w-20 bg-gray-200 rounded-full h-2">
                                        <div class="{{ $isFull ? 'bg-red-500' : 'bg-emerald-500' }} h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <span class="text-xs {{ $isFull ? 'text-red-600 font-medium' : 'text-gray-600' }}">{{ $volunteerCount }}/{{ $evt->max_volunteers }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <div class="flex items-center gap-2">
                                    @if(!$isPast)
                                        <a href="{{ route('events.edit', $evt->id) }}" class="text-blue-600 hover:text-blue-900 font-medium transition text-xs">Edit</a>
                                        <span class="text-gray-300">|</span>
                                        <div class="relative" x-data="{ open: false }">
                                            <button type="button" @click="open = !open" class="text-gray-600 hover:text-gray-900 font-medium transition text-xs">Status</button>
                                            <div x-show="open" @click.away="open = false" class="absolute left-0 mt-1 w-32 bg-white rounded-lg shadow-lg border z-10" style="display: none;">
                                                <form action="{{ route('events.changeStatus', $evt->id) }}" method="POST" class="p-1">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="status" value="open">
                                                    <button type="submit" class="w-full text-left px-3 py-2 text-xs hover:bg-green-50 text-green-700 rounded {{ $evt->effective_status === 'open' ? 'bg-green-100' : '' }}">Open</button>
                                                </form>
                                                <form action="{{ route('events.changeStatus', $evt->id) }}" method="POST" class="p-1">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="status" value="closed">
                                                    <button type="submit" class="w-full text-left px-3 py-2 text-xs hover:bg-gray-50 text-gray-700 rounded {{ $evt->effective_status === 'closed' ? 'bg-gray-100' : '' }}">Close</button>
                                                </form>
                                                <form action="{{ route('events.changeStatus', $evt->id) }}" method="POST" class="p-1">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="status" value="cancelled">
                                                    <button type="submit" class="w-full text-left px-3 py-2 text-xs hover:bg-red-50 text-red-700 rounded {{ $evt->effective_status === 'cancelled' ? 'bg-red-100' : '' }}">Cancel</button>
                                                </form>
                                            </div>
                                        </div>
                                        <span class="text-gray-300">|</span>
                                    @endif

                                    <a href="{{ route('events.volunteers', $evt->id) }}" class="text-purple-600 hover:text-purple-900 font-medium transition text-xs">{{ $volunteerCount }} volunteers</a>

                                    @if($isPast)
                                        <span class="text-gray-300">|</span>
                                        <span class="text-gray-400 text-xs">Past Event</span>
                                    @else
                                        <!-- <span class="text-gray-300">|</span>
                                        <button type="button" data-action="{{ route('events.destroy', $evt->id) }}" data-method="DELETE" data-title="Delete Event" data-message="Are you sure?" data-btn-text="Delete" data-btn-class="bg-red-600 hover:bg-red-700" onclick="showConfirmModal(this.dataset.title, this.dataset.message, this.dataset.btnText, this.dataset.btnClass, this.dataset.action, this.dataset.method)" class="text-red-600 hover:text-red-900 font-medium transition text-xs">Delete</button> -->
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-8 text-gray-500">No events found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($events->hasPages())
            <div id="pagination" class="px-4 sm:px-6 py-3 bg-gray-50 border-t border-gray-100">
                {{ $events->appends(request()->except('page'))->links() }}
            </div>
        @endif

        <!-- Mobile Card View -->
        <div class="lg:hidden divide-y divide-gray-200">
            @forelse($events as $evt)
                @php
                    $volunteerCount = $evt->volunteers_count;
                    $percentage = min(100, ($volunteerCount / max(1, $evt->max_volunteers)) * 100);
                    $isFull = $volunteerCount >= $evt->max_volunteers;
                    $isPast = $evt->isPast();
                @endphp
                <div class="p-4 {{ isset($event) && $event->id == $evt->id ? 'bg-yellow-50' : '' }}">
                    <!-- Header Row -->
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1 min-w-0">
                            <!-- Status Badges -->
                            <div class="flex flex-wrap items-center gap-1 mb-2">
                                @if($evt->effective_status === 'open')
                                    @if($isFull)
                                        <span class="inline-flex items-center gap-1 bg-red-100 text-red-800 text-xs px-2 py-0.5 rounded-full font-medium">Full</span>
                                    @else
                                        <span class="inline-flex items-center gap-1 bg-green-100 text-green-800 text-xs px-2 py-0.5 rounded-full font-medium">Open</span>
                                    @endif
                                @elseif($evt->effective_status === 'closed')
                                    <span class="inline-flex items-center gap-1 bg-gray-100 text-gray-800 text-xs px-2 py-0.5 rounded-full font-medium">Closed</span>
                                @else
                                    <span class="inline-flex items-center gap-1 bg-red-100 text-red-800 text-xs px-2 py-0.5 rounded-full font-medium">Cancelled</span>
                                @endif
                                @if($isPast && $evt->effective_status !== 'cancelled')
                                    <span class="bg-gray-200 text-gray-600 text-xs px-2 py-0.5 rounded-full">Past</span>
                                @endif
                            </div>
                            <h3 class="text-base font-bold text-gray-900">{{ $evt->title }}</h3>
                        </div>
                    </div>

                    <!-- Info Row -->
                    <div class="flex flex-col sm:flex-row sm:items-center gap-2 text-sm text-gray-600 mb-3">
                        <div class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span>{{ $evt->event_date ? $evt->event_date->format('d M Y, h:i A') : 'No date' }}</span>
                        </div>
                        <div class="flex items-center gap-1.5 sm:ml-3">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span>{{ $evt->location }}</span>
                        </div>
                    </div>

                    <!-- Volunteers Progress -->
                    <div class="flex items-center gap-3 mb-4">
                        <div class="flex-1 bg-gray-200 rounded-full h-2">
                            <div class="{{ $isFull ? 'bg-red-500' : 'bg-emerald-500' }} h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                        <span class="text-xs font-medium {{ $isFull ? 'text-red-600' : 'text-gray-600' }}">{{ $volunteerCount }}/{{ $evt->max_volunteers }} volunteers</span>
                    </div>

                    <!-- Action Buttons (Touch-friendly: min 44px height) -->
                    @if(!$isPast)
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('events.edit', $evt->id) }}" class="flex-1 min-w-[44px] min-h-[44px] flex items-center justify-center gap-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 font-medium text-sm px-3 py-2 rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                Edit
                            </a>
                            <a href="{{ route('events.volunteers', $evt->id) }}" class="flex-1 min-w-[44px] min-h-[44px] flex items-center justify-center gap-1.5 bg-purple-100 hover:bg-purple-200 text-purple-700 font-medium text-sm px-3 py-2 rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                {{ $volunteerCount }}
                            </a>
                            <button type="button" data-action="{{ route('events.destroy', $evt->id) }}" data-method="DELETE" data-title="Delete Event" data-message="Are you sure?" data-btn-text="Delete" data-btn-class="bg-red-600 hover:bg-red-700" onclick="showConfirmModal(this.dataset.title, this.dataset.message, this.dataset.btnText, this.dataset.btnClass, this.dataset.action, this.dataset.method)" class="hidden sm:flex flex-1 min-w-[44px] min-h-[44px] items-center justify-center gap-1.5 bg-red-100 hover:bg-red-200 text-red-700 font-medium text-sm px-3 py-2 rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                Delete
                            </button>
                        </div>
                    @else
                        <div class="text-center py-2 px-4 bg-gray-100 rounded-lg text-gray-500 text-sm">This event has passed</div>
                    @endif
                </div>
            @empty
                <div class="p-8 text-center">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <p class="text-gray-500">No events found. Create your first event above!</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Status Menu Toggle Script -->
    <script>
        function toggleStatusMenu(eventId) {
            // Close all other menus first
            document.querySelectorAll('[id^="status-menu-"]').forEach(menu => {
                if (menu.id !== 'status-menu-' + eventId) {
                    menu.classList.add('hidden');
                }
            });
            
            // Toggle current menu
            const menu = document.getElementById('status-menu-' + eventId);
            if (menu) {
                menu.classList.toggle('hidden');
            }
        }

        // Close menus when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.matches('[onclick^="toggleStatusMenu"]') && !e.target.closest('[id^="status-menu-"]')) {
                document.querySelectorAll('[id^="status-menu-"]').forEach(menu => {
                    menu.classList.add('hidden');
                });
            }
        });
    </script>

    <script>
        function autoFillEvent() {
            const titles = ['Qurban Program', 'Charity Drive', 'Food Distribution', 'Community Cleanup', 'Health Fair', 'Eid Celebration', 'Religious Talk', 'Youth Camp', 'Elderly Visit', 'Fundraising Gala'];
            const categories = ['religious', 'charity', 'education', 'community', 'youth', 'elderly', 'maintenance'];
            const locations = ['Mosque Hall', 'Community Center', 'City Park', 'Town Hall', 'Islamic Center', 'Youth Hall', 'Sports Complex', 'Library'];
            const description = 'This event aims to bring together community members for a meaningful activity. All volunteers are welcome to participate and contribute. Please join us for a rewarding experience.';
            const skills = ['Communication', 'Teamwork', 'Cooking', 'Driving', 'First Aid', 'Teaching'];
            const hobbies = ['Reading', 'Gardening', 'Sports', 'Music', 'Art', 'Cooking'];
            const languages = ['English', 'Malay', 'Arabic', 'Mandarin', 'Tamil'];
            
            // Fill in form fields
            document.querySelector('input[name="title"]').value = titles[Math.floor(Math.random() * titles.length)];
            document.querySelector('input[name="event_date"]').value = getRandomFutureDate();
            document.querySelector('input[name="end_time"]').value = getRandomFutureEndDate();
            document.querySelector('input[name="location"]').value = locations[Math.floor(Math.random() * locations.length)];
            document.querySelector('input[name="event_location"]').value = locations[Math.floor(Math.random() * locations.length)];
            document.querySelector('textarea[name="description"]').value = description;
            document.querySelector('input[name="max_volunteers"]').value = Math.floor(Math.random() * 20) + 1;
            
            // Fill gamification category select
            const categorySelect = document.querySelector('select[name="gamification_category"]');
            if (categorySelect) {
                const randomCategory = categories[Math.floor(Math.random() * categories.length)];
                for (let i = 0; i < categorySelect.options.length; i++) {
                    if (categorySelect.options[i].value === randomCategory) {
                        categorySelect.selectedIndex = i;
                        break;
                    }
                }
            }
            
            // Fill optional fields
            const skillsInput = document.querySelector('input[name="required_skills"]');
            if (skillsInput) {
                const selectedSkills = skills.sort(() => 0.5 - Math.random()).slice(0, Math.floor(Math.random() * 3) + 1);
                skillsInput.value = selectedSkills.join(', ');
            }
            
            const hobbiesInput = document.querySelector('input[name="required_hobbies"]');
            if (hobbiesInput) {
                const selectedHobbies = hobbies.sort(() => 0.5 - Math.random()).slice(0, Math.floor(Math.random() * 2) + 1);
                hobbiesInput.value = selectedHobbies.join(', ');
            }
            
            const languagesInput = document.querySelector('input[name="required_languages"]');
            if (languagesInput) {
                const selectedLanguages = languages.sort(() => 0.5 - Math.random()).slice(0, Math.floor(Math.random() * 2) + 1);
                languagesInput.value = selectedLanguages.join(', ');
            }
            
            // Set health requirement select
            const healthSelect = document.querySelector('select[name="health_requirement"]');
            if (healthSelect) {
                const options = ['Any', 'Fit', 'Light'];
                healthSelect.value = options[Math.floor(Math.random() * options.length)];
            }
        }
        
        function getRandomFutureDate() {
            const today = new Date();
            const randomDays = Math.floor(Math.random() * 60) + 1;
            const futureDate = new Date(today.getTime() + (randomDays * 24 * 60 * 60 * 1000));
            return futureDate.toISOString().slice(0, 16);
        }

        function getRandomFutureEndDate() {
            const today = new Date();
            const randomDays = Math.floor(Math.random() * 60) + 1;
            const randomHours = Math.floor(Math.random() * 4) + 2;
            const futureDate = new Date(today.getTime() + (randomDays * 24 * 60 * 60 * 1000) + (randomHours * 60 * 60 * 1000));
            return futureDate.toISOString().slice(0, 16);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('page')) {
                const table = document.getElementById('events-table');
                if (table) {
                    setTimeout(() => {
                        table.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }, 100);
                }
            }
        });
    </script>

@endsection


