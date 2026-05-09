@extends('layouts.app')

@section('title', 'Financial Transparency')

@section('content')

    <!-- HEADER -->
    <div class="bg-blue-600 rounded-t-lg p-6 text-center">
        <h1 class="text-2xl font-bold text-white">Mosque Financial Transparency</h1>
        <p class="text-blue-100 text-sm mt-1">Track donations (In) and expenses (Out) in real-time.</p>
    </div>

    <div class="bg-white rounded-b-lg shadow-lg p-6 md:p-8">

        <!-- FILTER SECTION -->
        <div class="mb-8 bg-gray-50 p-4 rounded-lg border border-gray-200">
            <h3 class="text-sm font-bold text-gray-700 uppercase mb-3">Custom Date Range</h3>
            <form action="{{ route('transparency.index') }}" method="GET" class="flex flex-col md:flex-row items-end gap-4">
                <div class="w-full">
                    <label class="block text-xs font-bold text-gray-600 mb-1">From Date</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}"
                        class="border rounded px-3 py-2 w-full">
                </div>
                <div class="w-full">
                    <label class="block text-xs font-bold text-gray-600 mb-1">To Date</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}"
                        class="border rounded px-3 py-2 w-full">
                </div>
                <button type="submit" class="bg-gray-700 hover:bg-gray-800 text-white font-bold py-2 px-6 rounded text-sm">
                    Filter Data
                </button>
                @if(request('start_date'))
                    <a href="{{ route('transparency.index') }}"
                        class="bg-red-500 text-white font-bold py-2 px-6 rounded text-sm hover:underline">Reset Filter</a>
                @endif
            </form>
        </div>

        <!-- STATS CARDS GRID -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

            <!-- CARD 1: TODAY -->
            <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-5 text-center">
                <p class="text-emerald-600 font-bold uppercase text-xs mb-1">Today's Collection</p>
                <p class="text-2xl font-extrabold text-emerald-800">RM {{ number_format($donationToday, 2) }}</p>
            </div>

            <!-- CARD 2: MONTH -->
            <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-5 text-center">
                <p class="text-emerald-600 font-bold uppercase text-xs mb-1">This Month</p>
                <p class="text-2xl font-extrabold text-emerald-800">RM {{ number_format($donationMonth, 2) }}</p>
            </div>

            <!-- CARD 3: YEAR / CUSTOM RANGE -->
            @if($isFilterActive)
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-5 text-center relative overflow-hidden">
                    <div class="absolute top-0 right-0 bg-blue-200 text-blue-800 text-[10px] px-2 py-1 rounded-bl font-bold">
                        FILTERED</div>
                    <p class="text-blue-600 font-bold uppercase text-xs mb-1">Custom Range Total</p>
                    <p class="text-2xl font-extrabold text-blue-800">RM {{ number_format($customRangeTotal, 2) }}</p>
                </div>
            @else
                <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-5 text-center">
                    <p class="text-emerald-600 font-bold uppercase text-xs mb-1">This Year</p>
                    <p class="text-2xl font-extrabold text-emerald-800">RM {{ number_format($donationYear, 2) }}</p>
                </div>
            @endif

            <!-- CARD 4: EXPENSES -->
            <div class="bg-red-50 border border-red-200 rounded-lg p-5 text-center">
                <p class="text-red-600 font-bold uppercase text-xs mb-1">Total Spent @if($isFilterActive) (Filtered) @endif
                </p>
                <p class="text-2xl font-extrabold text-red-800">- RM {{ number_format($totalSpent, 2) }}</p>
            </div>

        </div>

        <!-- EXPENSES TABLE -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date Approved</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Purpose</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount (RM)</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($expenses as $expense)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $expense->approved_at ? $expense->approved_at->format('d M Y') : '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $expense->purpose }}
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap text-sm font-bold text-red-600">
                                - {{ number_format($expense->amount, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-10 text-center text-gray-400">
                                <p>No expenses recorded @if($isFilterActive) for this date range @endif.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-8 text-center text-xs text-gray-400">
            <p>* Donation figures are calculated based on the recorded donation date. Expenses are based on approval date.
            </p>
            @if($isFilterActive)
                <p class="mt-1 font-bold text-gray-600">Showing data for selected date range.</p>
            @endif
        </div>

    </div>

@endsection