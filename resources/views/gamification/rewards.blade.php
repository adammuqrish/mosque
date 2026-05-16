@extends('layouts.app')

@section('back', '/dashboard')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-amber-50 via-white to-orange-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Reward Catalog</h1>
                <p class="text-gray-600 mt-1">Redeem your hard-earned points for exclusive rewards</p>
            </div>
            <div class="mt-4 md:mt-0 flex items-center gap-3 bg-white rounded-2xl px-4 sm:px-6 py-3 shadow-lg">
                <span class="text-2xl">⭐</span>
                <div>
                    <p class="text-sm text-gray-500">Available Points</p>
                    <p class="text-xl font-bold text-emerald-600">{{ number_format($userPoints) }}</p>
                </div>
            </div>
        </div>

        {{-- Category Filter --}}
        <div class="flex flex-wrap gap-3 mb-8">
            <a href="{{ route('gamification.rewards') }}" 
               class="px-4 py-2 rounded-full text-sm font-medium transition-colors {{ !$category ? 'bg-emerald-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50 shadow-sm' }}">
                All
            </a>
            @foreach(['facilities', 'recognition', 'events', 'merchandise_common', 'merchandise_limited'] as $cat)
                <a href="{{ route('gamification.rewards', ['category' => $cat]) }}"
                   class="px-4 py-2 rounded-full text-sm font-medium transition-colors {{ $category === $cat ? 'bg-emerald-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50 shadow-sm' }}">
                    {{ ucfirst(str_replace('_', ' ', $cat)) }}
                </a>
            @endforeach
        </div>

        {{-- Rewards Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($rewards as $reward)
                <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden">
                    {{-- Header --}}
                    <div class="h-24 bg-gradient-to-br from-amber-400 to-orange-500 relative">
                        <div class="absolute -bottom-8 left-6 w-16 h-16 rounded-2xl bg-white shadow-lg flex items-center justify-center text-3xl">
                            @if($reward->category === 'facilities') 🏛️
                            @elseif($reward->category === 'recognition') 🏆
                            @elseif($reward->category === 'events') 🎉
                            @elseif(str_starts_with($reward->category, 'merchandise')) 🎁
                            @else 🌟
                            @endif
                        </div>
                    </div>

                    <div class="p-6 pt-8">
                        <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $reward->name }}</h3>
                        <p class="text-sm text-gray-600 mb-4">{{ $reward->description }}</p>

                        {{-- Points Cost --}}
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2">
                                <span class="text-xl font-bold text-amber-600">{{ $reward->points_cost }}</span>
                                <span class="text-sm text-gray-500">points</span>
                            </div>
                            @if($reward->valid_until)
                                <span class="text-xs text-gray-400">Valid until {{ $reward->valid_until->format('d M Y') }}</span>
                            @endif
                        </div>

                        {{-- Redeem Button --}}
                        <form action="{{ route('gamification.redeem', $reward) }}" method="POST">
                            @csrf
                            <button 
                                type="submit"
                                @unless($userPoints >= $reward->points_cost) disabled @endunless
                                class="w-full py-3 rounded-xl font-semibold transition-all duration-300
                                    @if($userPoints >= $reward->points_cost)
                                        bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white shadow-lg hover:shadow-xl
                                    @else
                                        bg-gray-100 text-gray-400 cursor-not-allowed
                                    @endif
                                "
                            >
                                @if($userPoints >= $reward->points_cost)
                                    Redeem Now
                                @else
                                    Need {{ number_format($reward->points_cost - $userPoints) }} more points
                                @endif
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                        <span class="text-4xl">🎁</span>
                    </div>
                    <p class="text-gray-500">No rewards available in this category.</p>
                </div>
            @endforelse
        </div>

        {{-- My Recent Redemptions --}}
        @if(count($myRedemptions) > 0)
            <div class="mt-12">
                <h2 class="text-xl font-bold text-gray-900 mb-6">My Redemptions</h2>
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    {{-- Desktop Table --}}
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reward</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Points</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Claim Code</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($myRedemptions as $redemption)
                                    <tr>
                                        <td class="px-6 py-4 font-medium text-gray-900">{{ $redemption->reward->name }}</td>
                                        <td class="px-6 py-4 text-red-600">-{{ number_format($redemption->points_spent) }}</td>
                                        <td class="px-6 py-4 text-gray-500">{{ $redemption->redeemed_at->format('d M Y') }}</td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                                @if($redemption->status === 'claimed') bg-emerald-100 text-emerald-700
                                                @elseif($redemption->status === 'pending') bg-amber-100 text-amber-700
                                                @else bg-red-100 text-red-700
                                                @endif
                                            ">{{ ucfirst($redemption->status) }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($redemption->isCertificate() && $redemption->status === 'claimed')
                                                <a href="{{ route('gamification.certificate.download', $redemption) }}" class="px-3 py-1.5 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700">
                                                    Download Certificate
                                                </a>
                                            @elseif($redemption->isPriorityRegistration() && $redemption->status === 'claimed' && !$redemption->isConsumed())
                                                <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">Priority Active</span>
                                            @elseif($redemption->isPriorityRegistration() && $redemption->isConsumed())
                                                <span class="text-xs text-gray-500">Used for event #{{ $redemption->used_for_event_id }}</span>
                                            @elseif($redemption->claim_code)
                                                <span class="font-mono text-sm">{{ $redemption->claim_code }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile Cards --}}
                    <div class="md:hidden divide-y divide-gray-200">
                        @foreach($myRedemptions as $redemption)
                            <div class="p-4 space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="font-medium text-gray-900 text-sm">{{ $redemption->reward->name }}</span>
                                    <span class="text-red-600 font-semibold text-sm">-{{ number_format($redemption->points_spent) }} pts</span>
                                </div>
                                <div class="flex items-center justify-between text-xs text-gray-500">
                                    <span>{{ $redemption->redeemed_at->format('d M Y') }}</span>
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                        @if($redemption->status === 'claimed') bg-emerald-100 text-emerald-700
                                        @elseif($redemption->status === 'pending') bg-amber-100 text-amber-700
                                        @else bg-red-100 text-red-700
                                        @endif
                                    ">{{ ucfirst($redemption->status) }}</span>
                                </div>
                                @if($redemption->isCertificate() && $redemption->status === 'claimed')
                                    <a href="{{ route('gamification.certificate.download', $redemption) }}" class="block text-center px-3 py-1.5 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700">
                                        Download Certificate
                                    </a>
                                @elseif($redemption->isPriorityRegistration() && $redemption->status === 'claimed' && !$redemption->isConsumed())
                                    <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">Priority Active</span>
                                @elseif($redemption->isPriorityRegistration() && $redemption->isConsumed())
                                    <span class="text-xs text-gray-500">Used for event #{{ $redemption->used_for_event_id }}</span>
                                @elseif($redemption->claim_code)
                                    <p class="text-xs text-gray-400 font-mono">Code: {{ $redemption->claim_code }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                @if($myRedemptions->hasPages())
                    <div class="px-6 py-3 border-t border-gray-200 flex justify-center">
                        {{ $myRedemptions->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection


