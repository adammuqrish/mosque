@php
    $showPointsPreview = $showPointsPreview ?? true;
    $capacityPercent = $event->volunteers()->count() / $event->max_volunteers * 100;
    $isFull = $event->isFull();
    $daysUntil = now()->diffInDays($event->event_date);
    $basePoints = 50;
    
    $categoryStyles = [
        'religious' => ['bg' => 'from-amber-500 to-orange-600', 'text' => 'text-amber-100'],
        'charity' => ['bg' => 'from-emerald-500 to-teal-600', 'text' => 'text-emerald-100'],
        'education' => ['bg' => 'from-blue-500 to-indigo-600', 'text' => 'text-blue-100'],
        'emergency' => ['bg' => 'from-red-500 to-rose-600', 'text' => 'text-red-100'],
        'community' => ['bg' => 'from-violet-500 to-purple-600', 'text' => 'text-violet-100'],
        'general' => ['bg' => 'from-slate-500 to-gray-600', 'text' => 'text-slate-100'],
    ];
    $style = $categoryStyles[$event->gamification_category] ?? $categoryStyles['general'];

    // Safely get required_skills as array
    $requiredSkills = $event->required_skills;
    if (is_string($requiredSkills)) {
        $requiredSkills = json_decode($requiredSkills, true) ?: [];
    } elseif (!is_array($requiredSkills)) {
        $requiredSkills = [];
    }
@endphp

<div 
    x-data="{ 
        expanded: false, 
        joined: {{ $event->volunteers()->where('user_id', auth()->id())->exists() ? 'true' : 'false' }},
        loading: false,
        countdown: {{ strtotime($event->event_date) * 1000 }}
    }"
    class="group relative bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 overflow-hidden transform hover:-translate-y-1"
>
    {{-- Gradient Header --}}
    <div class="h-32 bg-gradient-to-br {{ $style['bg'] }} relative overflow-hidden">
        <div class="absolute -right-8 -top-8 w-32 h-32 rounded-full bg-white/10"></div>
        <div class="absolute -right-4 top-16 w-20 h-20 rounded-full bg-white/5"></div>
        
        {{-- Category Badge --}}
        <span class="absolute top-4 left-4 px-3 py-1 rounded-full text-xs font-semibold bg-white/20 backdrop-blur-sm {{ $style['text'] }} uppercase tracking-wider">
            {{ $event->gamification_category }}
        </span>

        {{-- Points Preview --}}
        @if($showPointsPreview)
            <div class="absolute top-4 right-4 px-3 py-1.5 rounded-full bg-white/20 backdrop-blur-sm flex items-center gap-1.5">
                <svg class="w-4 h-4 text-yellow-300" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                <span class="text-sm font-bold text-white">+{{ $basePoints }}</span>
            </div>
        @endif

        {{-- Poster Icon --}}
        <div class="absolute inset-0 flex items-center justify-center opacity-20">
            <svg class="w-20 h-20 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
        </div>
    </div>

    {{-- Content --}}
    <div class="p-5">
        <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2 group-hover:text-emerald-600 transition-colors">
            {{ $event->title }}
        </h3>

        {{-- Date & Location --}}
        <div class="space-y-2 mb-4">
            <div class="flex items-center gap-2 text-sm text-gray-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span>{{ $event->event_date->format('d M Y, H:i') }}</span>
            </div>
            @if($event->location)
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>{{ $event->location }}</span>
                </div>
            @endif
        </div>

        {{-- Countdown --}}
        <div x-show="countdown > Date.now()" class="mb-4">
            <div class="flex items-center gap-2 text-sm">
                <span class="px-2 py-1 bg-amber-50 text-amber-700 rounded-lg font-medium">
                    <span x-text="Math.max(0, Math.ceil((countdown - Date.now()) / (1000 * 60 * 60 * 24)))"></span> days
                </span>
                <span class="text-gray-400">until event</span>
            </div>
        </div>

        @if($event->required_skills && count($requiredSkills) > 0)
            <div class="flex flex-wrap gap-1.5 mb-4">
                @foreach(array_slice($requiredSkills, 0, 3) as $skill)
                    <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs">
                        {{ $skill }}
                    </span>
                @endforeach
                @if(count($requiredSkills) > 3)
                    <span class="px-2 py-0.5 bg-gray-100 text-gray-500 rounded text-xs">
                        +{{ count($requiredSkills) - 3 }}
                    </span>
                @endif
            </div>
        @endif

        {{-- Capacity Bar --}}
        <div class="mb-4">
            <div class="flex justify-between text-xs text-gray-500 mb-1">
                <span>{{ $event->volunteers()->count() }} / {{ $event->max_volunteers }} volunteers</span>
                <span class="{{ $isFull ? 'text-red-500' : 'text-emerald-500' }}">
                    {{ $isFull ? 'Full' : 'Open' }}
                </span>
            </div>
            <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                <div 
                    class="h-full rounded-full transition-all duration-500 {{ $isFull ? 'bg-red-500' : 'bg-emerald-500' }}"
                    style="width: {{ min(100, (int)$capacityPercent) }}%"
                ></div>
            </div>
        </div>

        {{-- Action Button --}}
        @if(auth()->user()->isMember())
            <button 
                @click="
                    loading = true;
                    fetch('{{ route('volunteer.join', $event) }}', {
                        method: 'POST',
                        headers: { 
                            'X-CSRF-TOKEN': '{{ csrf_token() }}', 
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(r => r.json())
                    .then(d => { 
                        if(d.success) { joined = true; showToast(d.message || 'Joined successfully!'); }
                        else { showToast(d.message || 'Failed to join', 'error'); }
                        loading = false;
                    })
                    .catch(() => { 
                        loading = false;
                        window.location.reload();
                    });
                "
                :disabled="joined || {{ $isFull ? 'true' : 'false' }} || loading"
                :class="joined || {{ $isFull ? 'true' : 'false' }} ? 'bg-gray-400 cursor-not-allowed' : 'bg-emerald-600 hover:bg-emerald-700'"
                class="w-full py-2.5 px-4 rounded-xl text-white font-semibold transition-all duration-300 flex items-center justify-center gap-2 disabled:cursor-not-allowed"
            >
                <template x-if="loading">
                    <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </template>
                <template x-if="!loading">
                    <span x-text="joined ? '✓ Joined' : '{{ $isFull ? 'Event Full' : 'Join Event' }}'"></span>
                </template>
            </button>
        @endif
    </div>

    {{-- Expand Toggle --}}
    <button 
        @click="expanded = !expanded"
        class="absolute bottom-3 right-3 w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors"
    >
        <svg 
            class="w-4 h-4 text-gray-500 transition-transform duration-300" 
            :class="{ 'rotate-180': expanded }"
            fill="none" stroke="currentColor" viewBox="0 0 24 24"
        >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    {{-- Expandable Details --}}
    <div 
        x-show="expanded"
        x-collapse
        class="border-t border-gray-100 p-5 bg-gray-50"
    >
        @if($event->description)
            <p class="text-sm text-gray-600 mb-4">{{ $event->description }}</p>
        @endif
        
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <span class="text-gray-500">Confirmed</span>
                <p class="font-semibold">{{ $event->volunteers()->where('attendance_status', 'confirmed')->count() }}</p>
            </div>
            <div>
                <span class="text-gray-500">Pending Review</span>
                <p class="font-semibold">{{ $event->volunteers()->where('attendance_status', 'pending_review')->count() }}</p>
            </div>
        </div>
    </div>
</div>
