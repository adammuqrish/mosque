@extends('layouts.app')

@section('back', '/donations')

@section('title', 'Batch Donation Entry')

@php
    $breadcrumbs = [
        ['label' => __('islamic.donations.nav_label'), 'url' => route('donations.index')],
        ['label' => 'Batch Entry'],
    ];
@endphp

@section('content')

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Batch Donation Entry</h1>
        <p class="text-gray-500 text-sm mt-1">Enter multiple <strong>Sadaqah</strong> donations at once — for Friday prayers, events, or box collection. For Zakat/Waqf, use the <a href="{{ route('donations.index') }}" class="text-emerald-600 underline">single entry form</a>.</p>
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
        <form method="POST" action="{{ route('donations.batch.store') }}" id="batchForm">
            @csrf

            {{-- Single responsive donation rows (no duplicates — avoids duplicate input names on submit) --}}
            <div id="batchRows" class="space-y-4">
                <div class="donation-card border rounded-lg p-4 bg-gray-50 space-y-3" data-row="0">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-400 row-number">Row 1</span>
                        <button type="button" onclick="removeRow(this)" class="text-red-400 hover:text-red-600 transition p-1 remove-btn" title="Remove">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        <div class="sm:col-span-2 md:col-span-1">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Amount (RM) <span class="text-red-500">*</span></label>
                            <input type="number" step="0.01" min="0.01" name="donations[0][amount]"
                                class="w-full border rounded px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none required:border-red-500"
                                placeholder="0.00" required>
                        </div>
                        <div class="sm:col-span-1 md:col-span-1">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Fund Purpose</label>
                            <input type="text" name="donations[0][fund_purpose]"
                                class="w-full border rounded px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none purpose-input-mobile mb-2"
                                placeholder="Purpose" value="General Fund">
                            <div class="flex flex-wrap gap-1">
                                @foreach($suggestedPurposes as $purpose)
                                    <button type="button" onclick="this.closest('.donation-card').querySelector('.purpose-input-mobile').value='{{ $purpose }}'"
                                        class="px-2 py-0.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-[10px] rounded-full transition font-medium">{{ $purpose }}</button>
                                @endforeach
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Source <span class="text-red-500">*</span></label>
                            <select name="donations[0][source]" class="w-full border rounded px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none" required>
                                <option value="cash">Cash</option>
                                <option value="online">Online</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Date <span class="text-red-500">*</span></label>
                            <input type="date" name="donations[0][donation_date]"
                                class="w-full border rounded px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                                value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Description (Optional)</label>
                            <input type="text" name="donations[0][description]"
                                class="w-full border rounded px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none" placeholder="(optional)">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 flex flex-wrap gap-3">
                <button type="button" onclick="addRow()"
                    class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg transition text-sm flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add Row
                </button>
                <button type="submit"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-6 rounded-lg shadow transition text-sm">
                    Record All Donations
                </button>
            </div>
        </form>
    </div>

@endsection

@section('scripts')
<script>
let rowIndex = 1;

function getRowTemplate() {
    // Single card cloned for new rows — no layout duplication.
    const container = document.getElementById('batchRows');
    const firstCard = container.querySelector('.donation-card');
    return firstCard.cloneNode(true);
}

function addRow() {
    const container = document.getElementById('batchRows');
    const rowClone = getRowTemplate();

    rowClone.querySelectorAll('input, select').forEach(el => {
        const name = el.getAttribute('name');
        if (name) el.setAttribute('name', name.replace(/donations\[0\]/, 'donations[' + rowIndex + ']'));
        if (el.type === 'date') el.value = '{{ date('Y-m-d') }}';
        else if (el.type === 'number') el.value = '';
        else if (el.tagName === 'SELECT') el.selectedIndex = 0;
        else if (el.type === 'text' && name && name.includes('fund_purpose')) el.value = 'General Fund';
        else if (el.type === 'text' && name && name.includes('description')) el.value = '';
    });
    rowClone.setAttribute('data-row', rowIndex);
    rowClone.querySelector('.remove-btn').onclick = function() { removeRow(this); };
    rowClone.querySelector('.row-number').textContent = 'Row ' + (rowIndex + 1);
    container.appendChild(rowClone);

    rowIndex++;
}

function removeRow(btn) {
    const row = btn.closest('.donation-card');
    const container = document.getElementById('batchRows');

    if (container.querySelectorAll('.donation-card').length <= 1) {
        showNotification('warning', 'Cannot Remove', 'At least one row is required.');
        return;
    }

    row.remove();
    updateNumbers();
}

function updateNumbers() {
    let i = 1;
    document.querySelectorAll('#batchRows .donation-card .row-number').forEach((el) => {
        el.textContent = 'Row ' + i;
        i++;
    });
}
</script>
@endsection

