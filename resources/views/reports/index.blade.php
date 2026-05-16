@extends('layouts.app')

@section('back', '/dashboard')

@section('title', __('islamic.navigation.reports'))

@section('content')

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
            <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            Financial Reports
        </h1>
        <p class="text-gray-600 mt-2">View and export donation reports for {{ $reportType === 'yearly' ? $year : date('F', mktime(0, 0, 0, $month, 1)) }} {{ $year }}</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden p-4 sm:p-7 mb-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Select Report Period</h3>
                <form action="{{ route('reports.index') }}" method="GET" class="space-y-4">
                    <input type="hidden" name="tab" value="{{ $tab }}">
                    <div class="flex gap-2 mb-4">
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="report_type" value="monthly" class="peer sr-only" {{ $reportType !== 'yearly' ? 'checked' : '' }} onchange="this.form.submit()">
                            <div class="text-center py-2 px-4 rounded-lg border-2 border-gray-200 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 text-sm font-medium transition hover:bg-gray-50 peer-checked:text-emerald-700">
                                Monthly
                            </div>
                        </label>
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="report_type" value="yearly" class="peer sr-only" {{ $reportType === 'yearly' ? 'checked' : '' }} onchange="this.form.submit()">
                            <div class="text-center py-2 px-4 rounded-lg border-2 border-gray-200 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 text-sm font-medium transition hover:bg-gray-50 peer-checked:text-emerald-700">
                                Yearly
                            </div>
                        </label>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="{{ $reportType === 'yearly' ? 'col-span-2' : '' }}">
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ $reportType === 'yearly' ? 'Year' : 'Month' }}</label>
                            @if($reportType !== 'yearly')
                                <select name="month" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>
                                            {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                        </option>
                                    @endfor
                                </select>
                            @else
                                <select name="year" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                    @for($i = 2024; $i <= date('Y') + 1; $i++)
                                        <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                            @endif
                        </div>
                        @if($reportType !== 'yearly')
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Year</label>
                                <select name="year" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                    @for($i = 2024; $i <= date('Y') + 1; $i++)
                                        <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        @endif
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3 pt-2">
                        <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2.5 px-4 rounded-lg transition min-h-[44px]">
                            Generate Report
                        </button>
                        <!-- <button type="button" onclick="window.print()" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2.5 px-4 rounded-lg transition min-h-[44px]">
                            Print / PDF
                        </button> -->
                    </div>
                </form>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Quick Export</h3>
                <p class="text-sm text-gray-600 mb-4">Download reports for {{ $reportType === 'yearly' ? $year . ' (Full Year)' : date('F Y', mktime(0, 0, 0, $month, 1, $year)) }}</p>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false" class="w-full bg-green-50 hover:bg-green-100 text-green-700 font-medium py-3 px-2 sm:py-2.5 sm:px-3 rounded-lg border border-green-200 text-xs sm:text-sm transition flex items-center justify-center gap-1.5 sm:gap-2 min-h-[48px] sm:min-h-[44px]">
                            <svg class="w-5 h-5 sm:w-4 sm:h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>Donations</span>
                        </button>
                        <div x-show="open" x-transition class="absolute left-0 right-0 mt-1 bg-white rounded-lg shadow-lg border z-20 mx-1" style="display: none;">
                            <a href="{{ route('reports.export.donations.csv', ['month' => $month, 'year' => $year, 'report_type' => $reportType]) }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-green-50 rounded-t-lg min-h-[44px] flex items-center">CSV</a>
                            <a href="{{ route('reports.export.donations.pdf', ['month' => $month, 'year' => $year, 'report_type' => $reportType]) }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-green-50 rounded-b-lg min-h-[44px] flex items-center">PDF</a>
                        </div>
                    </div>

                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false" class="w-full bg-purple-50 hover:bg-purple-100 text-purple-700 font-medium py-3 px-2 sm:py-2.5 sm:px-3 rounded-lg border border-purple-200 text-xs sm:text-sm transition flex items-center justify-center gap-1.5 sm:gap-2 min-h-[48px] sm:min-h-[44px]">
                            <svg class="w-5 h-5 sm:w-4 sm:h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span>Events</span>
                        </button>
                        <div x-show="open" x-transition class="absolute left-0 right-0 mt-1 bg-white rounded-lg shadow-lg border z-20 mx-1" style="display: none;">
                            <a href="{{ route('reports.export.events.csv', ['month' => $month, 'year' => $year, 'report_type' => $reportType]) }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-purple-50 rounded-t-lg min-h-[44px] flex items-center">CSV</a>
                            <a href="{{ route('reports.export.events.pdf', ['month' => $month, 'year' => $year, 'report_type' => $reportType]) }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-purple-50 rounded-b-lg min-h-[44px] flex items-center">PDF</a>
                        </div>
                    </div>

                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false" class="w-full bg-yellow-50 hover:bg-yellow-100 text-yellow-700 font-medium py-3 px-2 sm:py-2.5 sm:px-3 rounded-lg border border-yellow-200 text-xs sm:text-sm transition flex items-center justify-center gap-1.5 sm:gap-2 min-h-[48px] sm:min-h-[44px]">
                            <svg class="w-5 h-5 sm:w-4 sm:h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                            <span>Attendance</span>
                        </button>
                        <div x-show="open" x-transition class="absolute left-0 right-0 mt-1 bg-white rounded-lg shadow-lg border z-20 mx-1" style="display: none;">
                            <a href="{{ route('reports.export.attendance.csv', ['month' => $month, 'year' => $year, 'report_type' => $reportType]) }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-yellow-50 rounded-t-lg min-h-[44px] flex items-center">CSV</a>
                            <a href="{{ route('reports.export.attendance.pdf', ['month' => $month, 'year' => $year, 'report_type' => $reportType]) }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-yellow-50 rounded-b-lg min-h-[44px] flex items-center">PDF</a>
                        </div>
                    </div>

                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false" class="w-full bg-blue-50 hover:bg-blue-100 text-blue-700 font-medium py-3 px-2 sm:py-2.5 sm:px-3 rounded-lg border border-blue-200 text-xs sm:text-sm transition flex items-center justify-center gap-1.5 sm:gap-2 min-h-[48px] sm:min-h-[44px]">
                            <svg class="w-5 h-5 sm:w-4 sm:h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>Financial</span>
                        </button>
                        <div x-show="open" x-transition class="absolute left-0 right-0 mt-1 bg-white rounded-lg shadow-lg border z-20 mx-1" style="display: none;">
                            <a href="{{ route('reports.export.financial.csv', ['month' => $month, 'year' => $year, 'report_type' => $reportType]) }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 rounded-t-lg min-h-[44px] flex items-center">CSV</a>
                            <a href="{{ route('reports.export.financial.pdf', ['month' => $month, 'year' => $year, 'report_type' => $reportType]) }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 rounded-b-lg min-h-[44px] flex items-center">PDF</a>
                        </div>
                    </div>

                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false" class="w-full bg-amber-50 hover:bg-amber-100 text-amber-700 font-medium py-3 px-2 sm:py-2.5 sm:px-3 rounded-lg border border-amber-200 text-xs sm:text-sm transition flex items-center justify-center gap-1.5 sm:gap-2 min-h-[48px] sm:min-h-[44px]">
                            <svg class="w-5 h-5 sm:w-4 sm:h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                            <span>Gamification</span>
                        </button>
                        <div x-show="open" x-transition class="absolute left-0 right-0 mt-1 bg-white rounded-lg shadow-lg border z-20 mx-1" style="display: none;">
                            <a href="{{ route('reports.export.gamification.csv', ['month' => $month, 'year' => $year, 'report_type' => $reportType]) }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-amber-50 rounded-t-lg min-h-[44px] flex items-center">CSV</a>
                            <a href="{{ route('reports.export.gamification.pdf', ['month' => $month, 'year' => $year, 'report_type' => $reportType]) }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-amber-50 rounded-b-lg min-h-[44px] flex items-center">PDF</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-6">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 overflow-x-auto" aria-label="Tabs">
                <a href="{{ route('reports.index', array_merge(request()->except(['tab', 'page']), ['tab' => 'donations'])) }}" class="{{ $tab === 'donations' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Donations
                </a>
                <a href="{{ route('reports.index', array_merge(request()->except(['tab', 'page']), ['tab' => 'events'])) }}" class="{{ $tab === 'events' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Events
                </a>
                <a href="{{ route('reports.index', array_merge(request()->except(['tab', 'page']), ['tab' => 'attendance'])) }}" class="{{ $tab === 'attendance' ? 'border-yellow-500 text-yellow-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Attendance
                </a>
                <a href="{{ route('reports.index', array_merge(request()->except(['tab', 'page']), ['tab' => 'financial'])) }}" class="{{ $tab === 'financial' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Financial
                </a>
                <a href="{{ route('reports.index', array_merge(request()->except(['tab', 'page']), ['tab' => 'withdrawals'])) }}" class="{{ $tab === 'withdrawals' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Withdrawals
                </a>
                <a href="{{ route('reports.index', array_merge(request()->except(['tab', 'page']), ['tab' => 'gamification'])) }}" class="{{ $tab === 'gamification' ? 'border-amber-500 text-amber-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Gamification
                </a>
            </nav>
        </div>
    </div>

    <div class="space-y-8">
        @if($tab === 'donations')
            <div id="donations-table" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-4 sm:px-6 py-4 border-b border-green-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Donations ({{ $monthName }} {{ $year }})
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">
                            Total: RM {{ number_format($totalDonations, 2) }}
                        </span>
                        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                            {{ $cashCount }} Cash
                        </span>
                        <span class="bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded-full">
                            {{ $onlineCount }} Online
                        </span>
                    </div>
                </div>
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'donation_date', 'direction' => $sortDonation === 'donation_date' && $directionDonation === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-700">
                                        Date
                                        @if($sortDonation === 'donation_date')
                                            <svg class="w-4 h-4 {{ $directionDonation === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'category', 'direction' => $sortDonation === 'category' && $directionDonation === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-700">
                                        Category
                                        @if($sortDonation === 'category')
                                            <svg class="w-4 h-4 {{ $directionDonation === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'source', 'direction' => $sortDonation === 'source' && $directionDonation === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-700">
                                        Source
                                        @if($sortDonation === 'source')
                                            <svg class="w-4 h-4 {{ $directionDonation === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'direction' => $sortDonation === 'created_at' && $directionDonation === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-700">
                                        Recorded By
                                        @if($sortDonation === 'created_at')
                                            <svg class="w-4 h-4 {{ $directionDonation === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-4 sm:px-6 py-3 text-right text-xs font-medium text-gray-600 uppercase">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'amount', 'direction' => $sortDonation === 'amount' && $directionDonation === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center justify-end gap-1 hover:text-gray-700">
                                        Amount
                                        @if($sortDonation === 'amount')
                                            <svg class="w-4 h-4 {{ $directionDonation === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                        @endif
                                    </a>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 text-sm">
                            @forelse($donations as $donation)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 sm:px-6 py-3 text-gray-800">{{ $donation->donation_date->format('d M Y') }}</td>
                                    <td class="px-4 sm:px-6 py-3 text-gray-800"><span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">{{ $donation->category }}</span></td>
                                    <td class="px-4 sm:px-6 py-3 text-gray-800">{{ ucfirst($donation->source) }}</td>
                                    <td class="px-4 sm:px-6 py-3 text-gray-800">{{ $donation->user->name }}</td>
                                    <td class="px-4 sm:px-6 py-3 text-right font-bold text-green-700">+ RM {{ number_format($donation->amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-6 text-gray-400">No donations for this period</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="md:hidden divide-y divide-gray-200">
                    @forelse($donations as $donation)
                        <div class="p-4 space-y-2">
                            <div class="flex justify-between items-start">
                                <span class="text-sm font-medium text-gray-900">{{ $donation->donation_date->format('d M Y') }}</span>
                                <span class="text-sm font-bold text-green-700">+ RM {{ number_format($donation->amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm text-gray-600">
                                <span><span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">{{ $donation->category }}</span></span>
                                <span>{{ ucfirst($donation->source) }}</span>
                            </div>
                            <div class="text-xs text-gray-500">Recorded by: {{ $donation->user->name }}</div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-gray-400 text-sm">No donations for this period</div>
                    @endforelse
                </div>
                @if($donations->hasPages())
                    <div class="px-4 sm:px-6 py-3 bg-gray-50 border-t border-gray-100">
                        {{ $donations->appends(request()->except('page'))->links() }}
                    </div>
                @endif
            </div>
        @endif

        @if($tab === 'events')
            <div id="events-table" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-purple-50 to-indigo-50 px-4 sm:px-6 py-4 border-b border-purple-200">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Events ({{ $monthName }} {{ $year }})
                    </h3>
                </div>
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                                    <a href="{{ request()->fullUrlWithQuery(['sort_event' => 'title', 'direction_event' => $sortEvent === 'title' && $directionEvent === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-700">
                                        Title
                                        @if($sortEvent === 'title')
                                            <svg class="w-4 h-4 {{ $directionEvent === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                                    <a href="{{ request()->fullUrlWithQuery(['sort_event' => 'event_date', 'direction_event' => $sortEvent === 'event_date' && $directionEvent === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-700">
                                        Date
                                        @if($sortEvent === 'event_date')
                                            <svg class="w-4 h-4 {{ $directionEvent === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                                    <a href="{{ request()->fullUrlWithQuery(['sort_event' => 'event_location', 'direction_event' => $sortEvent === 'event_location' && $directionEvent === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-700">
                                        Location
                                        @if($sortEvent === 'event_location')
                                            <svg class="w-4 h-4 {{ $directionEvent === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                                    <a href="{{ request()->fullUrlWithQuery(['sort_event' => 'status', 'direction_event' => $sortEvent === 'status' && $directionEvent === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-700">
                                        Status
                                        @if($sortEvent === 'status')
                                            <svg class="w-4 h-4 {{ $directionEvent === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-4 sm:px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase">Volunteers</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 text-sm">
                            @forelse($events as $event)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 sm:px-6 py-3 text-gray-800 font-medium">{{ $event->title }}</td>
                                    <td class="px-4 sm:px-6 py-3 text-gray-800">{{ $event->event_date->format('d M Y, h:i A') }}</td>
                                    <td class="px-4 sm:px-6 py-3 text-gray-800">{{ $event->event_location ?? '-' }}</td>
                                    <td class="px-4 sm:px-6 py-3">
                                        @if($event->status === 'open')
                                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Open</span>
                                        @elseif($event->status === 'closed')
                                            <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs">Closed</span>
                                        @elseif($event->status === 'cancelled')
                                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">Cancelled</span>
                                        @else
                                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">{{ ucfirst($event->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 sm:px-6 py-3 text-center">
                                        <span class="font-semibold text-purple-700">{{ $event->volunteers_count }}</span> / {{ $event->max_volunteers }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-6 text-gray-400">No events for this period</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="md:hidden divide-y divide-gray-200">
                    @forelse($events as $event)
                        <div class="p-4 space-y-2">
                            <div class="flex justify-between items-start">
                                <span class="text-sm font-medium text-gray-900">{{ $event->title }}</span>
                                <span class="text-xs">
                                    @if($event->status === 'open')
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Open</span>
                                    @elseif($event->status === 'closed')
                                        <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs">Closed</span>
                                    @elseif($event->status === 'cancelled')
                                        <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">Cancelled</span>
                                    @else
                                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">{{ ucfirst($event->status) }}</span>
                                    @endif
                                </span>
                            </div>
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>{{ $event->event_date->format('d M Y, h:i A') }}</span>
                                <span>{{ $event->event_location ?? '-' }}</span>
                            </div>
                            <div class="text-xs text-gray-500">
                                Capacity: <span class="font-semibold text-purple-700">{{ $event->volunteers_count }}</span> / {{ $event->max_volunteers }}
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-gray-400 text-sm">No events for this period</div>
                    @endforelse
                </div>
                @if($events->hasPages())
                    <div class="px-4 sm:px-6 py-3 bg-gray-50 border-t border-gray-100">
                        {{ $events->appends(request()->except('page'))->links() }}
                    </div>
                @endif
            </div>
        @endif

        @if($tab === 'attendance')
            <div id="attendance-table" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-yellow-50 to-amber-50 px-4 sm:px-6 py-4 border-b border-yellow-200">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Volunteer Attendance ({{ $monthName }} {{ $year }})
                    </h3>
                </div>
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                                    <a href="{{ request()->fullUrlWithQuery(['sort_attendance' => 'event_title', 'direction_attendance' => $sortAttendance === 'event_title' && $directionAttendance === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-700">
                                        Event
                                        @if($sortAttendance === 'event_title')
                                            <svg class="w-4 h-4 {{ $directionAttendance === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                                    <a href="{{ request()->fullUrlWithQuery(['sort_attendance' => 'event_date', 'direction_attendance' => $sortAttendance === 'event_date' && $directionAttendance === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-700">
                                        Event Date
                                        @if($sortAttendance === 'event_date')
                                            <svg class="w-4 h-4 {{ $directionAttendance === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                                    <a href="{{ request()->fullUrlWithQuery(['sort_attendance' => 'volunteer_name', 'direction_attendance' => $sortAttendance === 'volunteer_name' && $directionAttendance === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-700">
                                        Volunteer
                                        @if($sortAttendance === 'volunteer_name')
                                            <svg class="w-4 h-4 {{ $directionAttendance === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                                    <a href="{{ request()->fullUrlWithQuery(['sort_attendance' => 'email', 'direction_attendance' => $sortAttendance === 'email' && $directionAttendance === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-700">
                                        Email
                                        @if($sortAttendance === 'email')
                                            <svg class="w-4 h-4 {{ $directionAttendance === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                                    <a href="{{ request()->fullUrlWithQuery(['sort_attendance' => 'attendance_status', 'direction_attendance' => $sortAttendance === 'attendance_status' && $directionAttendance === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-700">
                                        Status
                                        @if($sortAttendance === 'attendance_status')
                                            <svg class="w-4 h-4 {{ $directionAttendance === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                        @endif
                                    </a>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 text-sm">
                            @forelse($attendance as $record)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 sm:px-6 py-3 text-gray-800 font-medium">{{ $record->event_title }}</td>
                                    <td class="px-4 sm:px-6 py-3 text-gray-800">{{ \Carbon\Carbon::parse($record->event_date)->format('d M Y') }}</td>
                                    <td class="px-4 sm:px-6 py-3 text-gray-800">{{ $record->volunteer_name }}</td>
                                    <td class="px-4 sm:px-6 py-3 text-gray-600">{{ $record->email }}</td>
                                    <td class="px-4 sm:px-6 py-3">
                                        @if($record->attendance_status === 'confirmed')
                                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">Confirmed</span>
                                        @elseif($record->attendance_status === 'completed')
                                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Completed</span>
                                        @elseif($record->attendance_status === 'absent')
                                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">Absent</span>
                                        @elseif($record->attendance_status === 'pending_review')
                                            <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">Pending Review</span>
                                        @else
                                            <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs">{{ ucfirst(str_replace('_', ' ', $record->attendance_status)) }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-6 text-gray-400">No attendance records for this period</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="md:hidden divide-y divide-gray-200">
                    @forelse($attendance as $record)
                        <div class="p-4 space-y-2">
                            <div class="flex justify-between items-start">
                                <span class="text-sm font-medium text-gray-900">{{ $record->event_title }}</span>
                                <span class="text-sm text-gray-600">{{ $record->volunteer_name }}</span>
                            </div>
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>{{ $record->email }}</span>
                                <span>
                                    @if($record->attendance_status === 'confirmed')
                                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">Confirmed</span>
                                    @elseif($record->attendance_status === 'completed')
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Completed</span>
                                    @elseif($record->attendance_status === 'absent')
                                        <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">Absent</span>
                                    @elseif($record->attendance_status === 'pending_review')
                                        <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">Pending Review</span>
                                    @else
                                        <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs">{{ ucfirst(str_replace('_', ' ', $record->attendance_status)) }}</span>
                                    @endif
                                </span>
                            </div>
                            <div class="text-xs text-gray-500">Joined: {{ \Carbon\Carbon::parse($record->event_date)->format('d M Y') }}</div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-gray-400 text-sm">No attendance records for this period</div>
                    @endforelse
                </div>
                @if($attendance->hasPages())
                    <div class="px-4 sm:px-6 py-3 bg-gray-50 border-t border-gray-100">
                        {{ $attendance->appends(request()->except('page'))->links() }}
                    </div>
                @endif
            </div>
        @endif

        @if($tab === 'financial')
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="bg-[#FDF6E3] border-l-4 border-[#C5A059] p-4 sm:p-6 rounded-xl shadow-sm">
                        <p class="text-[#C5A059] font-semibold text-xs uppercase tracking-wide">Zakat</p>
                        <p class="text-3xl font-bold text-[#C5A059] mt-1">In: RM {{ number_format($zakatDonations, 0) }}</p>
                        <p class="text-sm text-red-600 mt-1">Out: - RM {{ number_format($zakatWithdrawals, 0) }}</p>
                    </div>
                    <div class="bg-amber-50 border-l-4 border-amber-500 p-4 sm:p-6 rounded-xl shadow-sm">
                        <p class="text-amber-700 font-semibold text-xs uppercase tracking-wide">Zakat Fitr</p>
                        <p class="text-3xl font-bold text-amber-700 mt-1">In: RM {{ number_format($zakatFitrDonations, 0) }}</p>
                        <p class="text-sm text-red-600 mt-1">Out: RM 0</p>
                    </div>
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 sm:p-6 rounded-xl shadow-sm">
                        <p class="text-blue-700 font-semibold text-xs uppercase tracking-wide">Sadaqah</p>
                        <p class="text-3xl font-bold text-blue-700 mt-1">In: RM {{ number_format($sadaqahDonations, 0) }}</p>
                        <p class="text-sm text-red-600 mt-1">Out: - RM {{ number_format($sadaqahWithdrawals, 0) }}</p>
                    </div>
                    <div class="bg-purple-50 border-l-4 border-purple-500 p-4 sm:p-6 rounded-xl shadow-sm">
                        <p class="text-purple-700 font-semibold text-xs uppercase tracking-wide">Waqf</p>
                        <p class="text-3xl font-bold text-purple-700 mt-1">In: RM {{ number_format($waqfDonations, 0) }}</p>
                        <p class="text-sm text-red-600 mt-1">Out: - RM {{ number_format($waqfWithdrawals, 0) }}</p>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-4 sm:px-6 py-4 border-b border-blue-200">
                        <h3 class="font-bold text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            Financial Summary ({{ $monthName }} {{ $year }})
                        </h3>
                    </div>
                    <div class="p-4 sm:p-6">
                        <table class="min-w-full">
                            <tbody class="divide-y divide-gray-200">
                                @php
                                    $categories = [
                                        'zakat' => ['label' => 'Zakat', 'in' => $zakatDonations, 'out' => $zakatWithdrawals, 'color' => 'text-[#C5A059]'],
                                        'zakat_fitr' => ['label' => 'Zakat Fitr', 'in' => $zakatFitrDonations, 'out' => $zakatFitrWithdrawals, 'color' => 'text-amber-600'],
                                        'sadaqah' => ['label' => 'Sadaqah', 'in' => $sadaqahDonations, 'out' => $sadaqahWithdrawals, 'color' => 'text-blue-700'],
                                        'waqf' => ['label' => 'Waqf', 'in' => $waqfDonations, 'out' => $waqfWithdrawals, 'color' => 'text-purple-700'],
                                    ];
                                @endphp
                                @foreach($categories as $key => $cat)
                                    @php $net = $cat['in'] - $cat['out']; @endphp
                                    <tr>
                                        <td class="py-3 text-gray-600 font-medium">{{ $cat['label'] }} In</td>
                                        <td class="py-3 text-right font-bold {{ $cat['color'] }}">RM {{ number_format($cat['in'], 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-3 pl-4 text-gray-500 text-sm">{{ $cat['label'] }} Out</td>
                                        <td class="py-3 text-right font-bold text-red-600">- RM {{ number_format($cat['out'], 2) }}</td>
                                    </tr>
                                    <tr class="{{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                                        <td class="py-3 pl-4 text-gray-700 text-sm font-semibold">{{ $cat['label'] }} Net</td>
                                        <td class="py-3 text-right font-bold {{ $net >= 0 ? $cat['color'] : 'text-red-600' }}">RM {{ number_format($net, 2) }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td class="py-3 text-gray-600">Report Period</td>
                                    <td class="py-3 text-right text-gray-800">{{ $monthName }} {{ $year }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Category Breakdown --}}
                @php $totalCat = array_sum($categoryBreakdown); @endphp
                @if($totalCat > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-emerald-50 to-green-50 px-4 sm:px-6 py-4 border-b border-emerald-200">
                        <h3 class="font-bold text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.55 13.36c1.334.11.856.216 1.545.345m8.455 0v.417m-8.455 0H12m0 0l3 3m-3-3l3 3"></path>
                            </svg>
                            Donations by Shariah Type
                        </h3>
                    </div>
                    <div class="p-4 sm:p-6">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            @foreach($catLabels as $key => $label)
                                @if(($categoryBreakdown[$key] ?? 0) > 0)
                                <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                                    <p class="text-xs text-gray-500 uppercase tracking-wide truncate">{{ $label }}</p>
                                    <p class="text-lg font-bold text-emerald-700">RM {{ number_format($categoryBreakdown[$key], 2) }}</p>
                                    <div class="mt-2 bg-gray-200 rounded-full h-1.5">
                                        <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ ($categoryBreakdown[$key] / $totalCat) * 100 }}%"></div>
                                    </div>
                                    <p class="text-xs text-gray-400 mt-1">{{ number_format(($categoryBreakdown[$key] / $totalCat) * 100, 1) }}%</p>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                {{-- Chart --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-4 sm:px-6 py-4 border-b border-blue-200">
                        <h3 class="font-bold text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 4 4-4 4 4 4 4 4 4 4 4 4 4 4 4 4"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h18v18H3V3z"></path>
                            </svg>
                            Donations vs Expenses (Last 6 Months)
                        </h3>
                    </div>
                    <div class="p-4 sm:p-6">
                        <canvas id="reportsChart" height="100"></canvas>
                    </div>
                </div>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const ctx = document.getElementById('reportsChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: @json($chartLabels),
                            datasets: [
                                { label: 'Donations (In)', data: @json($chartDonations), backgroundColor: 'rgba(16, 185, 129, 0.7)', borderColor: 'rgb(16, 185, 129)', borderWidth: 1, borderRadius: 4 },
                                { label: 'Expenses (Out)', data: @json($chartExpenses), backgroundColor: 'rgba(239, 68, 68, 0.7)', borderColor: 'rgb(239, 68, 68)', borderWidth: 1, borderRadius: 4 }
                            ]
                        },
                        options: {
                            responsive: true, maintainAspectRatio: true,
                            plugins: { legend: { position: 'top' }, tooltip: { callbacks: { label: function(ctx) { return ctx.dataset.label + ': RM ' + ctx.parsed.y.toLocaleString('en-MY', {minimumFractionDigits: 2}); } } } },
                            scales: { y: { beginAtZero: true, ticks: { callback: function(v) { return 'RM ' + v.toLocaleString(); } } } }
                        }
                    });
                });
            </script>
        @endif

@if($tab === 'withdrawals')
            <div id="withdrawals-table" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-red-50 to-rose-50 px-4 sm:px-6 py-4 border-b border-red-200">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4m0 0L3 5m0 0v8m0-8l8 8"></path>
                        </svg>
                        Approved Withdrawals ({{ $monthName }} {{ $year }})
                    </h3>
                </div>
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                                    <a href="{{ request()->fullUrlWithQuery(['sort_withdrawal' => 'created_at', 'direction_withdrawal' => $sortWithdrawal === 'created_at' && $directionWithdrawal === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-700">
                                        Date
                                        @if($sortWithdrawal === 'created_at')
                                            <svg class="w-4 h-4 {{ $directionWithdrawal === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                                    <a href="{{ request()->fullUrlWithQuery(['sort_withdrawal' => 'purpose', 'direction_withdrawal' => $sortWithdrawal === 'purpose' && $directionWithdrawal === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-700">
                                        Purpose
                                        @if($sortWithdrawal === 'purpose')
                                            <svg class="w-4 h-4 {{ $directionWithdrawal === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                                    <a href="{{ request()->fullUrlWithQuery(['sort_withdrawal' => 'requested_by', 'direction_withdrawal' => $sortWithdrawal === 'requested_by' && $directionWithdrawal === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-700">
                                        Requested By
                                        @if($sortWithdrawal === 'requested_by')
                                            <svg class="w-4 h-4 {{ $directionWithdrawal === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Approved By</th>
                                <th class="px-4 sm:px-6 py-3 text-right text-xs font-medium text-gray-600 uppercase">
                                    <a href="{{ request()->fullUrlWithQuery(['sort_withdrawal' => 'amount', 'direction_withdrawal' => $sortWithdrawal === 'amount' && $directionWithdrawal === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center justify-end gap-1 hover:text-gray-700">
                                        Amount
                                        @if($sortWithdrawal === 'amount')
                                            <svg class="w-4 h-4 {{ $directionWithdrawal === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                        @endif
                                    </a>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 text-sm">
                            @forelse($withdrawals as $wd)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 sm:px-6 py-3 text-gray-800">{{ $wd->created_at->format('d M Y') }}</td>
                                    <td class="px-4 sm:px-6 py-3 text-gray-800">{{ $wd->purpose }}</td>
                                    <td class="px-4 sm:px-6 py-3 text-gray-800">{{ $wd->requester->name }}</td>
                                    <td class="px-4 sm:px-6 py-3 text-gray-800">{{ $wd->approver->name ?? '-' }}</td>
                                    <td class="px-4 sm:px-6 py-3 text-right font-bold text-red-700">- RM {{ number_format($wd->amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-6 text-gray-400">No approved withdrawals for this period</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="md:hidden divide-y divide-gray-200">
                    @forelse($withdrawals as $wd)
                        <div class="p-4 space-y-2">
                            <div class="flex justify-between items-start">
                                <span class="text-sm font-medium text-gray-900">{{ $wd->created_at->format('d M Y') }}</span>
                                <span class="text-sm font-bold text-red-700">- RM {{ number_format($wd->amount, 2) }}</span>
                            </div>
                            <div class="text-sm text-gray-800">{{ $wd->purpose }}</div>
                            <div class="flex justify-between text-xs text-gray-500">
                                <span>Requested by: {{ $wd->requester->name }}</span>
                                <span>Approved by: {{ $wd->approver->name ?? '-' }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-gray-400 text-sm">No approved withdrawals for this period</div>
                    @endforelse
                </div>
                @if($withdrawals->hasPages())
                    <div class="px-4 sm:px-6 py-3 bg-gray-50 border-t border-gray-100">
                        {{ $withdrawals->appends(request()->except('page'))->links() }}
                    </div>
                @endif
            </div>
        @endif

        @if($tab === 'gamification')
            @php
                $gamTotalMembers = \App\Models\MemberPoints::count();
                $gamTotalEarned = \App\Models\PointTransaction::where('type', 'earned')->sum('points');
                $gamTotalRedeemed = abs(\App\Models\PointTransaction::where('type', 'redeemed')->sum('points'));
                $gamTotalBadges = \App\Models\BadgeEarning::count();
                $gamTotalRedemptions = \App\Models\RewardRedemption::count();
            @endphp
            <div id="gamification-content" class="space-y-6">
                <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                    <div class="bg-gradient-to-br from-amber-50 to-orange-50 border-l-4 border-amber-500 p-5 rounded-xl shadow-sm text-center">
                        <p class="text-2xl font-bold text-amber-800">{{ number_format($gamTotalMembers) }}</p>
                        <p class="text-xs text-amber-600 mt-1">Active Members</p>
                    </div>
                    <div class="bg-gradient-to-br from-emerald-50 to-green-50 border-l-4 border-emerald-500 p-5 rounded-xl shadow-sm text-center">
                        <p class="text-2xl font-bold text-emerald-800">{{ number_format($gamTotalEarned) }}</p>
                        <p class="text-xs text-emerald-600 mt-1">Total Points Earned</p>
                    </div>
                    <div class="bg-gradient-to-br from-purple-50 to-violet-50 border-l-4 border-purple-500 p-5 rounded-xl shadow-sm text-center">
                        <p class="text-2xl font-bold text-purple-800">{{ number_format($gamTotalRedeemed) }}</p>
                        <p class="text-xs text-purple-600 mt-1">Points Redeemed</p>
                    </div>
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border-l-4 border-blue-500 p-5 rounded-xl shadow-sm text-center">
                        <p class="text-2xl font-bold text-blue-800">{{ number_format($gamTotalBadges) }}</p>
                        <p class="text-xs text-blue-600 mt-1">Badges Awarded</p>
                    </div>
                    <div class="bg-gradient-to-br from-rose-50 to-pink-50 border-l-4 border-rose-500 p-5 rounded-xl shadow-sm text-center">
                        <p class="text-2xl font-bold text-rose-800">{{ number_format($gamTotalRedemptions) }}</p>
                        <p class="text-xs text-rose-600 mt-1">Rewards Redeemed</p>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-amber-50 to-orange-50 px-4 sm:px-6 py-4 border-b border-amber-200">
                        <h3 class="font-bold text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Gamification Summary
                        </h3>
                    </div>
                    <div class="p-4 sm:p-6 text-center text-gray-500">
                        <p>View the full gamification report including member points, transactions, badge earnings, and reward redemptions in the CSV or PDF export.</p>
                        <p class="mt-2 text-sm">Use the Quick Export section above to download the complete report.</p>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            
            if (urlParams.has('page')) {
                const activeTab = '{{ $tab }}';
                const tableId = activeTab + '-table';
                const table = document.getElementById(tableId);
                if (table) {
                    setTimeout(() => {
                        table.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }, 100);
                }
            }
        });
    </script>

@endsection

