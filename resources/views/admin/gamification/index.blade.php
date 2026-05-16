@extends('layouts.app')

@section('back', '/dashboard')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-amber-50 via-white to-orange-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Gamification Management</h1>
                <p class="text-gray-600 mt-1">Manage member points, badges, and reward redemptions</p>
            </div>
            <div class="mt-4 md:mt-0 flex gap-4">
                <div class="bg-white rounded-2xl px-4 sm:px-6 py-3 shadow-lg">
                    <p class="text-sm text-gray-500">Total Points Issued</p>
                    <p class="text-xl font-bold text-emerald-600">{{ number_format($totalPoints) }}</p>
                </div>
                <div class="bg-white rounded-2xl px-4 sm:px-6 py-3 shadow-lg">
                    <p class="text-sm text-gray-500">Active Members</p>
                    <p class="text-xl font-bold text-blue-600">{{ number_format($totalMembers) }}</p>
                </div>
            </div>
        </div>

        {{-- Navigation Tabs --}}
        <div class="flex gap-4 mb-8 flex-wrap">
            <a href="{{ route('admin.gamification.index') }}" class="px-4 py-2 bg-emerald-600 text-white rounded-lg font-medium">
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
            <a href="{{ route('admin.gamification.tiers.index') }}" class="px-4 py-2 bg-white text-gray-700 rounded-lg font-medium hover:bg-gray-50">
                Tiers
            </a>
        </div>

        {{-- Search --}}
        <div class="bg-white rounded-2xl shadow-lg p-4 mb-8">
            <form method="GET" class="flex flex-col sm:flex-row gap-3">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ $search ?? '' }}"
                    placeholder="Search by name or email..." 
                    class="w-full sm:flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                >
                <button type="submit" class="w-full sm:w-auto px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                    Search
                </button>
            </form>
        </div>

        {{-- Members Table --}}
        <div id="gamification-table" class="bg-white rounded-2xl shadow-xl overflow-hidden">
            {{-- Desktop Table --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tier</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'total_points', 'direction' => $sort === 'total_points' && $direction === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-700">
                                    Total Points
                                    @if($sort === 'total_points')
                                        <svg class="w-4 h-4 {{ $direction === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'available_points', 'direction' => $sort === 'available_points' && $direction === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-700">
                                    Available
                                    @if($sort === 'available_points')
                                        <svg class="w-4 h-4 {{ $direction === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'redeemed_points', 'direction' => $sort === 'redeemed_points' && $direction === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-700">
                                    Redeemed
                                    @if($sort === 'redeemed_points')
                                        <svg class="w-4 h-4 {{ $direction === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'current_streak', 'direction' => $sort === 'current_streak' && $direction === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-700">
                                    Streak
                                    @if($sort === 'current_streak')
                                        <svg class="w-4 h-4 {{ $direction === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($members as $member)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        @if($member->user->avatar_url)
                                            <img src="{{ $member->user->avatar_url }}" alt="{{ $member->user->name }}" class="w-10 h-10 rounded-full object-cover">
                                        @else
                                            <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center font-bold text-emerald-700">
                                                {{ $member->user->initials }}
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $member->user->name }}</p>
                                            <p class="text-sm text-gray-500">{{ $member->user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $tier = $member->tier;
                                    @endphp
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium capitalize whitespace-nowrap
                                        @if($tier && $tier->tier == 'diamond') bg-purple-100 text-purple-700
                                        @elseif($tier && $tier->tier == 'platinum') bg-gray-200 text-gray-700
                                        @elseif($tier && $tier->tier == 'gold') bg-amber-100 text-amber-700
                                        @elseif($tier && $tier->tier == 'silver') bg-slate-100 text-slate-700
                                        @else bg-orange-100 text-orange-700
                                        @endif
                                    ">
                                        @if($tier)
                                            <span class="w-4 h-4 inline-flex items-center justify-center flex-shrink-0">
                                                <svg class="w-full h-full" viewBox="0 0 24 24" fill="currentColor">{!! $tier->icon_svg !!}</svg>
                                            </span>
                                            {{ $tier->name }}
                                        @else
                                            Bronze
                                        @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-lg font-bold text-emerald-600">{{ number_format($member->total_points) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    {{ number_format($member->available_points) }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ number_format($member->redeemed_points) }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-orange-600 font-medium">🔥 {{ $member->current_streak }}</span>
                                    <span class="text-xs text-gray-400 block">Best: {{ $member->longest_streak }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex gap-2">
                                        <button 
                                            type="button"
                                            class="px-3 py-1 bg-blue-100 text-blue-700 rounded-lg text-sm hover:bg-blue-200"
                                            onclick="openAdjustModal({{ $member->user->id }}, '{{ $member->user->name }}')"
                                        >
                                            Adjust
                                        </button>
                                        <a 
                                            href="{{ route('admin.gamification.transactions', $member->user->id) }}"
                                            class="px-3 py-1 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200"
                                        >
                                            History
                                        </a>
                                    </div>
                                </td>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    No members found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="md:hidden divide-y divide-gray-200">
                @forelse($members as $member)
                    <div class="p-4 space-y-3">
                        <div class="flex items-center gap-3">
                            @if($member->user->avatar_url)
                                <img src="{{ $member->user->avatar_url }}" alt="{{ $member->user->name }}" class="w-10 h-10 rounded-full object-cover">
                            @else
                                <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center font-bold text-emerald-700">
                                    {{ $member->user->initials }}
                                </div>
                            @endif
                            <div>
                                <p class="font-medium text-gray-900">{{ $member->user->name }}</p>
                                <p class="text-sm text-gray-500">{{ $member->user->email }}</p>
                            </div>
                        </div>
                        <div>
                            @php
                                $tier = $member->tier;
                            @endphp
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium capitalize
                                @if($tier && $tier->tier == 'diamond') bg-purple-100 text-purple-700
                                @elseif($tier && $tier->tier == 'platinum') bg-gray-200 text-gray-700
                                @elseif($tier && $tier->tier == 'gold') bg-amber-100 text-amber-700
                                @elseif($tier && $tier->tier == 'silver') bg-slate-100 text-slate-700
                                @else bg-orange-100 text-orange-700
                                @endif
                            ">
                                @if($tier)
                                    <span class="w-4 h-4 inline-flex items-center justify-center flex-shrink-0">
                                        <svg class="w-full h-full" viewBox="0 0 24 24" fill="currentColor">{!! $tier->icon_svg !!}</svg>
                                    </span>
                                    {{ $tier->name }}
                                @else
                                    Bronze
                                @endif
                            </span>
                        </div>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <span class="text-gray-500">Total Points</span>
                                <p class="text-lg font-bold text-emerald-600">{{ number_format($member->total_points) }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500">Available</span>
                                <p class="text-lg font-bold">{{ number_format($member->available_points) }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500">Redeemed</span>
                                <p class="font-medium">{{ number_format($member->redeemed_points) }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500">Streak</span>
                                <p class="text-orange-600 font-medium">🔥 {{ $member->current_streak }}
                                    <span class="text-xs text-gray-400">Best: {{ $member->longest_streak }}</span>
                                </p>
                            </div>
                        </div>
                        <div class="flex gap-2 pt-1">
                            <button type="button"
                                    class="flex-1 text-center px-3 py-2 bg-blue-100 text-blue-700 rounded-lg text-sm font-medium hover:bg-blue-200 transition"
                                    onclick="openAdjustModal({{ $member->user->id }}, '{{ $member->user->name }}')">
                                Adjust
                            </button>
                            <a href="{{ route('admin.gamification.transactions', $member->user->id) }}"
                               class="flex-1 text-center px-3 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition">
                                History
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center text-gray-500">
                        No members found
                    </div>
                @endforelse
            </div>

            @if($members->hasPages())
                <div id="gamification-pagination" class="px-6 py-4 border-t border-gray-200">
                    {{ $members->appends(request()->except('page'))->links() }}
                </div>
            @endif
        </div>

        {{-- Adjust Points Modal --}}
        <div id="adjust-modal" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeAdjustModal()"></div>
            <div class="fixed inset-0 flex items-center justify-center p-4">
                <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Adjust Points</h3>
                    <p class="text-gray-600 mb-4">Adjust points for: <strong id="modal-user-name"></strong></p>
                    
                    <form action="" method="POST" id="adjust-form">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Points (use negative to deduct)</label>
                            <input type="number" name="points" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="e.g., 50 or -50">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Reason (required)</label>
                            <input type="text" name="reason" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="e.g., Event cancelled, bonus for extra work">
                        </div>
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Admin Notes (optional)</label>
                            <textarea name="notes" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"></textarea>
                        </div>
                        <div class="flex justify-end gap-3">
                            <button type="button" onclick="closeAdjustModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                                Adjust Points
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function openAdjustModal(userId, userName) {
    document.getElementById('modal-user-name').textContent = userName;
    document.getElementById('adjust-form').action = '/admin/gamification/members/' + userId + '/adjust';
    document.getElementById('adjust-modal').classList.remove('hidden');
}

function closeAdjustModal() {
    document.getElementById('adjust-modal').classList.add('hidden');
}

document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('page')) {
        const table = document.getElementById('gamification-table');
        if (table) {
            setTimeout(() => {
                table.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 100);
        }
    }
});
</script>
@endsection


