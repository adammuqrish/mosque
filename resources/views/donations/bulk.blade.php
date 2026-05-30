@extends('layouts.app')

@section('back', '/donations')

@section('title', 'Bulk Sadaqah Entry')

@php
    $breadcrumbs = [
        ['label' => __('islamic.donations.nav_label'), 'url' => route('donations.index')],
        ['label' => 'Bulk Entry'],
    ];
@endphp

@section('content')

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Bulk Sadaqah Entry</h1>
        <p class="text-gray-500 text-sm mt-1">Record <strong>anonymous sadaqah</strong> from donation boxes — for weekly or monthly box collections where individual donors are not known. One receipt will be generated for the entire collection.</p>
    </div>

    @if (session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="font-semibold text-red-800 text-sm">Please fix the following errors:</p>
            <ul class="list-disc list-inside text-sm text-red-700 mt-1 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-md p-4 sm:p-6">
        <form method="POST" action="{{ route('donations.bulk.store') }}">
            @csrf

            <div class="max-w-lg space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Total Amount (RM) <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" name="amount"
                        class="w-full border rounded-lg px-4 py-2.5 text-lg font-bold text-emerald-700 focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                        placeholder="0.00" value="{{ old('amount') }}" required>
                    <p class="text-xs text-gray-400 mt-1">Total cash counted from the donation box.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Collection Date <span class="text-red-500">*</span></label>
                    <input type="date" name="donation_date"
                        class="w-full border rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                        value="{{ old('donation_date', date('Y-m-d')) }}" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fund Purpose <span class="text-red-500">*</span></label>
                    <input type="text" name="fund_purpose"
                        class="w-full border rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none purpose-input mb-2"
                        placeholder="e.g. General Fund" value="{{ old('fund_purpose', 'General Fund') }}" required>
                    <div class="flex flex-wrap gap-1">
                        @foreach($suggestedPurposes as $purpose)
                            <button type="button" onclick="document.querySelector('.purpose-input').value='{{ $purpose }}'"
                                class="px-2.5 py-1 bg-gray-100 hover:bg-gray-200 text-gray-600 text-[10px] rounded-full transition font-medium">{{ $purpose }}</button>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description / Notes</label>
                    <textarea name="description" rows="2"
                        class="w-full border rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                        placeholder="e.g. Monthly collection for December 2026">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Witnesses</label>
                    <input type="text" name="witnesses"
                        class="w-full border rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                        placeholder="e.g. Imran &amp; Hassan" value="{{ old('witnesses') }}">
                    <p class="text-xs text-gray-400 mt-1">Names of those who counted the box together (optional, for transparency).</p>
                </div>

                <div class="pt-2">
                    <button type="submit"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-6 rounded-lg shadow transition text-sm">
                        Record Collection
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="mt-6 bg-amber-50 border border-amber-200 rounded-lg p-4 max-w-lg">
        <p class="text-xs text-amber-800">
            <strong>Note:</strong> This entry will be recorded as an <strong>anonymous confirmed sadaqah</strong> donation with a single receipt number. The amount is the total cash counted from the box. No individual donor data will be stored.
        </p>
    </div>

@endsection
