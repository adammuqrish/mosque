@extends('layouts.app')

@section('back', '/dashboard')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-amber-50 via-white to-orange-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Pending Redemptions</h1>
                <p class="text-gray-600 mt-1">Review and fulfill reward redemption requests</p>
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
            <a href="{{ route('admin.gamification.redemptions') }}" class="px-4 py-2 bg-emerald-600 text-white rounded-lg font-medium">
                Redemptions
            </a>
            <a href="{{ route('admin.gamification.tiers.index') }}" class="px-4 py-2 bg-white text-gray-700 rounded-lg font-medium hover:bg-gray-50">
                Tiers
            </a>
        </div>

        {{-- Pending Redemptions Table --}}
        <div id="redemptions-table" class="bg-white rounded-2xl shadow-xl overflow-hidden">
            {{-- Desktop Table --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reward</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Points Spent</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Claim Code</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requested</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($redemptions as $redemption)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        @if($redemption->user->avatar_url)
                                            <img src="{{ $redemption->user->avatar_url }}" alt="{{ $redemption->user->name }}" class="w-10 h-10 rounded-full object-cover">
                                        @else
                                            <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center font-bold text-emerald-700">
                                                {{ $redemption->user->initials }}
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $redemption->user->name }}</p>
                                            <p class="text-sm text-gray-500">{{ $redemption->user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-medium text-gray-900">{{ $redemption->reward->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $redemption->reward->category }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-red-600 font-bold">-{{ number_format($redemption->points_spent) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <code class="bg-gray-100 px-2 py-1 rounded font-mono text-sm">{{ $redemption->claim_code }}</code>
                                </td>
                                <td class="px-6 py-4">
                                    {{ $redemption->redeemed_at->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($redemption->isCertificate())
                                        <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-medium">Auto-fulfilled</span>
                                    @else
                                        <div class="flex items-center gap-2">
                                            <form action="{{ route('admin.gamification.fulfill', $redemption) }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="action" value="fulfill">
                                                <button type="submit" class="px-3 py-1.5 bg-emerald-100 text-emerald-700 rounded-lg text-sm font-medium hover:bg-emerald-200 transition whitespace-nowrap">
                                                    Approve
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.gamification.fulfill', $redemption) }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="action" value="reject">
                                                <button type="submit" class="px-3 py-1.5 bg-red-100 text-red-700 rounded-lg text-sm font-medium hover:bg-red-200 transition whitespace-nowrap">
                                                    Reject
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                                        <span class="text-4xl">🎁</span>
                                    </div>
                                    <p class="text-gray-500">No pending redemptions!</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="md:hidden divide-y divide-gray-200">
                @forelse($redemptions as $redemption)
                    <div class="p-4 space-y-3">
                        <div class="flex items-center gap-3">
                            @if($redemption->user->avatar_url)
                                <img src="{{ $redemption->user->avatar_url }}" alt="{{ $redemption->user->name }}" class="w-10 h-10 rounded-full object-cover">
                            @else
                                <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center font-bold text-emerald-700">
                                    {{ $redemption->user->initials }}
                                </div>
                            @endif
                            <div>
                                <p class="font-medium text-gray-900">{{ $redemption->user->name }}</p>
                                <p class="text-sm text-gray-500">{{ $redemption->user->email }}</p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-900">{{ $redemption->reward->name }}</p>
                                <p class="text-xs text-gray-500">{{ $redemption->reward->category }}</p>
                            </div>
                            <span class="text-red-600 font-bold">-{{ number_format($redemption->points_spent) }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <code class="bg-gray-100 px-2 py-0.5 rounded font-mono text-xs">{{ $redemption->claim_code }}</code>
                            <span class="text-gray-500">{{ $redemption->redeemed_at->diffForHumans() }}</span>
                        </div>
                        <div class="flex gap-2 pt-1">
                            @if($redemption->isCertificate())
                                <span class="w-full text-center px-3 py-2 bg-blue-100 text-blue-700 rounded-lg text-sm font-medium">
                                    Auto-fulfilled
                                </span>
                            @else
                                <form action="{{ route('admin.gamification.fulfill', $redemption) }}" method="POST" class="flex-1">
                                    @csrf
                                    <input type="hidden" name="action" value="fulfill">
                                    <button type="submit" class="w-full px-3 py-2 bg-emerald-100 text-emerald-700 rounded-lg text-sm font-medium hover:bg-emerald-200 transition">
                                        Approve
                                    </button>
                                </form>
                                <form action="{{ route('admin.gamification.fulfill', $redemption) }}" method="POST" class="flex-1">
                                    @csrf
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit" class="w-full px-3 py-2 bg-red-100 text-red-700 rounded-lg text-sm font-medium hover:bg-red-200 transition">
                                        Reject
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center text-gray-500">
                        No pending redemptions!
                    </div>
                @endforelse
            </div>

            @if($redemptions->hasPages())
                <div id="redemptions-pagination" class="px-6 py-4 border-t border-gray-200">
                    {{ $redemptions->appends(request()->except('page'))->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection



