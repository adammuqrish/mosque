@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-slate-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-4 sm:px-6 lg:px-8">

        {{-- Back --}}
        <a href="{{ route('admin.gamification.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 transition font-medium mb-6">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back
        </a>

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Point Transactions</h1>
                <p class="text-gray-600 mt-1">{{ $user->name }} - {{ $user->email }}</p>
            </div>
        </div>

        {{-- Transactions Table --}}
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            {{-- Desktop Table --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Points</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Source</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($transactions as $tx)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $tx->created_at->format('d M Y, H:i') }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize
                                        @if($tx->type === 'earned') bg-emerald-100 text-emerald-700
                                        @elseif($tx->type === 'redeemed') bg-red-100 text-red-700
                                        @elseif($tx->type === 'refunded') bg-blue-100 text-blue-700
                                        @else bg-gray-100 text-gray-700
                                        @endif
                                    ">
                                        {{ $tx->type }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-bold {{ $tx->points > 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                        {{ $tx->points > 0 ? '+' : '' }}{{ number_format($tx->points) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    {{ number_format($tx->balance_after) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate" title="{{ $tx->reason }}">
                                    {{ $tx->reason }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    @if($tx->admin_id)
                                        <span class="text-blue-600">Admin</span>
                                    @else
                                        {{ $tx->source_type ?? '-' }}
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    No transactions found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="md:hidden divide-y divide-gray-200">
                @forelse($transactions as $tx)
                    <div class="p-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">{{ $tx->created_at->format('d M Y, H:i') }}</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize
                                @if($tx->type === 'earned') bg-emerald-100 text-emerald-700
                                @elseif($tx->type === 'redeemed') bg-red-100 text-red-700
                                @elseif($tx->type === 'refunded') bg-blue-100 text-blue-700
                                @else bg-gray-100 text-gray-700
                                @endif
                            ">
                                {{ $tx->type }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="font-bold {{ $tx->points > 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                {{ $tx->points > 0 ? '+' : '' }}{{ number_format($tx->points) }}
                            </span>
                            <span class="text-gray-600">Balance: {{ number_format($tx->balance_after) }}</span>
                        </div>
                        <div class="text-sm text-gray-600">
                            {{ $tx->reason }}
                        </div>
                        <div class="text-sm text-gray-500">
                            Source:
                            @if($tx->admin_id)
                                <span class="text-blue-600">Admin</span>
                            @else
                                {{ $tx->source_type ?? '-' }}
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center text-gray-500">
                        No transactions found
                    </div>
                @endforelse
            </div>

            @if($transactions->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection



