@extends('layouts.app')

@section('back', '/dashboard')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Leaderboard</h1>
            <p class="text-gray-600">See how you rank among our volunteers</p>
        </div>

        {{-- Type Filter --}}
        <div class="flex justify-center gap-3 mb-4">
            <a href="{{ route('gamification.leaderboard', ['type' => 'global']) }}"
               class="px-4 py-2 rounded-full text-sm font-medium transition-colors {{ $type === 'global' ? 'bg-emerald-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50 shadow-sm' }}">
                Global
            </a>
            <a href="{{ route('gamification.leaderboard', ['type' => 'monthly']) }}"
               class="px-4 py-2 rounded-full text-sm font-medium transition-colors {{ $type === 'monthly' ? 'bg-emerald-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50 shadow-sm' }}">
                Monthly
            </a>
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" @click.away="open = false"
                   class="px-4 py-2 rounded-full text-sm font-medium transition-colors {{ $type === 'category' ? 'bg-emerald-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50 shadow-sm' }} flex items-center gap-2">
                    By Category
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="open" class="absolute left-0 mt-2 w-48 bg-white rounded-lg shadow-lg border z-10" style="display: none;">
                    @php $categories = ['education', 'community', 'religious', 'charity', 'maintenance', 'youth', 'elderly']; @endphp
                    @foreach($categories as $cat)
                        <a href="{{ route('gamification.leaderboard', ['type' => 'category', 'category' => $cat]) }}"
                           class="block px-4 py-2 text-sm hover:bg-gray-50 first:rounded-t-lg last:rounded-b-lg {{ $type === 'category' && $category === $cat ? 'bg-emerald-50 text-emerald-700' : 'text-gray-700' }}">
                            {{ ucfirst($cat) }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        @if($type === 'category' && $category)
            <div class="flex justify-center mb-6">
                <span class="bg-emerald-100 text-emerald-700 px-4 py-2 rounded-full text-sm font-medium">
                    Category: {{ ucfirst($category) }}
                </span>
            </div>
        @endif

        {{-- User's Rank Card --}}
        <div class="bg-gradient-to-r from-emerald-500 to-teal-500 rounded-3xl p-6 mb-8 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-emerald-100 text-sm">Your Position</p>
                    <p class="text-4xl font-bold">#{{ $userRank['global'] }}</p>
                    <p class="text-emerald-100 text-sm mt-1">out of {{ $userRank['total_members'] }} volunteers</p>
                </div>
                @if($userRank['monthly'] > 0)
                    <div class="text-right">
                        <p class="text-emerald-100 text-sm">Monthly Rank</p>
                        <p class="text-3xl font-bold">#{{ $userRank['monthly'] }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Leaderboard List --}}
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
            @foreach($leaderboard as $entry)
                <div class="flex items-center gap-4 p-4 border-b border-gray-100 last:border-b-0 hover:bg-gray-50 transition-colors {{ $entry['user_id'] === auth()->id() ? 'bg-emerald-50' : '' }}">
                    
                    {{-- Position --}}
                    <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-lg
                        {{ $entry['position'] === 1 ? 'bg-amber-100 text-amber-700' : '' }}
                        {{ $entry['position'] === 2 ? 'bg-gray-200 text-gray-700' : '' }}
                        {{ $entry['position'] === 3 ? 'bg-orange-100 text-orange-700' : '' }}
                        {{ $entry['position'] > 3 ? 'bg-gray-100 text-gray-600' : '' }}
                    ">
                        {{ $entry['position'] }}
                    </div>

                    {{-- Avatar --}}
                    <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center text-xl">
                        @if(isset($entry['tier_icon']) && $entry['tier_icon'])
                            {!! $entry['tier_icon'] !!}
                        @else
                            👤
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="flex-1">
                        <p class="font-semibold text-gray-900">
                            {{ $entry['name'] }}
                            @if($entry['user_id'] === auth()->id())
                                <span class="ml-2 px-2 py-0.5 bg-emerald-100 text-emerald-700 text-xs rounded-full">You</span>
                            @endif
                        </p>
                        <div class="flex items-center gap-2 text-sm text-gray-500">
                            @if(isset($entry['tier']))
                                <span class="capitalize">{{ $entry['tier'] }}</span>
                                <span>•</span>
                            @endif
                            <span>{{ $entry['badge_count'] }} badges</span>
                        </div>
                    </div>

                    {{-- Points --}}
                    @if(isset($entry['points']))
                        <div class="text-right">
                            <p class="text-xl font-bold text-emerald-600">{{ number_format($entry['points']) }}</p>
                            <p class="text-xs text-gray-500">points</p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        @if(count($leaderboard) === 0)
            <div class="bg-white rounded-3xl p-12 shadow-xl text-center">
                <p class="text-gray-500">No leaderboard data yet. Start volunteering to appear here!</p>
            </div>
        @endif
    </div>
</div>
@endsection

