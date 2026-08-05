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
                        <td class="px-4 sm:px-6 py-3 text-gray-800">{{ $wd->requester->name ?? 'Deleted User' }}</td>
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
