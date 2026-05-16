@extends('layouts.app')

@section('back', '/dashboard')

@section('title', __('islamic.navigation.requests'))

@section('content')

<!-- STEP 1: Form Request (Admin only) -->
@if(Auth::user()->role == 'admin')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-8 border-l-4 border-emerald-500">
    <!-- Card Header with Section Title -->
    <div class="px-4 sm:px-6 py-4 border-b border-t-2 border-t-[#C5A059] border-gray-100 bg-gray-50/50 flex justify-between items-center">
        <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            Request Withdrawal
        </h2>
        <span class="text-xs text-gray-500">For expenses</span>
    </div>

    <!-- Card Body with Form -->
    <div class="p-6">
        <form action="/withdrawals" method="POST" data-loading>
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:p-6">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Amount (RM)</label>
                    <input type="number" step="0.01" name="amount"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        placeholder="0.00" required>
                    @error('amount')
                    <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"></path>
                        </svg>
                        {{ $message }}
                    </p>
                    @enderror
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
                    <label class="block text-gray-700 text-sm font-bold mb-2">Purpose</label>
                    <input type="text" name="purpose"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        placeholder="e.g., Beli minuman hari raya" required>
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
            <div class="mt-6 flex flex-col sm:flex-row gap-3">
                <button type="button" onclick="autoFillWithdrawal()"
                    class="w-full sm:w-auto bg-blue-400 hover:bg-blue-500 text-white font-semibold py-2.5 px-4 rounded-lg transition flex items-center justify-center gap-2 min-h-[44px]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Auto Fill
                </button>
                <button type="submit"
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
    <!-- Card Header -->
    <div class="px-4 sm:px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
        <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Request History
        </h2>

        <!-- STEP 1: Summary badges -->
        @php
        // $pending, $approved, $rejected passed from controller
        @endphp
        <div class="flex flex-wrap gap-2">
            <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full flex items-center gap-1">
                <span class="w-2 h-2 bg-yellow-500 rounded-full"></span>
                {{ $pending }} Pending
            </span>
            <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">
                {{ $approved }} Approved
            </span>
            <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full">
                {{ $rejected }} Rejected
            </span>
        </div>

        {{-- Summary Cards: Outflow by Type --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 px-4 sm:px-6 pb-2">
            <div class="bg-[#C5A059]/10 rounded-lg p-3 border border-[#C5A059]/20">
                <p class="text-[10px] font-semibold text-[#C5A059] uppercase">Zakat Out</p>
                <p class="text-lg font-bold text-[#C5A059]">RM {{ number_format($zakatOut, 2) }}</p>
            </div>
            <div class="bg-amber-50 rounded-lg p-3 border border-amber-200">
                <p class="text-[10px] font-semibold text-amber-700 uppercase">Zakat Fitr Out</p>
                <p class="text-lg font-bold text-amber-700">RM {{ number_format($zakatFitrOut, 2) }}</p>
            </div>
            <div class="bg-blue-50 rounded-lg p-3 border border-blue-200">
                <p class="text-[10px] font-semibold text-blue-700 uppercase">Sadaqah Out</p>
                <p class="text-lg font-bold text-blue-700">RM {{ number_format($sadaqahOut, 2) }}</p>
            </div>
            <div class="bg-purple-50 rounded-lg p-3 border border-purple-200">
                <p class="text-[10px] font-semibold text-purple-700 uppercase">Waqf Out</p>
                <p class="text-lg font-bold text-purple-700">RM {{ number_format($waqfOut, 2) }}</p>
            </div>
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
                        Requested By
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'purpose', 'direction' => $sort === 'purpose' && $direction === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-700">
                            Purpose
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
                    <!-- <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th> -->
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
                    <td class="px-4 py-3 text-sm text-gray-700 font-medium">{{ $req->requester->name }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600 max-w-xs truncate">{{ $req->purpose }}</td>
                    <td class="px-4 py-3 text-sm font-bold text-emerald-600">RM {{ number_format($req->amount, 2) }}</td>
                    <td class="px-4 py-3 text-sm">
                        @if($req->status == 'pending')
                        <span class="inline-flex items-center gap-1 bg-yellow-100 text-yellow-800 text-xs px-3 py-1 rounded-full font-medium">
                            <span class="w-2 h-2 bg-yellow-500 rounded-full animate-pulse"></span>
                            Pending
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
                        @if(Auth::user()->role == 'treasurer' && $req->status == 'pending')
                        <div class="flex gap-1">
                            <button type="button" data-action="{{ route('withdrawals.approve', $req->id) }}" data-title="Approve Request" data-message="Are you sure?" data-btn-text="Approve" data-btn-class="bg-green-600 hover:bg-green-700" onclick="showConfirmModal(this.dataset.title, this.dataset.message, this.dataset.btnText, this.dataset.btnClass, this.dataset.action)" class="inline-flex items-center gap-1 bg-green-600 hover:bg-green-700 text-white font-medium py-1.5 px-3 rounded text-xs transition">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Approve
                            </button>
                            <button type="button" data-action="{{ route('withdrawals.reject', $req->id) }}" data-title="Reject Request" data-message="Are you sure?" data-btn-text="Reject" data-btn-class="bg-red-600 hover:bg-red-700" onclick="showConfirmModal(this.dataset.title, this.dataset.message, this.dataset.btnText, this.dataset.btnClass, this.dataset.action, 'POST', true)" class="inline-flex items-center gap-1 bg-red-600 hover:bg-red-700 text-white font-medium py-1.5 px-3 rounded text-xs transition">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Reject
                            </button>
                        </div>
                        @elseif(in_array($req->status, ['approved', 'rejected']))
                        <span class="text-xs text-gray-500">
                            {{ $req->approver->name ?? '-' }}
                            <span class="text-gray-400">&bull;</span>
                            {{ $req->approved_at ? $req->approved_at->format('d M Y') : '' }}
                        </span>
                        @else
                        <span class="text-xs text-gray-400">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-8 text-gray-500">No requests found.</td>
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
                </div>
                <p class="text-lg font-bold text-emerald-600">RM {{ number_format($req->amount, 2) }}</p>
            </div>
            <p class="text-sm text-gray-600 mb-3">{{ $req->purpose }}</p>
            <div class="flex items-center justify-between mb-3">
                @if($req->status == 'pending')
                <span class="inline-flex items-center gap-1 bg-yellow-100 text-yellow-800 text-xs px-2 py-0.5 rounded-full font-medium"><span class="w-2 h-2 bg-yellow-500 rounded-full animate-pulse"></span> Pending</span>
                @elseif($req->status == 'approved')
                <span class="inline-flex items-center gap-1 bg-green-100 text-green-800 text-xs px-2 py-0.5 rounded-full font-medium"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Approved</span>
                @else
                <span class="inline-flex items-center gap-1 bg-red-100 text-red-800 text-xs px-2 py-0.5 rounded-full font-medium"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg> Rejected</span>
                @endif
                @if($req->approver)
                <span class="text-xs text-gray-500">by {{ $req->approver->name }}</span>
                @endif
            </div>
            @if(Auth::user()->role == 'treasurer' && $req->status == 'pending')
            <div class="flex gap-2">
                <button type="button" data-action="{{ route('withdrawals.approve', $req->id) }}" data-title="Approve Request" data-message="Are you sure?" data-btn-text="Approve" data-btn-class="bg-green-600 hover:bg-green-700" onclick="showConfirmModal(this.dataset.title, this.dataset.message, this.dataset.btnText, this.dataset.btnClass, this.dataset.action)" class="flex-1 min-h-[44px] flex items-center justify-center gap-1.5 bg-green-600 hover:bg-green-700 text-white font-medium text-sm px-3 py-2 rounded-lg transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Approve</button>
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

<script>
    const typeBalances = @json($typeBalances);

    function updateBalance() {
        const type = document.getElementById('withdrawalTypeSelect').value;
        const balance = typeBalances[type] || 0;
        document.getElementById('balanceDisplay').textContent = 'RM ' + balance.toLocaleString('en', {minimumFractionDigits: 0, maximumFractionDigits: 0});
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

        document.querySelector('input[name="amount"]').value = amounts[Math.floor(Math.random() * amounts.length)].toFixed(2);
        document.querySelector('input[name="purpose"]').value = purposes[Math.floor(Math.random() * purposes.length)];
    }

    document.addEventListener('DOMContentLoaded', function() {
        updateBalance();
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('page')) {
            const table = document.getElementById('withdrawals-table');
            if (table) {
                setTimeout(() => {
                    table.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }, 100);
            }
        }
    });
</script>

@endsection

