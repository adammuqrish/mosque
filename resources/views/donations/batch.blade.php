@extends('layouts.app')

@section('back', '/donations')

@section('title', 'Batch Donation Entry')

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

            {{-- Desktop Table --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="batchTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase w-8">#</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount (RM)</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fund Purpose</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Source</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Donor (Optional)</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase w-10"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="batchTableBody">
                        <tr class="donation-row" data-row="0">
                            <td class="px-3 py-2 text-xs text-gray-400 row-number">1</td>
                            <td class="px-3 py-2">
                                <input type="number" step="0.01" name="donations[0][amount]"
                                    class="w-full border rounded px-2 py-1.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                                    placeholder="0.00" required>
                            </td>
                            <td class="px-3 py-2">
                                <span class="inline-flex items-center gap-1 bg-emerald-100 text-emerald-800 text-xs px-2.5 py-1.5 rounded-full font-medium">Sadaqah</span>
                            </td>
                            <td class="px-3 py-2">
                                <div>
                                    <input type="text" name="donations[0][fund_purpose]"
                                        class="w-full border rounded px-2 py-1.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none purpose-input mb-1.5"
                                        placeholder="Purpose" value="General Fund">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($suggestedPurposes as $purpose)
                                            <button type="button" onclick="this.closest('td').querySelector('.purpose-input').value='{{ $purpose }}'"
                                                class="px-2 py-0.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-[10px] rounded-full transition font-medium">{{ $purpose }}</button>
                                        @endforeach
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 py-2">
                                <select name="donations[0][source]" class="w-full border rounded px-2 py-1.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                    <option value="cash">Cash</option>
                                    <option value="online">Online</option>
                                </select>
                            </td>
                            <td class="px-3 py-2">
                                <input type="date" name="donations[0][donation_date]"
                                    class="w-full border rounded px-2 py-1.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                                    value="{{ date('Y-m-d') }}" required>
                            </td>
                            <td class="px-3 py-2">
                                <input type="text" name="donations[0][donor_name]"
                                    class="w-full border rounded px-2 py-1.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none" placeholder="(optional)">
                            </td>
                            <td class="px-3 py-2 text-center">
                                <button type="button" onclick="removeRow(this)" class="text-red-400 hover:text-red-600 transition p-1 remove-btn" title="Remove">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="md:hidden divide-y divide-gray-200" id="batchMobileCards">
                <div class="donation-card p-4 space-y-3" data-row="0">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-400 row-number">#1</span>
                        <button type="button" onclick="removeRow(this)" class="text-red-400 hover:text-red-600 transition p-1" title="Remove">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Amount (RM)</label>
                            <input type="number" step="0.01" name="donations[0][amount]"
                                class="w-full border rounded px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                                placeholder="0.00" required>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Source</label>
                            <select name="donations[0][source]" class="w-full border rounded px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                <option value="cash">Cash</option>
                                <option value="online">Online</option>
                            </select>
                        </div>
                    </div>
                    <div>
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
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Date</label>
                            <input type="date" name="donations[0][donation_date]"
                                class="w-full border rounded px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                                value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Donor</label>
                            <input type="text" name="donations[0][donor_name]"
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

function addRow() {
    const tbody = document.getElementById('batchTableBody');
    const cards = document.getElementById('batchMobileCards');
    const firstRow = tbody.querySelector('.donation-row');
    const firstCard = cards.querySelector('.donation-card');

    // Clone table row
    const rowClone = firstRow.cloneNode(true);
    rowClone.querySelectorAll('input, select').forEach(el => {
        const name = el.getAttribute('name');
        if (name) el.setAttribute('name', name.replace(/\d+/, rowIndex));
        if (el.type === 'date') el.value = '{{ date('Y-m-d') }}';
        else if (el.type === 'number') el.value = '';
        else if (el.tagName === 'SELECT') el.selectedIndex = 0;
        else if (el.type === 'text' && name && name.includes('fund_purpose')) el.value = 'General Fund';
        else if (el.type === 'text' && name && name.includes('donor_name')) el.value = '';
    });
    rowClone.setAttribute('data-row', rowIndex);
    rowClone.querySelector('.remove-btn').onclick = function() { removeRow(this); };
    tbody.appendChild(rowClone);

    // Clone mobile card
    const cardClone = firstCard.cloneNode(true);
    cardClone.querySelectorAll('input, select').forEach(el => {
        const name = el.getAttribute('name');
        if (name) el.setAttribute('name', name.replace(/\d+/, rowIndex));
        if (el.type === 'date') el.value = '{{ date('Y-m-d') }}';
        else if (el.type === 'number') el.value = '';
        else if (el.tagName === 'SELECT') el.selectedIndex = 0;
        else if (el.type === 'text' && name && name.includes('fund_purpose')) el.value = 'General Fund';
        else if (el.type === 'text' && name && name.includes('donor_name')) el.value = '';
    });
    cardClone.setAttribute('data-row', rowIndex);
    cardClone.querySelectorAll('[onclick*="removeRow"]').forEach(el => {
        el.onclick = function() { removeRow(this); };
    });
    cards.appendChild(cardClone);

    rowIndex++;
    updateNumbers();
}

function removeRow(btn) {
    const row = btn.closest('[data-row]');
    const index = row.getAttribute('data-row');

    const tbody = document.getElementById('batchTableBody');
    const cards = document.getElementById('batchMobileCards');

    const totalRows = tbody.querySelectorAll('.donation-row').length;

    if (totalRows <= 1) {
        showNotification('warning', 'Cannot Remove', 'At least one row is required.');
        return;
    }

    const tableRow = tbody.querySelector(`.donation-row[data-row="${index}"]`);
    const mobileCard = cards.querySelector(`.donation-card[data-row="${index}"]`);
    if (tableRow) tableRow.remove();
    if (mobileCard) mobileCard.remove();

    updateNumbers();
}

function updateNumbers() {
    document.querySelectorAll('#batchTableBody .row-number').forEach((el, i) => {
        el.textContent = i + 1;
    });
    document.querySelectorAll('#batchMobileCards .row-number').forEach((el, i) => {
        el.textContent = '#' + (i + 1);
    });
}
</script>
@endsection

