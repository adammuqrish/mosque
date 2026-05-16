@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-amber-50 via-white to-orange-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Reward Management</h1>
                <p class="text-gray-600 mt-1">Create, edit, and manage reward catalog</p>
            </div>
            <div class="mt-4 md:mt-0">
                <a href="{{ route('admin.gamification.rewards.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Create Reward
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
            <a href="{{ route('admin.gamification.rewards.index') }}" class="px-4 py-2 bg-emerald-600 text-white rounded-lg font-medium">
                Rewards
            </a>
            <a href="{{ route('admin.gamification.redemptions') }}" class="px-4 py-2 bg-white text-gray-700 rounded-lg font-medium hover:bg-gray-50">
                Redemptions
            </a>
            <a href="{{ route('admin.gamification.tiers.index') }}" class="px-4 py-2 bg-white text-gray-700 rounded-lg font-medium hover:bg-gray-50">
                Tiers
            </a>
        </div>

        {{-- Rewards Table --}}
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            {{-- Desktop Table --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'code', 'direction' => $sort === 'code' && $direction === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-700">
                                    Code
                                    @if($sort === 'code')
                                        <svg class="w-4 h-4 {{ $direction === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'direction' => $sort === 'name' && $direction === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-700">
                                    Name
                                    @if($sort === 'name')
                                        <svg class="w-4 h-4 {{ $direction === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'category', 'direction' => $sort === 'category' && $direction === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-700">
                                    Category
                                    @if($sort === 'category')
                                        <svg class="w-4 h-4 {{ $direction === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'points_cost', 'direction' => $sort === 'points_cost' && $direction === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-700">
                                    Points Cost
                                    @if($sort === 'points_cost')
                                        <svg class="w-4 h-4 {{ $direction === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'stock_quantity', 'direction' => $sort === 'stock_quantity' && $direction === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-700">
                                    Stock
                                    @if($sort === 'stock_quantity')
                                        <svg class="w-4 h-4 {{ $direction === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'is_active', 'direction' => $sort === 'is_active' && $direction === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-700">
                                    Status
                                    @if($sort === 'is_active')
                                        <svg class="w-4 h-4 {{ $direction === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($rewards as $reward)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        @if($reward->image_url)
                                            <img src="{{ $reward->image_url }}" alt="{{ $reward->name }}" class="w-10 h-10 object-contain rounded-lg border border-gray-200">
                                        @elseif($reward->is_raw_svg)
                                            <span class="w-10 h-10 flex items-center justify-center text-2xl">{!! $reward->image_svg !!}</span>
                                        @else
                                            <span class="w-10 h-10 flex items-center justify-center text-2xl">🎁</span>
                                        @endif
                                        <code class="bg-gray-100 px-2 py-1 rounded font-mono text-sm">{{ $reward->code }}</code>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-medium text-gray-900">{{ $reward->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $reward->name_my }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium capitalize whitespace-nowrap
                                        @if($reward->category === 'priority') bg-blue-100 text-blue-700
                                        @elseif($reward->category === 'recognition') bg-purple-100 text-purple-700
                                        @elseif($reward->category === 'privilege') bg-amber-100 text-amber-700
                                        @else bg-green-100 text-green-700
                                        @endif
                                    ">
                                        {{ ucfirst($reward->category) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-semibold text-emerald-600">{{ number_format($reward->points_cost) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($reward->stock_quantity !== null)
                                        <span class="{{ $reward->stock_quantity <= 0 ? 'text-red-600' : 'text-gray-900' }}">
                                            {{ $reward->stock_quantity }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">Unlimited</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium
                                        @if($reward->is_active) bg-emerald-100 text-emerald-700
                                        @else bg-red-100 text-red-700
                                        @endif
                                    ">
                                        {{ $reward->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.gamification.rewards.edit', $reward) }}"
                                           class="px-3 py-1.5 bg-blue-100 text-blue-700 rounded-lg text-sm font-medium hover:bg-blue-200 transition whitespace-nowrap">
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.gamification.rewards.toggle', $reward) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="px-3 py-1.5 rounded-lg text-sm font-medium transition whitespace-nowrap
                                                        @if($reward->is_active) bg-red-100 text-red-700 hover:bg-red-200
                                                        @else bg-emerald-100 text-emerald-700 hover:bg-emerald-200
                                                        @endif
                                                    ">
                                                {{ $reward->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                                        <span class="text-4xl">🎁</span>
                                    </div>
                                    <p class="text-gray-500 mb-4">No rewards created yet.</p>
                                    <a href="{{ route('admin.gamification.rewards.create') }}"
                                       class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        Create First Reward
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="md:hidden divide-y divide-gray-200">
                @forelse($rewards as $reward)
                    <div class="p-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                @if($reward->image_url)
                                    <img src="{{ $reward->image_url }}" alt="{{ $reward->name }}" class="w-8 h-8 object-contain rounded-lg border border-gray-200">
                                @elseif($reward->is_raw_svg)
                                    <span class="w-8 h-8 flex items-center justify-center text-xl">{!! $reward->image_svg !!}</span>
                                @else
                                    <span class="w-8 h-8 flex items-center justify-center text-xl">🎁</span>
                                @endif
                                <code class="bg-gray-100 px-2 py-0.5 rounded font-mono text-xs">{{ $reward->code }}</code>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                @if($reward->is_active) bg-emerald-100 text-emerald-700
                                @else bg-red-100 text-red-700
                                @endif
                            ">
                                {{ $reward->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $reward->name }}</p>
                            <p class="text-xs text-gray-500">{{ $reward->name_my }}</p>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium capitalize
                                @if($reward->category === 'priority') bg-blue-100 text-blue-700
                                @elseif($reward->category === 'recognition') bg-purple-100 text-purple-700
                                @elseif($reward->category === 'privilege') bg-amber-100 text-amber-700
                                @else bg-green-100 text-green-700
                                @endif
                            ">
                                {{ ucfirst($reward->category) }}
                            </span>
                            <span class="font-semibold text-emerald-600">{{ number_format($reward->points_cost) }} pts</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">
                                Stock:
                                @if($reward->stock_quantity !== null)
                                    <span class="{{ $reward->stock_quantity <= 0 ? 'text-red-600' : 'text-gray-900' }}">{{ $reward->stock_quantity }}</span>
                                @else
                                    <span class="text-gray-400">Unlimited</span>
                                @endif
                            </span>
                        </div>
                        <div class="flex gap-2 pt-1">
                            <a href="{{ route('admin.gamification.rewards.edit', $reward) }}"
                               class="flex-1 text-center px-3 py-2 bg-blue-100 text-blue-700 rounded-lg text-sm font-medium hover:bg-blue-200 transition">
                                Edit
                            </a>
                            <form action="{{ route('admin.gamification.rewards.toggle', $reward) }}" method="POST" class="flex-1">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="w-full px-3 py-2 rounded-lg text-sm font-medium transition
                                            @if($reward->is_active) bg-red-100 text-red-700 hover:bg-red-200
                                            @else bg-emerald-100 text-emerald-700 hover:bg-emerald-200
                                            @endif
                                        ">
                                    {{ $reward->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center text-gray-500">
                        No rewards created yet.
                    </div>
                @endforelse
            </div>

            @if($rewards->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $rewards->appends(request()->except('page'))->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection


