@extends('layouts.app')

{{-- Back goes to previous page via layout default --}}

@section('title', __('islamic.navigation.reports'))

@php
    $breadcrumbs = [
        ['label' => __('islamic.navigation.reports')],
    ];
@endphp

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
                             <a href="{{ route('reports.export.donations.csv', ['month' => $month, 'year' => $year, 'report_type' => $reportType]) }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-green-50 rounded-t-lg min-h-[44px] flex items-center gap-2"><svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7c0-2-1-3-3-3H7C5 4 4 5 4 7z M8 12h8 M8 8h8 M8 16h5"/></svg>CSV</a>
                             <a href="{{ route('reports.export.donations.pdf', ['month' => $month, 'year' => $year, 'report_type' => $reportType]) }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-green-50 rounded-b-lg min-h-[44px] flex items-center gap-2"><svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>PDF</a>
                        </div>
                    </div>

                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false" class="w-full bg-purple-50 hover:bg-purple-100 text-purple-700 font-medium py-3 px-2 sm:py-2.5 sm:px-3 rounded-lg border border-purple-200 text-xs sm:text-sm transition flex items-center justify-center gap-1.5 sm:gap-2 min-h-[48px] sm:min-h-[44px]">
                            <svg class="w-5 h-5 sm:w-4 sm:h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span>Events</span>
                        </button>
                        <div x-show="open" x-transition class="absolute left-0 right-0 mt-1 bg-white rounded-lg shadow-lg border z-20 mx-1" style="display: none;">
                             <a href="{{ route('reports.export.events.csv', ['month' => $month, 'year' => $year, 'report_type' => $reportType]) }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-purple-50 rounded-t-lg min-h-[44px] flex items-center gap-2"><svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7c0-2-1-3-3-3H7C5 4 4 5 4 7z M8 12h8 M8 8h8 M8 16h5"/></svg>CSV</a>
                             <a href="{{ route('reports.export.events.pdf', ['month' => $month, 'year' => $year, 'report_type' => $reportType]) }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-purple-50 rounded-b-lg min-h-[44px] flex items-center gap-2"><svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>PDF</a>
                        </div>
                    </div>

                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false" class="w-full bg-yellow-50 hover:bg-yellow-100 text-yellow-700 font-medium py-3 px-2 sm:py-2.5 sm:px-3 rounded-lg border border-yellow-200 text-xs sm:text-sm transition flex items-center justify-center gap-1.5 sm:gap-2 min-h-[48px] sm:min-h-[44px]">
                            <svg class="w-5 h-5 sm:w-4 sm:h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                            <span>Attendance</span>
                        </button>
                        <div x-show="open" x-transition class="absolute left-0 right-0 mt-1 bg-white rounded-lg shadow-lg border z-20 mx-1" style="display: none;">
                             <a href="{{ route('reports.export.attendance.csv', ['month' => $month, 'year' => $year, 'report_type' => $reportType]) }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-yellow-50 rounded-t-lg min-h-[44px] flex items-center gap-2"><svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7c0-2-1-3-3-3H7C5 4 4 5 4 7z M8 12h8 M8 8h8 M8 16h5"/></svg>CSV</a>
                             <a href="{{ route('reports.export.attendance.pdf', ['month' => $month, 'year' => $year, 'report_type' => $reportType]) }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-yellow-50 rounded-b-lg min-h-[44px] flex items-center gap-2"><svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>PDF</a>
                        </div>
                    </div>

                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false" class="w-full bg-blue-50 hover:bg-blue-100 text-blue-700 font-medium py-3 px-2 sm:py-2.5 sm:px-3 rounded-lg border border-blue-200 text-xs sm:text-sm transition flex items-center justify-center gap-1.5 sm:gap-2 min-h-[48px] sm:min-h-[44px]">
                            <svg class="w-5 h-5 sm:w-4 sm:h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>Financial</span>
                        </button>
                        <div x-show="open" x-transition class="absolute left-0 right-0 mt-1 bg-white rounded-lg shadow-lg border z-20 mx-1" style="display: none;">
                             <a href="{{ route('reports.export.financial.csv', ['month' => $month, 'year' => $year, 'report_type' => $reportType]) }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 rounded-t-lg min-h-[44px] flex items-center gap-2"><svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7c0-2-1-3-3-3H7C5 4 4 5 4 7z M8 12h8 M8 8h8 M8 16h5"/></svg>CSV</a>
                             <a href="{{ route('reports.export.financial.pdf', ['month' => $month, 'year' => $year, 'report_type' => $reportType]) }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 rounded-b-lg min-h-[44px] flex items-center gap-2"><svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>PDF</a>
                        </div>
                    </div>

                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false" class="w-full bg-amber-50 hover:bg-amber-100 text-amber-700 font-medium py-3 px-2 sm:py-2.5 sm:px-3 rounded-lg border border-amber-200 text-xs sm:text-sm transition flex items-center justify-center gap-1.5 sm:gap-2 min-h-[48px] sm:min-h-[44px]">
                            <svg class="w-5 h-5 sm:w-4 sm:h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                            <span>Gamification</span>
                        </button>
                        <div x-show="open" x-transition class="absolute left-0 right-0 mt-1 bg-white rounded-lg shadow-lg border z-20 mx-1" style="display: none;">
                             <a href="{{ route('reports.export.gamification.csv', ['month' => $month, 'year' => $year, 'report_type' => $reportType]) }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-amber-50 rounded-t-lg min-h-[44px] flex items-center gap-2"><svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7c0-2-1-3-3-3H7C5 4 4 5 4 7z M8 12h8 M8 8h8 M8 16h5"/></svg>CSV</a>
                             <a href="{{ route('reports.export.gamification.pdf', ['month' => $month, 'year' => $year, 'report_type' => $reportType]) }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-amber-50 rounded-b-lg min-h-[44px] flex items-center gap-2"><svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>PDF</a>
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
            @include('reports.partials._donations')
        @endif

        @if($tab === 'events')
            @include('reports.partials._events')
        @endif

        @if($tab === 'attendance')
            @include('reports.partials._attendance')
        @endif

        @if($tab === 'financial')
            @include('reports.partials._financial')
        @endif

        @if($tab === 'withdrawals')
            @include('reports.partials._withdrawals')
        @endif

        @if($tab === 'gamification')
            @include('reports.partials._gamification')
        @endif
    </div>

@endsection

