@extends('layouts.app')

@section('back', '/dashboard')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-pink-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="text-center mb-12">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">My Badges</h1>
            <p class="text-gray-600">Achievements you've earned through volunteering</p>
        </div>

        {{-- Earned Badges --}}
        <div class="mb-12">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Earned Badges ({{ count($earnedBadges) }})</h2>
            
            @if(count($earnedBadges) > 0)
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                    @foreach($earnedBadges as $badge)
                        <div class="bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all text-center relative overflow-hidden group">
                            <div class="absolute inset-0 bg-gradient-to-br from-emerald-100/50 to-teal-100/50 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            <div class="relative">
                                <div class="w-16 h-16 mx-auto mb-3 rounded-2xl bg-gradient-to-br from-amber-100 to-orange-100 flex items-center justify-center text-3xl">
                                    @if($badge->icon_url)
                                        <img src="{{ $badge->icon_url }}" alt="{{ $badge->name }}" class="w-10 h-10 object-contain">
                                    @elseif($badge->is_raw_svg)
                                        {!! $badge->icon_svg !!}
                                    @else
                                        {{ $badge->fallback_emoji }}
                                    @endif
                                </div>
                                <h3 class="font-bold text-gray-900 mb-1">{{ $badge->name }}</h3>
                                <p class="text-xs text-gray-500 mb-2">{{ $badge->name_my }}</p>
                                @if($badge->pivot && $badge->pivot->earned_at)
                                    <p class="text-xs text-gray-400">Earned {{ $badge->pivot->earned_at->diffForHumans() }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-2xl p-12 shadow-lg text-center">
                    <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                        <span class="text-4xl">🏅</span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No badges yet</h3>
                    <p class="text-gray-500">Complete volunteer events to earn your first badge!</p>
                </div>
            @endif
        </div>

        {{-- Available Badges --}}
        <div>
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Badges to Earn</h2>
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                @php $earnedIds = $earnedBadges->pluck('id')->toArray(); @endphp
                @foreach($allBadges as $badge)
                    @if(!in_array($badge->id, $earnedIds))
                        <div class="bg-white rounded-2xl p-6 shadow-md hover:shadow-lg transition-all text-center opacity-60">
                            <div class="w-16 h-16 mx-auto mb-3 rounded-2xl bg-gray-100 flex items-center justify-center text-3xl grayscale">
                                @if($badge->icon_url)
                                    <img src="{{ $badge->icon_url }}" alt="{{ $badge->name }}" class="w-10 h-10 object-contain grayscale">
                                @elseif($badge->is_raw_svg)
                                    {!! $badge->icon_svg !!}
                                @else
                                    {{ $badge->fallback_emoji }}
                                @endif
                            </div>
                            <h3 class="font-bold text-gray-700 mb-1">{{ $badge->name }}</h3>
                            <p class="text-xs text-gray-500 mb-2">{{ $badge->name_my }}</p>
                            <p class="text-xs text-gray-400">{{ $badge->description }}</p>
                            <p class="text-xs text-emerald-600 mt-2 font-medium">+{{ $badge->points_awarded }} pts</p>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

