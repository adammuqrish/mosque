@extends('layouts.app')

@section('back', route('gamification.dashboard'))

@php
    $breadcrumbs = [
        ['label' => __('islamic.navigation.gamification'), 'url' => route('gamification.dashboard')],
        ['label' => 'My Redemptions'],
    ];
@endphp

@section('content')
<div class="min-h-screen bg-gradient-to-br from-amber-50 via-white to-orange-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">My Redemptions</h1>
                <p class="text-gray-600 mt-1">Track the rewards you have redeemed with your points</p>
            </div>
            <a href="{{ route('gamification.rewards') }}"
               class="mt-4 md:mt-0 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-semibold shadow-lg hover:from-emerald-600 hover:to-teal-600 transition-all">
                <span>🎁</span> Browse Rewards
            </a>
        </div>

        {{-- Redemptions List --}}
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            @if($redemptions->count() > 0)
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
                            @foreach($redemptions as $redemption)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 font-medium text-gray-900">{{ $redemption->reward->name }}</td>
                                    <td class="px-6 py-4 text-red-600">-{{ number_format($redemption->points_spent) }}</td>
                                    <td class="px-6 py-4 text-gray-500">{{ $redemption->redeemed_at->format('d M Y H:i') }}</td>
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
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Mobile Cards --}}
                <div class="md:hidden divide-y divide-gray-200">
                    @foreach($redemptions as $redemption)
                        <div class="p-4 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="font-medium text-gray-900 text-sm">{{ $redemption->reward->name }}</span>
                                <span class="text-red-600 font-semibold text-sm">-{{ number_format($redemption->points_spent) }} pts</span>
                            </div>
                            <div class="flex items-center justify-between text-xs text-gray-500">
                                <span>{{ $redemption->redeemed_at->format('d M Y H:i') }}</span>
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
            @else
                <div class="text-center py-16">
                    <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                        <span class="text-4xl">🎁</span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">No Redemptions Yet</h3>
                    <p class="text-gray-500 mb-6">You have not redeemed any rewards yet. Browse the catalog and spend your points!</p>
                    <a href="{{ route('gamification.rewards') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-semibold shadow-lg hover:from-emerald-600 hover:to-teal-600 transition-all">
                        <span>🎁</span> Browse Rewards
                    </a>
                </div>
            @endif
        </div>

        @if($redemptions->hasPages())
            <div class="mt-6 flex justify-center">
                {{ $redemptions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
