@extends('layouts.app')

@section('back', '/dashboard')

@section('title', __('islamic.navigation.transparency'))

@section('content')

<!-- STEP 1: Page Header -->
<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
        <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
        </svg>
        Financial Transparency (Sidq)
    </h1>
    <p class="text-gray-600 mt-2">Track donations and expenses for {{ date('F Y') }}.</p>
</div>

<!-- STEP 2: Main Content Card -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden p-4 sm:p-6 md:p-8">

    <!-- Inflow -->
    <p class="text-sm font-bold text-gray-600 uppercase tracking-wide mb-3">Cash Inflow — {{ date('F Y') }} (Month / Year)</p>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-[#FDF6E3] border border-[#C5A059]/30 rounded-xl p-5 text-center shadow-sm">
            <p class="text-[#C5A059] font-semibold uppercase text-xs mb-1 tracking-wide">Zakat</p>
            <p class="text-lg font-bold text-[#C5A059] mt-1">RM {{ number_format($zakatMonth, 0) }} / RM {{ number_format($zakatYear, 0) }}</p>
        </div>
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-5 text-center shadow-sm">
            <p class="text-amber-600 font-semibold uppercase text-xs mb-1 tracking-wide">Zakat Fitr</p>
            <p class="text-lg font-bold text-amber-700 mt-1">RM {{ number_format($zakatFitrMonth, 0) }} / RM {{ number_format($zakatFitrYear, 0) }}</p>
        </div>
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-5 text-center shadow-sm">
            <p class="text-blue-600 font-semibold uppercase text-xs mb-1 tracking-wide">Sadaqah</p>
            <p class="text-lg font-bold text-blue-700 mt-1">RM {{ number_format($sadaqahMonth, 0) }} / RM {{ number_format($sadaqahYear, 0) }}</p>
        </div>
        <div class="bg-purple-50 border border-purple-200 rounded-xl p-5 text-center shadow-sm">
            <p class="text-purple-600 font-semibold uppercase text-xs mb-1 tracking-wide">Waqf</p>
            <p class="text-lg font-bold text-purple-700 mt-1">RM {{ number_format($waqfMonth, 0) }} / RM {{ number_format($waqfYear, 0) }}</p>
        </div>
    </div>

    <!-- Outflow -->
    <p class="text-sm font-bold text-gray-600 uppercase tracking-wide mb-3">Cash Outflow (Expenses — This Year)</p>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-red-50 border border-red-200 rounded-xl p-5 text-center shadow-sm">
            <p class="text-red-600 font-semibold uppercase text-xs mb-1 tracking-wide">Zakat Spent</p>
            <p class="text-2xl font-bold text-red-800 mt-1">- RM {{ number_format($zakatSpentYear, 2) }}</p>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-xl p-5 text-center shadow-sm">
            <p class="text-red-600 font-semibold uppercase text-xs mb-1 tracking-wide">Zakat Fitr Spent</p>
            <p class="text-2xl font-bold text-red-800 mt-1">- RM {{ number_format($zakatFitrSpentYear, 2) }}</p>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-xl p-5 text-center shadow-sm">
            <p class="text-red-600 font-semibold uppercase text-xs mb-1 tracking-wide">Sadaqah Spent</p>
            <p class="text-2xl font-bold text-red-800 mt-1">- RM {{ number_format($sadaqahSpentYear, 2) }}</p>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-xl p-5 text-center shadow-sm">
            <p class="text-red-600 font-semibold uppercase text-xs mb-1 tracking-wide">Waqf Spent</p>
            <p class="text-2xl font-bold text-red-800 mt-1">- RM {{ number_format($waqfSpentYear, 2) }}</p>
        </div>
    </div>

    <!-- Expenses Table -->
    <p class="text-sm font-bold text-gray-600 uppercase tracking-wide mb-3">Approved Withdrawals</p>
    <div id="expenses-table" class="rounded-lg border border-gray-200">
        {{-- Desktop Table --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Approved</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purpose</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount (RM)</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($expenses as $expense)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $expense->approved_at ? $expense->approved_at->format('d M Y') : '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $expense->purpose }}</td>
                        <td class="px-6 py-4 text-right whitespace-nowrap text-sm font-bold text-red-600">- RM {{ number_format($expense->amount, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center text-gray-400"><p class="text-sm">No expenses recorded this year.</p></td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Cards --}}
        <div class="md:hidden divide-y divide-gray-200">
            @forelse($expenses as $expense)
            <div class="p-4 space-y-1.5">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-500">{{ $expense->approved_at ? $expense->approved_at->format('d M Y') : '-' }}</span>
                    <span class="text-sm font-bold text-red-600">- RM {{ number_format($expense->amount, 2) }}</span>
                </div>
                <p class="text-sm text-gray-900">{{ $expense->purpose }}</p>
            </div>
            @empty
            <div class="p-8 text-center text-gray-400 text-sm">No expenses recorded this year.</div>
            @endforelse
        </div>
    </div>

    @if($expenses->hasPages())
    <div class="px-6 py-3 bg-gray-50 border-t border-gray-100">
        {{ $expenses->links() }}
    </div>
    @endif

    <div class="mt-8 pt-6 border-t border-gray-100 text-center text-xs text-gray-400">
        <p>* Donation figures are based on recorded donation date. Expenses are based on approval date.</p>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('page')) {
            const table = document.getElementById('expenses-table');
            if (table) {
                setTimeout(() => {
                    table.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 100);
            }
        }
    });
</script>

@endsection

