@extends('layouts.app')

@section('back', '/dashboard')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#FAFAF5] via-white to-[#D4EDDA]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        {{-- Gamification Summary Widget --}}
        <div class="bg-white rounded-3xl shadow-xl p-6 mb-8 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-emerald-100/50 to-teal-100/50 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            
            <div class="relative grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Points & Tier --}}
                <div class="lg:col-span-2">
                    <div class="flex items-start gap-6">
                        {{-- Progress Ring --}}
                        <div class="relative flex-shrink-0">
                            <svg class="w-28 h-28 transform -rotate-90">
                                <circle cx="56" cy="56" r="48" stroke="#E5E7EB" stroke-width="8" fill="none"/>
                                <circle 
                                    cx="56" cy="56" r="48" 
                                    stroke="url(#tierGradient)" 
                                    stroke-width="8" 
                                    fill="none"
                                    stroke-linecap="round"
                                    stroke-dasharray="{{ round(($stats['tier_progress']['progress'] ?? 0) * 3.0159) }} 301.59"
                                />
                                <defs>
                                    <linearGradient id="tierGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                        <stop offset="0%" stop-color="#10B981"/>
                                        <stop offset="100%" stop-color="#059669"/>
                                    </linearGradient>
                                </defs>
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                @if($stats['tier_icon'])
                                    <span>{!! $stats['tier_icon'] !!}</span>
                                @else
                                    <span class="text-2xl">🌱</span>
                                @endif
                            </div>
                        </div>

                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h2 class="text-2xl font-bold text-gray-900">{{ $stats['tier_name'] ?? 'Bronze Volunteer' }}</h2>
                                <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-sm font-semibold capitalize">
                                    {{ $stats['tier'] ?? 'bronze' }}
                                </span>
                            </div>
                            
                            <p class="text-gray-600 mb-4">
                                <span class="font-bold text-xl text-emerald-600">{{ number_format($stats['total_points']) }}</span> total points
                                <span class="mx-2">•</span>
                                <span class="font-semibold">{{ number_format($stats['available_points']) }}</span> available
                            </p>

                            {{-- Tier Progress --}}
                            @if(isset($stats['tier_progress']['next_tier']) && $stats['tier_progress']['next_tier'])
                                <div class="mb-2">
                                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                                        <span>Progress to {{ $stats['tier_progress']['next_tier']->name }}</span>
                                        <span>{{ $stats['tier_progress']['points_needed'] }} pts to go</span>
                                    </div>
                                    <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                                        <div 
                                            class="h-full bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full transition-all duration-500"
                                            style="width: {{ $stats['tier_progress']['progress'] }}%"
                                        ></div>
                                    </div>
                                </div>
                            @endif

                            {{-- Quick Stats --}}
                            <div class="flex flex-wrap gap-4 mt-4">
                                <div class="flex items-center gap-2">
                                    <span class="text-2xl">🔥</span>
                                    <div>
                                        <p class="text-xs text-gray-500">Current Streak</p>
                                        <p class="font-bold text-gray-900">{{ $stats['current_streak'] }} events</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-2xl">🏆</span>
                                    <div>
                                        <p class="text-xs text-gray-500">Badges Earned</p>
                                        <p class="font-bold text-gray-900">{{ $stats['badge_count'] }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-2xl">📊</span>
                                    <div>
                                        <p class="text-xs text-gray-500">Global Rank</p>
                                        <p class="font-bold text-gray-900">#{{ $userRank['global'] }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Next Badge Preview --}}
                <div class="border-t lg:border-t-0 lg:border-l border-gray-100 lg:pl-8 pt-6 lg:pt-0">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Next Badges</h3>
                    @if(count($nextBadges) > 0)
                        @foreach($nextBadges as $item)
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-100 to-orange-100 flex items-center justify-center text-2xl">
                                    @if($item['badge']->icon_url)
                                        <img src="{{ $item['badge']->icon_url }}" alt="{{ $item['badge']->name }}" class="w-8 h-8 object-contain">
                                    @elseif($item['badge']->is_raw_svg)
                                        {!! $item['badge']->icon_svg !!}
                                    @else
                                        {{ $item['badge']->fallback_emoji }}
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-900">{{ $item['badge']->name }}</p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <div class="flex-1 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                            <div class="h-full bg-amber-500 rounded-full" style="width: {{ $item['progress'] }}%"></div>
                                        </div>
                                        <span class="text-xs text-gray-500">{{ $item['remaining'] }} more</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-gray-500 text-sm">You've earned all milestones! Keep volunteering!</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <a href="{{ route('gamification.badges') }}" class="bg-white rounded-2xl p-4 shadow-md hover:shadow-lg transition-shadow flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-100 to-pink-100 flex items-center justify-center text-2xl">
                    🏅
                </div>
                <div>
                    <p class="font-semibold text-gray-900">My Badges</p>
                    <p class="text-xs text-gray-500">{{ $stats['badge_count'] }} earned</p>
                </div>
            </a>
            <a href="{{ route('gamification.rewards') }}" class="bg-white rounded-2xl p-4 shadow-md hover:shadow-lg transition-shadow flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-100 to-teal-100 flex items-center justify-center text-2xl">
                    🎁
                </div>
                <div>
                    <p class="font-semibold text-gray-900">Rewards</p>
                    <p class="text-xs text-gray-500">{{ number_format($stats['available_points']) }} pts</p>
                </div>
            </a>
            <a href="{{ route('gamification.leaderboard') }}" class="bg-white rounded-2xl p-4 shadow-md hover:shadow-lg transition-shadow flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center text-2xl">
                    📊
                </div>
                <div>
                    <p class="font-semibold text-gray-900">Leaderboard</p>
                    <p class="text-xs text-gray-500">Rank #{{ $userRank['global'] }}</p>
                </div>
            </a>
            <a href="{{ route('gamification.points-history') }}" class="bg-white rounded-2xl p-4 shadow-md hover:shadow-lg transition-shadow flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-100 to-orange-100 flex items-center justify-center text-2xl">
                    📜
                </div>
                <div>
                    <p class="font-semibold text-gray-900">History</p>
                    <p class="text-xs text-gray-500">Point log</p>
                </div>
            </a>
        </div>

        {{-- Upcoming Events Posters --}}
        <div class="mb-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Upcoming Events</h2>
                <a href="{{ route('volunteer.my-events') }}" class="text-emerald-600 hover:text-emerald-700 font-medium text-sm flex items-center gap-1">
                    View all events
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($upcomingEvents as $event)
                    @include('gamification.components.event-poster', ['event' => $event])
                @empty
                    <div class="col-span-full text-center py-12">
                        <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">No upcoming events</h3>
                        <p class="text-gray-500">Check back soon for new volunteer opportunities!</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Leaderboard Preview --}}
        <div class="bg-white rounded-3xl shadow-xl p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900">Top Volunteers</h2>
                <a href="{{ route('gamification.leaderboard') }}" class="text-emerald-600 hover:text-emerald-700 font-medium text-sm">
                    Full leaderboard →
                </a>
            </div>

            <div class="space-y-3">
                @foreach($leaderboard as $entry)
                    <div class="flex items-center gap-4 p-3 rounded-xl hover:bg-gray-50 transition-colors {{ $entry['user_id'] === auth()->id() ? 'bg-emerald-50 ring-2 ring-emerald-200' : '' }}">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm
                            {{ $entry['position'] === 1 ? 'bg-amber-100 text-amber-700' : '' }}
                            {{ $entry['position'] === 2 ? 'bg-gray-200 text-gray-700' : '' }}
                            {{ $entry['position'] === 3 ? 'bg-orange-100 text-orange-700' : '' }}
                            {{ $entry['position'] > 3 ? 'bg-gray-100 text-gray-600' : '' }}
                        ">
                            {{ $entry['position'] }}
                        </div>
                        <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-lg">
                            @if(isset($entry['tier_icon']) && $entry['tier_icon'])
                                {!! $entry['tier_icon'] !!}
                            @else
                                👤
                            @endif
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900">
                                {{ $entry['name'] }}
                                @if($entry['user_id'] === auth()->id())
                                    <span class="text-xs text-emerald-600 font-normal">(You)</span>
                                @endif
                            </p>
                            <p class="text-xs text-gray-500">{{ $entry['badge_count'] }} badges</p>
                        </div>
                        @if(isset($entry['points']))
                            <div class="text-right">
                                <p class="font-bold text-emerald-600">{{ number_format($entry['points']) }}</p>
                                <p class="text-xs text-gray-500">points</p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Motivational Section --}}
        <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-3xl p-8 text-center text-white">
            <p class="text-xl md:text-2xl font-medium mb-2">
                "The best among you are those who bring the greatest benefit to others."
            </p>
            <p class="text-emerald-200">— Prophet Muhammad ﷺ</p>
        </div>
    </div>
</div>
@endsection

