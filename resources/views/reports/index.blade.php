@extends('layouts.app')

@section('title', 'Financial Report')

@section('content')

    <!-- 1. FILTER SECTION -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Generate Monthly Report</h2>

        <form action="{{ route('reports.index') }}" method="GET" class="flex flex-wrap items-end gap-4">

            <!-- Month Select -->
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Month</label>
                <select name="month" class="border rounded px-3 py-2">
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                        </option>
                    @endfor
                </select>
            </div>

            <!-- Year Select -->
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Year</label>
                <select name="year" class="border rounded px-3 py-2">
                    @for($i = 2024; $i <= date('Y') + 1; $i++)
                        <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>
                            {{ $i }}
                        </option>
                    @endfor
                </select>
            </div>

            <!-- Button -->
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-6 rounded shadow">
                Generate Report
            </button>

            <!-- Print Button -->
            <button type="button" onclick="window.print()"
                class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded shadow">
                Print / Save PDF
            </button>
        </form>
    </div>

    <!-- 2. SUMMARY CARDS (Financial Snapshot) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

        <!-- Total In -->
        <div class="bg-green-50 border-l-4 border-green-500 p-6 rounded shadow-sm">
            <p class="text-green-600 font-bold uppercase text-xs">Total Donations (In)</p>
            <p class="text-3xl font-bold text-green-800">RM {{ number_format($totalDonations, 2) }}</p>
        </div>

        <!-- Total Out -->
        <div class="bg-red-50 border-l-4 border-red-500 p-6 rounded shadow-sm">
            <p class="text-red-600 font-bold uppercase text-xs">Total Withdrawals (Out)</p>
            <p class="text-3xl font-bold text-red-800">RM {{ number_format($totalWithdrawals, 2) }}</p>
        </div>

        <!-- Balance -->
        <div class="bg-blue-50 border-l-4 border-blue-500 p-6 rounded shadow-sm">
            <p class="text-blue-600 font-bold uppercase text-xs">Net Balance</p>
            <p class="text-3xl font-bold text-blue-800">RM {{ number_format($balance, 2) }}</p>
        </div>

    </div>

    <!-- 3. REPORT TABLES -->

    <!-- Donations Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
        <div class="bg-gray-50 px-6 py-4 border-b flex justify-between items-center">
            <h3 class="font-bold text-gray-800">Donation Details ({{ $monthName }} {{ $year }})</h3>
        </div>
        <div class="p-6 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Source</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Recorded By</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 text-sm">
                    @forelse($donations as $donation)
                        <tr>
                            <td class="px-4 py-3">{{ $donation->donation_date->format('d M Y') }}</td>
                            <td class="px-4 py-3">{{ $donation->category }}</td>
                            <td class="px-4 py-3">{{ ucfirst($donation->source) }}</td>
                            <td class="px-4 py-3">{{ $donation->user->name }}</td>
                            <td class="px-4 py-3 text-right font-bold text-green-700">+ RM
                                {{ number_format($donation->amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-gray-400">No donations this month.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Withdrawals Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b flex justify-between items-center">
            <h3 class="font-bold text-gray-800">Approved Withdrawals ({{ $monthName }} {{ $year }})</h3>
        </div>
        <div class="p-6 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Purpose</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Requested By</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Approved By</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 text-sm">
                    @forelse($withdrawals as $wd)
                        <tr>
                            <td class="px-4 py-3">{{ $wd->created_at->format('d M Y') }}</td>
                            <td class="px-4 py-3">{{ $wd->purpose }}</td>
                            <td class="px-4 py-3">{{ $wd->requester->name }}</td>
                            <td class="px-4 py-3">{{ $wd->approver->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-right font-bold text-red-700">- RM {{ number_format($wd->amount, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-gray-400">No approved withdrawals this month.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection