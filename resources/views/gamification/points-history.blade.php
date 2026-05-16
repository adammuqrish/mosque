@extends('layouts.app')

@section('back', '/dashboard')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-slate-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Points History</h1>
            <p class="text-gray-600 mt-1">Track all your point earnings and redemptions</p>
        </div>

        {{-- Transactions List --}}
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
            @forelse($transactions as $transaction)
                <div class="flex items-center gap-4 p-4 border-b border-gray-100 last:border-b-0 hover:bg-gray-50 transition-colors">
                    
                    {{-- Icon --}}
                    <div class="w-12 h-12 rounded-full flex items-center justify-center
                        @if($transaction->type === 'earned') bg-emerald-100 text-emerald-600
                        @elseif($transaction->type === 'redeemed') bg-red-100 text-red-600
                        @elseif($transaction->type === 'refunded') bg-blue-100 text-blue-600
                        @else bg-gray-100 text-gray-600
                        @endif
                    ">
                        @if($transaction->type === 'earned')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                        @elseif($transaction->type === 'redeemed')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                            </svg>
                        @else
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        @endif
                    </div>

                    {{-- Details --}}
                    <div class="flex-1">
                        <p class="font-medium text-gray-900">{{ $transaction->reason }}</p>
                        <p class="text-sm text-gray-500">
                            {{ $transaction->created_at->format('d M Y, H:i') }}
                            @if($transaction->admin_id)
                                <span class="ml-2 text-xs">(Admin adjusted)</span>
                            @endif
                        </p>
                    </div>

                    {{-- Points Change --}}
                    <div class="text-right">
                        <p class="text-lg font-bold {{ $transaction->points > 0 ? 'text-emerald-600' : 'text-red-600' }}">
                            {{ $transaction->points > 0 ? '+' : '' }}{{ number_format($transaction->points) }}
                        </p>
                        <p class="text-xs text-gray-500">Balance: {{ number_format($transaction->balance_after) }}</p>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                        <span class="text-4xl">📜</span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No transactions yet</h3>
                    <p class="text-gray-500">Complete volunteer events to start earning points!</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($transactions->hasPages())
            <div class="mt-6">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

