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
                        <td class="px-4 sm:px-6 py-3 text-gray-800">{{ $donation->user->name ?? 'Deleted User' }}</td>
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
                <div class="text-xs text-gray-500">Recorded by: {{ $donation->user->name ?? 'Deleted User' }}</div>
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
