@extends('layouts.app')

@section('back', '/dashboard')

@section('title', __('islamic.navigation.requests'))

@php
    $breadcrumbs = [
        ['label' => __('islamic.navigation.requests')],
    ];
@endphp

@section('content')

<!-- STEP 1: Form Request (Admin only) -->
@if(Auth::user()->role == 'admin')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-8 border-l-4 border-emerald-500">
    <div class="px-4 sm:px-6 py-4 border-b border-t-2 border-t-[#C5A059] border-gray-100 bg-gray-50/50 flex justify-between items-center">
        <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            Request Withdrawal
        </h2>
        <span class="text-xs text-gray-500">For expenses</span>
    </div>

    <div class="p-6">
        <form action="/withdrawals" method="POST" enctype="multipart/form-data" data-loading>
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Amount (RM)</label>
                    <input type="number" step="0.01" name="amount"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        placeholder="0.00" required oninput="validateBalance(); updateSubmitButtonState()">
                    @error('amount')
                    <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"></path>
                        </svg>
                        {{ $message }}
                    </p>
                    @enderror
                    <div id="balanceWarning" class="hidden mt-2 p-2 bg-red-50 border border-red-200 rounded-lg flex items-start gap-2">
                        <svg class="w-4 h-4 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <p class="text-xs font-medium text-red-800">Amount exceeds available balance!</p>
                            <p class="text-xs text-red-600 mt-0.5">Available: <span id="warningAvailableBalance" class="font-semibold">RM 0</span></p>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Fund Type</label>
                    <select name="type" id="withdrawalTypeSelect" onchange="updateBalance()"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        <option value="sadaqah">Sadaqah (General)</option>
                        <option value="zakat">Zakat</option>
                        <option value="zakat_fitr">Zakat Fitr</option>
                        <option value="waqf">Waqf</option>
                    </select>
                    <p class="text-[10px] text-gray-400 mt-1">Available balance: <span id="balanceDisplay" class="font-semibold text-gray-700">RM {{ number_format($typeBalances['sadaqah'] ?? 0, 0) }}</span></p>
                    @error('type')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Fund Purpose <span class="text-red-500">*</span></label>
                    {{-- Sadaqah: show all fund purposes --}}
                    <select name="fund_purpose" id="fundPurposeSelectSadaqah" onchange="updatePurposeBalance()"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        <option value="" disabled selected>Select Fund Purpose</option>
                        @foreach($fundPurposes as $fp)
                            <option value="{{ $fp->name }}" {{ old('fund_purpose') == $fp->name ? 'selected' : '' }}>{{ $fp->name }}</option>
                        @endforeach
                    </select>
                    {{-- Zakat / Zakat Fitr / Waqf: auto-set to General Fund --}}
                    <div id="fundPurposeAuto" class="hidden">
                        <input type="hidden" name="fund_purpose" id="fundPurposeAutoInput" value="General Fund">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 bg-emerald-100 text-emerald-800 text-sm font-medium px-3 py-2 rounded-lg border border-emerald-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                General Fund
                            </span>
                            <span class="text-xs text-gray-400">(Auto-set — Zakat/Zakat Fitr/Waqf funds are pooled into the general fund)</span>
                        </div>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-1">Available: <span id="purposeBalanceDisplay" class="font-semibold text-gray-700">—</span></p>
                    @error('fund_purpose')
                    <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"></path>
                        </svg>
                        {{ $message }}
                    </p>
                    @enderror
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Purpose</label>
                    <input type="text" name="purpose"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        placeholder="e.g., Beli kipas gergasi untuk dewan utama" required>
                    @error('purpose')
                    <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"></path>
                        </svg>
                        {{ $message }}
                    </p>
                    @enderror
                </div>
            </div>

            {{-- Document Upload Section --}}
            <div class="mt-6 pt-6 border-t border-gray-200">
                <div class="flex items-center gap-2 mb-3">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                    </svg>
                    <h3 class="text-sm font-bold text-gray-700">Supporting Documents</h3>
                    <span class="text-xs text-gray-400">(Optional — Invoice, Quotation, Receipt)</span>
                </div>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 hover:border-blue-400 transition cursor-pointer" onclick="document.getElementById('documentInput').click()">
                    <input type="file" id="documentInput" name="documents[]" multiple accept=".pdf,.jpg,.jpeg,.png" class="hidden" onchange="handleFileSelect(event)">
                    <div class="text-center">
                        <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m0-3v12"></path>
                        </svg>
                        <p class="mt-2 text-sm text-gray-600">Click to upload or drag and drop</p>
                        <p class="text-xs text-gray-400">PDF, JPG, PNG (max 5MB each, max 5 files)</p>
                    </div>
                </div>
                <div id="filePreview" class="mt-3 space-y-2 hidden"></div>
            </div>

            <div class="mt-6 flex flex-col sm:flex-row gap-3">
                <button type="button" onclick="if(confirm('This will fill in demo/test data. Are you sure?')) autoFillWithdrawal()"
                    class="w-full sm:w-auto bg-blue-400 hover:bg-blue-500 text-white font-semibold py-2.5 px-4 rounded-lg transition flex items-center justify-center gap-2 min-h-[44px]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Auto Fill (Demo)
                </button>
                <button type="submit" id="submitBtn"
                    class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-4 sm:px-6 rounded-lg transition flex items-center justify-center gap-2 min-h-[44px]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Submit Request</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<!-- STEP 2: Request History Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-4 sm:px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
        <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Request History
        </h2>

        <div class="flex flex-wrap gap-2">
            <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full flex items-center gap-1">
                <span class="w-2 h-2 bg-yellow-500 rounded-full"></span>
                {{ $pending }} Pending
            </span>
            <span class="bg-orange-100 text-orange-800 text-xs px-2 py-1 rounded-full">
                {{ $makerChecked }} Checked
            </span>
            <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">
                {{ $approved }} Approved
            </span>
            <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full">
                {{ $rejected }} Rejected
            </span>
        </div>
    </div>

    {{-- Summary Cards: Outflow by Type --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 px-4 sm:px-6 py-4 border-b border-gray-100">
        <div class="bg-[#C5A059]/10 rounded-lg p-3 border border-[#C5A059]/20">
            <p class="text-[10px] font-semibold text-[#C5A059] uppercase">Zakat Out</p>
            <p class="text-lg font-bold text-[#C5A059]">RM {{ number_format($zakatOut, 2) }}</p>
            @if($zakatReserved > 0)
            <p class="text-[10px] text-amber-600 mt-0.5"><span class="w-1.5 h-1.5 bg-amber-500 rounded-full inline-block mr-0.5"></span>RM {{ number_format($zakatReserved, 0) }} reserved</p>
            @endif
        </div>
        <div class="bg-amber-50 rounded-lg p-3 border border-amber-200">
            <p class="text-[10px] font-semibold text-amber-700 uppercase">Zakat Fitr Out</p>
            <p class="text-lg font-bold text-amber-700">RM {{ number_format($zakatFitrOut, 2) }}</p>
            @if($zakatFitrReserved > 0)
            <p class="text-[10px] text-amber-600 mt-0.5"><span class="w-1.5 h-1.5 bg-amber-500 rounded-full inline-block mr-0.5"></span>RM {{ number_format($zakatFitrReserved, 0) }} reserved</p>
            @endif
        </div>
        <div class="bg-blue-50 rounded-lg p-3 border border-blue-200">
            <p class="text-[10px] font-semibold text-blue-700 uppercase">Sadaqah Out</p>
            <p class="text-lg font-bold text-blue-700">RM {{ number_format($sadaqahOut, 2) }}</p>
            @if($sadaqahReserved > 0)
            <p class="text-[10px] text-amber-600 mt-0.5"><span class="w-1.5 h-1.5 bg-amber-500 rounded-full inline-block mr-0.5"></span>RM {{ number_format($sadaqahReserved, 0) }} reserved</p>
            @endif
        </div>
        <div class="bg-purple-50 rounded-lg p-3 border border-purple-200">
            <p class="text-[10px] font-semibold text-purple-700 uppercase">Waqf Out</p>
            <p class="text-lg font-bold text-purple-700">RM {{ number_format($waqfOut, 2) }}</p>
            @if($waqfReserved > 0)
            <p class="text-[10px] text-amber-600 mt-0.5"><span class="w-1.5 h-1.5 bg-amber-500 rounded-full inline-block mr-0.5"></span>RM {{ number_format($waqfReserved, 0) }} reserved</p>
            @endif
        </div>
    </div>

    <!-- Desktop Table View -->
    <div id="withdrawals-table" class="hidden md:block p-4 sm:p-6 overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'direction' => $sort === 'created_at' && $direction === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-700">
                            Date
                            @if($sort === 'created_at')
                            <svg class="w-4 h-4 {{ $direction === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                            </svg>
                            @endif
                        </a>
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fund</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'fund_purpose', 'direction' => $sort === 'fund_purpose' && $direction === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-700">
                            Purpose
                            @if($sort === 'fund_purpose')
                            <svg class="w-4 h-4 {{ $direction === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                            </svg>
                            @endif
                        </a>
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                        Requested By
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'purpose', 'direction' => $sort === 'purpose' && $direction === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-700">
                            Description
                            @if($sort === 'purpose')
                            <svg class="w-4 h-4 {{ $direction === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                            </svg>
                            @endif
                        </a>
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'amount', 'direction' => $sort === 'amount' && $direction === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-700">
                            Amount
                            @if($sort === 'amount')
                            <svg class="w-4 h-4 {{ $direction === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                            </svg>
                            @endif
                        </a>
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Docs</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'status', 'direction' => $sort === 'status' && $direction === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-700">
                            Status
                            @if($sort === 'status')
                            <svg class="w-4 h-4 {{ $direction === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                            </svg>
                            @endif
                        </a>
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Verify By</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($requests as $req)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3 text-sm text-gray-700">{{ $req->created_at->format('d M Y') }}</td>
                    <td class="px-4 py-3 text-sm">
                        @if($req->type === 'zakat')
                            <span class="bg-[#C5A059] text-white text-xs px-2 py-0.5 rounded-full font-medium">Zakat</span>
                        @elseif($req->type === 'zakat_fitr')
                            <span class="bg-amber-100 text-amber-800 text-xs px-2 py-0.5 rounded-full font-medium">Zakat Fitr</span>
                        @elseif($req->type === 'waqf')
                            <span class="bg-purple-100 text-purple-800 text-xs px-2 py-0.5 rounded-full font-medium">Waqf</span>
                        @else
                            <span class="bg-blue-100 text-blue-800 text-xs px-2 py-0.5 rounded-full font-medium">Sadaqah</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm">
                        @if($req->fund_purpose)
                            <span class="inline-flex items-center gap-1 bg-gray-100 text-gray-700 text-xs px-2.5 py-1 rounded-full font-medium">{{ $req->fund_purpose }}</span>
                        @else
                            <span class="text-gray-400 text-xs italic">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-700 font-medium">{{ $req->requester->name }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600 max-w-xs truncate">{{ $req->purpose }}</td>
                    <td class="px-4 py-3 text-sm font-bold text-emerald-600">RM {{ number_format($req->amount, 2) }}</td>
                    <td class="px-4 py-3 text-sm">
                        @if($req->documents && $req->documents->count() > 0)
                            <button type="button" onclick="showDocumentsModal({{ $req->id }})" class="inline-flex items-center gap-1 bg-blue-100 hover:bg-blue-200 text-blue-700 text-xs px-2.5 py-1 rounded-full font-medium transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                </svg>
                                {{ $req->documents->count() }}
                            </button>
                        @else
                            <span class="inline-flex items-center gap-1 text-gray-400 text-xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                </svg>
                                None
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm">
                        @if($req->status == 'pending')
                        <span class="inline-flex items-center gap-1 bg-yellow-100 text-yellow-800 text-xs px-3 py-1 rounded-full font-medium">
                            <span class="w-2 h-2 bg-yellow-500 rounded-full animate-pulse"></span>
                            Pending
                        </span>
                        @elseif($req->status == 'maker_checked')
                        <span class="inline-flex items-center gap-1 bg-orange-100 text-orange-800 text-xs px-3 py-1 rounded-full font-medium">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Checked
                        </span>
                        @elseif($req->status == 'approved')
                        <span class="inline-flex items-center gap-1 bg-green-100 text-green-800 text-xs px-3 py-1 rounded-full font-medium">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Approved
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1 bg-red-100 text-red-800 text-xs px-3 py-1 rounded-full font-medium">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Rejected
                        </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm">
                        @php
                            $canAct = Auth::user()->role == 'treasurer'
                                && $req->requested_by !== Auth::id()
                                && ($req->status == 'pending'
                                    || ($req->status == 'maker_checked' && $req->maker_checked_by !== Auth::id()));
                        @endphp
                        @if($canAct)
                        <div class="flex gap-1">
                            <button type="button" data-action="{{ route('withdrawals.approve', $req->id) }}" data-title="Approve Request" data-message="Are you sure?" data-btn-text="Approve" data-btn-class="bg-green-600 hover:bg-green-700" onclick="showConfirmModal(this.dataset.title, this.dataset.message, this.dataset.btnText, this.dataset.btnClass, this.dataset.action)" class="inline-flex items-center gap-1 bg-green-600 hover:bg-green-700 text-white font-medium py-1.5 px-3 rounded text-xs transition">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                {{ $req->status == 'maker_checked' ? 'Final Approve' : 'Approve' }}
                            </button>
                            <button type="button" data-action="{{ route('withdrawals.reject', $req->id) }}" data-title="Reject Request" data-message="Are you sure?" data-btn-text="Reject" data-btn-class="bg-red-600 hover:bg-red-700" onclick="showConfirmModal(this.dataset.title, this.dataset.message, this.dataset.btnText, this.dataset.btnClass, this.dataset.action, 'POST', true)" class="inline-flex items-center gap-1 bg-red-600 hover:bg-red-700 text-white font-medium py-1.5 px-3 rounded text-xs transition">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Reject
                            </button>
                        </div>
                        @elseif($req->status == 'maker_checked')
                        <div class="text-xs text-gray-500">
                            <span class="text-orange-600 font-medium">Checked</span> by {{ $req->makerChecker->name ?? '-' }}
                            <span class="text-gray-400">&bull;</span>
                            {{ $req->maker_checked_at ? $req->maker_checked_at->format('d M Y') : '' }}
                        </div>
                        @elseif($req->status == 'approved')
                        <span class="text-xs text-gray-500">
                            {{ $req->approver->name ?? '-' }}
                            <span class="text-gray-400">&bull;</span>
                            {{ $req->approved_at ? $req->approved_at->format('d M Y') : '' }}
                        </span>
                        @elseif($req->status == 'rejected')
                        <span class="text-xs text-gray-500">
                            {{ $req->rejector->name ?? '-' }}
                            <span class="text-gray-400">&bull;</span>
                            {{ $req->rejected_at ? $req->rejected_at->format('d M Y') : '' }}
                        </span>
                        @else
                        <span class="text-xs text-gray-400">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-8 text-gray-500">No requests found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($requests->hasPages())
    <div id="withdrawals-pagination" class="hidden md:block px-4 sm:px-6 py-3 bg-gray-50 border-t border-gray-100">
        {{ $requests->appends(request()->except('page'))->links() }}
    </div>
    @endif

    <!-- Mobile Card View -->
    <div class="md:hidden divide-y divide-gray-200">
        @forelse($requests as $req)
        <div class="p-4 hover:bg-gray-50 transition">
            <div class="flex items-start justify-between mb-2">
                <div>
                    <p class="text-sm font-medium text-gray-800">{{ $req->requester->name }}</p>
                    <p class="text-xs text-gray-500">{{ $req->created_at->format('d M Y') }}</p>
                    <div class="flex items-center gap-1.5 mt-1 flex-wrap">
                        @if($req->type === 'zakat')
                            <span class="inline-flex items-center gap-0.5 bg-[#C5A059] text-white text-[10px] px-1.5 py-0.5 rounded-full font-medium">Zakat</span>
                        @elseif($req->type === 'zakat_fitr')
                            <span class="inline-flex items-center gap-0.5 bg-amber-100 text-amber-800 text-[10px] px-1.5 py-0.5 rounded-full font-medium">Zakat Fitr</span>
                        @elseif($req->type === 'waqf')
                            <span class="inline-flex items-center gap-0.5 bg-purple-100 text-purple-800 text-[10px] px-1.5 py-0.5 rounded-full font-medium">Waqf</span>
                        @else
                            <span class="inline-flex items-center gap-0.5 bg-blue-100 text-blue-800 text-[10px] px-1.5 py-0.5 rounded-full font-medium">Sadaqah</span>
                        @endif
                        @if($req->fund_purpose)
                            <span class="inline-flex items-center gap-0.5 bg-gray-100 text-gray-600 text-[10px] px-1.5 py-0.5 rounded-full font-medium">{{ $req->fund_purpose }}</span>
                        @endif
                        @if($req->documents && $req->documents->count() > 0)
                            <button type="button" onclick="showDocumentsModal({{ $req->id }})" class="inline-flex items-center gap-0.5 bg-blue-100 text-blue-700 text-[10px] px-1.5 py-0.5 rounded-full font-medium">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                {{ $req->documents->count() }} docs
                            </button>
                        @endif
                    </div>
                </div>
                <p class="text-lg font-bold text-emerald-600">RM {{ number_format($req->amount, 2) }}</p>
            </div>
            <p class="text-sm text-gray-600 mb-3">{{ $req->purpose }}</p>
            <div class="flex items-center justify-between mb-3">
                @if($req->status == 'pending')
                <span class="inline-flex items-center gap-1 bg-yellow-100 text-yellow-800 text-xs px-2 py-0.5 rounded-full font-medium"><span class="w-2 h-2 bg-yellow-500 rounded-full animate-pulse"></span> Pending</span>
                @elseif($req->status == 'maker_checked')
                <span class="inline-flex items-center gap-1 bg-orange-100 text-orange-800 text-xs px-2 py-0.5 rounded-full font-medium"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Checked</span>
                @elseif($req->status == 'approved')
                <span class="inline-flex items-center gap-1 bg-green-100 text-green-800 text-xs px-2 py-0.5 rounded-full font-medium"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Approved</span>
                @else
                <span class="inline-flex items-center gap-1 bg-red-100 text-red-800 text-xs px-2 py-0.5 rounded-full font-medium"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg> Rejected</span>
                @endif
                @if($req->approver)
                <span class="text-xs text-gray-500">by {{ $req->approver->name }}</span>
                @elseif($req->makerChecker)
                <span class="text-xs text-gray-500">checked by {{ $req->makerChecker->name }}</span>
                @endif
            </div>
            @php
                $canActMobile = Auth::user()->role == 'treasurer'
                    && $req->requested_by !== Auth::id()
                    && ($req->status == 'pending'
                        || ($req->status == 'maker_checked' && $req->maker_checked_by !== Auth::id()));
            @endphp
            @if($canActMobile)
            <div class="flex gap-2">
                <button type="button" data-action="{{ route('withdrawals.approve', $req->id) }}" data-title="Approve Request" data-message="Are you sure?" data-btn-text="Approve" data-btn-class="bg-green-600 hover:bg-green-700" onclick="showConfirmModal(this.dataset.title, this.dataset.message, this.dataset.btnText, this.dataset.btnClass, this.dataset.action)" class="flex-1 min-h-[44px] flex items-center justify-center gap-1.5 bg-green-600 hover:bg-green-700 text-white font-medium text-sm px-3 py-2 rounded-lg transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> {{ $req->status == 'maker_checked' ? 'Final Approve' : 'Approve' }}</button>
                <button type="button" data-action="{{ route('withdrawals.reject', $req->id) }}" data-title="Reject Request" data-message="Are you sure?" data-btn-text="Reject" data-btn-class="bg-red-600 hover:bg-red-700" onclick="showConfirmModal(this.dataset.title, this.dataset.message, this.dataset.btnText, this.dataset.btnClass, this.dataset.action, 'POST', true)" class="flex-1 min-h-[44px] flex items-center justify-center gap-1.5 bg-red-600 hover:bg-red-700 text-white font-medium text-sm px-3 py-2 rounded-lg transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg> Reject</button>
            </div>
            @endif
        </div>
        @empty
        <div class="p-8 text-center"><svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg><p class="text-gray-500">No requests found.</p></div>
        @endforelse
    </div>

    @if($requests->hasPages())
    <div id="withdrawals-pagination" class="px-4 sm:px-6 py-3 bg-gray-50 border-t border-gray-100 flex justify-center md:hidden">
        {{ $requests->appends(request()->except('page'))->links() }}
    </div>
    @endif
</div>

{{-- Documents Modal --}}
<div id="documentsModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4" onclick="if(event.target===this)closeDocumentsModal()">
    <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full max-h-[80vh] overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                </svg>
                Supporting Documents
            </h3>
            <button onclick="closeDocumentsModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div id="documentsList" class="p-6 overflow-y-auto max-h-[60vh] space-y-3">
        </div>
    </div>
</div>

{{-- Store withdrawal data for modal --}}
@php
    $withdrawalData = [];
    foreach($requests as $req) {
        $withdrawalData[$req->id] = [
            'documents' => $req->documents->map(function($doc) {
                return [
                    'id' => $doc->id,
                    'file_name' => $doc->file_name,
                    'file_type' => $doc->file_type,
                    'file_size' => $doc->formatted_size,
                    'file_url' => $doc->file_url,
                    'description' => $doc->description,
                    'uploaded_at' => $doc->created_at->format('d M Y, h:i A'),
                    'uploader' => $doc->uploader ? $doc->uploader->name : 'Unknown',
                ];
            })->toArray(),
        ];
    }
@endphp

<script>
    const typeBalances = @json($typeBalances);
    const purposeBalances = @json($purposeBalances);
    const purposeTypeBalances = @json($purposeTypeBalances);
    const withdrawalDocs = @json($withdrawalData);

    function validateBalance() {
        const amount = parseFloat(document.querySelector('input[name="amount"]').value) || 0;
        const type = document.getElementById('withdrawalTypeSelect').value;
        const balance = typeBalances[type] || 0;
        const warning = document.getElementById('balanceWarning');
        const warningBalance = document.getElementById('warningAvailableBalance');
        if (amount > 0 && amount > balance) {
            warningBalance.textContent = 'RM ' + Number(balance).toLocaleString('en', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            warning.classList.remove('hidden');
        } else {
            warning.classList.add('hidden');
        }
    }

    function updateBalance() {
        const type = document.getElementById('withdrawalTypeSelect').value;
        const balance = typeBalances[type] || 0;
        document.getElementById('balanceDisplay').textContent = 'RM ' + balance.toLocaleString('en', {minimumFractionDigits: 0, maximumFractionDigits: 0});

        const autoPurpose = ['zakat', 'zakat_fitr', 'waqf'].includes(type);
        const sadaqahSelect = document.getElementById('fundPurposeSelectSadaqah');
        const autoDiv = document.getElementById('fundPurposeAuto');
        const autoInput = document.getElementById('fundPurposeAutoInput');

        if (autoPurpose) {
            sadaqahSelect.classList.add('hidden');
            sadaqahSelect.removeAttribute('name');
            autoDiv.classList.remove('hidden');
            autoInput.setAttribute('name', 'fund_purpose');
        } else {
            sadaqahSelect.classList.remove('hidden');
            sadaqahSelect.setAttribute('name', 'fund_purpose');
            autoDiv.classList.add('hidden');
            autoInput.removeAttribute('name');
        }

        updatePurposeBalance();
        validateBalance();
    }

    function updatePurposeBalance() {
        const type = document.getElementById('withdrawalTypeSelect').value;
        const autoPurpose = ['zakat', 'zakat_fitr', 'waqf'].includes(type);
        const purpose = autoPurpose ? 'General Fund' : document.getElementById('fundPurposeSelectSadaqah').value;
        const balance = (purposeTypeBalances[purpose] && purposeTypeBalances[purpose][type] !== undefined)
            ? purposeTypeBalances[purpose][type]
            : null;
        const display = document.getElementById('purposeBalanceDisplay');
        if (balance !== null) {
            display.textContent = 'RM ' + balance.toLocaleString('en', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        } else {
            display.textContent = '—';
        }
        updateSubmitButtonState();
    }

    function updateSubmitButtonState() {
        const amount = parseFloat(document.querySelector('input[name="amount"]').value) || 0;
        const type = document.getElementById('withdrawalTypeSelect').value;
        const autoPurpose = ['zakat', 'zakat_fitr', 'waqf'].includes(type);
        const purpose = autoPurpose ? 'General Fund' : document.getElementById('fundPurposeSelectSadaqah').value;
        const purposeBalance = (purposeTypeBalances[purpose] && purposeTypeBalances[purpose][type] !== undefined)
            ? purposeTypeBalances[purpose][type]
            : null;
        const typeBalance = typeBalances[type] || 0;
        const submitBtn = document.getElementById('submitBtn');

        const purposeBlocked = purposeBalance !== null && purposeBalance <= 0;
        const amountExceedsType = amount > 0 && amount > typeBalance;
        const amountExceedsPurpose = amount > 0 && purposeBalance !== null && amount > purposeBalance;

        if (purposeBlocked || amountExceedsType || amountExceedsPurpose) {
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    }

    function handleFileSelect(event) {
        const files = event.target.files;
        const preview = document.getElementById('filePreview');
        preview.innerHTML = '';
        preview.classList.remove('hidden');

        const MAX_FILES = 5;
        if (files.length > MAX_FILES) {
            alert('Maximum ' + MAX_FILES + ' files allowed. Only the first ' + MAX_FILES + ' will be used.');
        }

        Array.from(files).slice(0, MAX_FILES).forEach((file, index) => {
            if (file.size > 5 * 1024 * 1024) {
                alert(`${file.name} exceeds 5MB limit and will be skipped.`);
                return;
            }

            const div = document.createElement('div');
            div.className = 'flex items-center gap-3 bg-gray-50 rounded-lg p-3';

            const icon = getFileIcon(file.name);
            div.innerHTML = `
                <div class="flex-shrink-0">${icon}</div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-800 truncate">${file.name}</p>
                    <p class="text-xs text-gray-500">${(file.size / 1024).toFixed(1)} KB</p>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-600 transition flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            `;
            preview.appendChild(div);
        });
    }

    function getFileIcon(filename) {
        const ext = filename.split('.').pop().toLowerCase();
        if (ext === 'pdf') {
            return `<svg class="w-8 h-8 text-red-500" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zM6 20V4h7l5 5v11H6z"/><text x="12" y="16" text-anchor="middle" font-size="6" fill="currentColor" font-weight="bold">PDF</text></svg>`;
        }
        return `<svg class="w-8 h-8 text-blue-500" fill="currentColor" viewBox="0 0 24 24"><path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/></svg>`;
    }

    function autoFillWithdrawal() {
        const amounts = [50.00, 100.00, 150.00, 200.00, 300.00, 500.00, 750.00, 1000.00];
        const purposes = [
            'Beli minuman untuk hari raya',
            'Pembayaran elektrik bulanan masjid',
            'Beli peralatan solat baru',
            'Baiki sistem paip masjid',
            'Kos cetak bahan promosi program',
            'Beli karpet baru untuk surau',
            'Bayaran tukang bersih kawasan masjid',
            'Kos makan untuk program gotong royong',
            'Beli kipas baru untuk dewan utama',
            'Kos pengangkutan program ziarah',
            'Beli cat untuk pengecatan dinding masjid',
            'Bayaran WiFi bulanan masjid'
        ];
        const fundPurposeNames = @json($fundPurposes->pluck('name'));

        document.querySelector('input[name="amount"]').value = amounts[Math.floor(Math.random() * amounts.length)].toFixed(2);
        document.querySelector('input[name="purpose"]').value = purposes[Math.floor(Math.random() * purposes.length)];
        if (fundPurposeNames.length > 0) {
            const randomPurpose = fundPurposeNames[Math.floor(Math.random() * fundPurposeNames.length)];
            const select = document.getElementById('fundPurposeSelectSadaqah');
            for (let i = 0; i < select.options.length; i++) {
                if (select.options[i].value === randomPurpose) {
                    select.selectedIndex = i;
                    break;
                }
            }
            updatePurposeBalance();
        }
        validateBalance();
    }

    function showDocumentsModal(id) {
        const data = withdrawalDocs[id];
        if (!data || !data.documents || data.documents.length === 0) return;

        const list = document.getElementById('documentsList');
        list.innerHTML = data.documents.map(doc => {
            const isImage = ['jpg', 'jpeg', 'png'].includes(doc.file_type);
            return `
                <div class="flex items-center gap-3 bg-gray-50 rounded-lg p-3 hover:bg-gray-100 transition">
                    <div class="flex-shrink-0">${getFileIcon(doc.file_name)}</div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">${doc.file_name}</p>
                        <p class="text-xs text-gray-500">${doc.file_size} &bull; ${doc.uploaded_at} &bull; by ${doc.uploader}</p>
                        ${doc.description ? `<p class="text-xs text-gray-600 mt-0.5">${doc.description}</p>` : ''}
                    </div>
                    <a href="${doc.file_url}" target="_blank" class="flex-shrink-0 bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1.5 rounded-lg transition flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        ${isImage ? 'View' : 'Download'}
                    </a>
                </div>
            `;
        }).join('');

        document.getElementById('documentsModal').classList.remove('hidden');
    }

    function closeDocumentsModal() {
        document.getElementById('documentsModal').classList.add('hidden');
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeDocumentsModal();
    });

    document.addEventListener('DOMContentLoaded', function() {
        updateBalance();
        updatePurposeBalance();
        validateBalance();
    });
</script>

@endsection
