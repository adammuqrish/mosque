@extends('layouts.app')

@section('back', '/dashboard')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-amber-50 via-white to-orange-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Tier Milestones</h1>
                <p class="text-gray-600 mt-1">Configure volunteer tier levels and benefits</p>
            </div>
            <div class="mt-4 md:mt-0">
                <a href="{{ route('admin.gamification.tiers.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Create Tier
                </a>
            </div>
        </div>

        {{-- Navigation Tabs --}}
        <div class="flex gap-4 mb-8 flex-wrap">
            <a href="{{ route('admin.gamification.index') }}" class="px-4 py-2 bg-white text-gray-700 rounded-lg font-medium hover:bg-gray-50">
                Members
            </a>
            <a href="{{ route('admin.gamification.badges.index') }}" class="px-4 py-2 bg-white text-gray-700 rounded-lg font-medium hover:bg-gray-50">
                Badges
            </a>
            <a href="{{ route('admin.gamification.rewards.index') }}" class="px-4 py-2 bg-white text-gray-700 rounded-lg font-medium hover:bg-gray-50">
                Rewards
            </a>
            <a href="{{ route('admin.gamification.redemptions') }}" class="px-4 py-2 bg-white text-gray-700 rounded-lg font-medium hover:bg-gray-50">
                Redemptions
            </a>
            <a href="{{ route('admin.gamification.tiers.index') }}" class="px-4 py-2 bg-emerald-600 text-white rounded-lg font-medium">
                Tiers
            </a>
        </div>

        {{-- Tiers Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($tiers as $tier)
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden hover:shadow-2xl transition-shadow">
                    {{-- Tier Header --}}
                    <div class="p-6 text-center
                        @if($tier->tier === 'diamond') bg-gradient-to-br from-purple-900 to-purple-700 text-white
                        @elseif($tier->tier === 'platinum') bg-gradient-to-br from-gray-600 to-gray-400 text-white
                        @elseif($tier->tier === 'gold') bg-gradient-to-br from-amber-600 to-amber-400 text-white
                        @elseif($tier->tier === 'silver') bg-gradient-to-br from-slate-500 to-slate-300 text-gray-800
                        @else bg-gradient-to-br from-orange-600 to-orange-400 text-white
                        @endif
                    ">
                        <div class="w-16 h-16 mx-auto mb-3 flex items-center justify-center">
                            @if($tier->icon_svg)
                                <span class="w-full h-full flex items-center justify-center">{!! $tier->icon_svg !!}</span>
                            @else
                                <span class="text-4xl">
                                    @if($tier->tier === 'diamond') 👑
                                    @elseif($tier->tier === 'platinum') 💎
                                    @elseif($tier->tier === 'gold') 🥇
                                    @elseif($tier->tier === 'silver') 🥈
                                    @else 🥉
                                    @endif
                                </span>
                            @endif
                        </div>
                        <h3 class="text-xl font-bold capitalize">{{ $tier->tier }}</h3>
                        <p class="text-sm opacity-80 mt-1">{{ $tier->min_points }} pts required</p>
                    </div>

                    {{-- Tier Body --}}
                    <div class="p-6">
                        <h4 class="font-bold text-gray-900 text-lg mb-2">{{ $tier->name }}</h4>
                        <p class="text-sm text-gray-500 mb-3">{{ $tier->name_my }}</p>

                        <div class="mb-4">
                            <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Benefits</p>
                            <ul class="space-y-1">
                                @foreach($tier->benefits_array as $benefit)
                                    <li class="flex items-start gap-2 text-sm text-gray-700">
                                        <svg class="w-4 h-4 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        {{ trim($benefit) }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="flex items-center gap-2 pt-4 border-t border-gray-100">
                            <a href="{{ route('admin.gamification.tiers.edit', $tier) }}"
                               class="flex-1 px-3 py-2 bg-blue-100 text-blue-700 rounded-lg text-sm font-medium text-center hover:bg-blue-200 transition">
                                Edit
                            </a>
                            <button type="button" onclick="showConfirmModal('Delete Tier', 'Delete this tier? Members at this level may lose tier display.', 'Delete', 'bg-red-600 hover:bg-red-700', '{{ route('admin.gamification.tiers.destroy', $tier) }}', 'DELETE')" class="flex-1 w-full px-3 py-2 bg-red-100 text-red-700 rounded-lg text-sm font-medium hover:bg-red-200 transition">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                        <span class="text-4xl">🏆</span>
                    </div>
                    <p class="text-gray-500 mb-4">No tier milestones configured yet.</p>
                    <a href="{{ route('admin.gamification.tiers.create') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Create First Tier
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

