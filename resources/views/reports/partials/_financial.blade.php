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
                Donations by Fund Type
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

    {{-- Fund Purpose Breakdown --}}
    @if(count($fundPurposeBreakdown) > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-teal-50 to-cyan-50 px-4 sm:px-6 py-4 border-b border-teal-200">
            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
                Cash Flow by Fund Purpose ({{ $monthName }} {{ $year }})
            </h3>
            <p class="text-xs text-gray-500 mt-1">Breakdown of donations (In) and withdrawals (Out) per specific fund purpose</p>
        </div>
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fund Purpose</th>
                        <th class="px-4 sm:px-6 py-3 text-right text-xs font-medium text-green-600 uppercase">In (Donations)</th>
                        <th class="px-4 sm:px-6 py-3 text-right text-xs font-medium text-red-600 uppercase">Out (Withdrawals)</th>
                        <th class="px-4 sm:px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase">Net Balance</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase w-48">Progress</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 text-sm">
                    @php $totalFpIn = 0; $totalFpOut = 0; @endphp
                    @foreach($fundPurposeBreakdown as $key => $data)
                        @php $totalFpIn += $data['in']; $totalFpOut += $data['out']; @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 sm:px-6 py-3 font-medium text-gray-800">
                                <span class="inline-flex items-center gap-1.5 bg-gray-100 text-gray-700 text-xs px-2.5 py-1 rounded-full font-medium">{{ $data['purpose'] }}</span>
                                <span class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 text-xs px-2 py-0.5 rounded-full font-medium ml-1">{{ $data['category'] }}</span>
                            </td>
                            <td class="px-4 sm:px-6 py-3 text-right font-bold text-green-700">RM {{ number_format($data['in'], 2) }}</td>
                            <td class="px-4 sm:px-6 py-3 text-right font-bold text-red-600">- RM {{ number_format($data['out'], 2) }}</td>
                            <td class="px-4 sm:px-6 py-3 text-right font-bold {{ $data['net'] >= 0 ? 'text-emerald-700' : 'text-red-700' }}">RM {{ number_format($data['net'], 2) }}</td>
                            <td class="px-4 sm:px-6 py-3">
                                @php $pct = $data['in'] > 0 ? ($data['out'] / $data['in']) * 100 : 0; @endphp
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 bg-gray-200 rounded-full h-2">
                                        <div class="h-2 rounded-full transition-all {{ $pct > 80 ? 'bg-red-500' : ($pct > 50 ? 'bg-amber-500' : 'bg-green-500') }}" style="width: {{ min($pct, 100) }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-500 w-10 text-right">{{ number_format($pct, 0) }}%</span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    @php $totalFpNet = $totalFpIn - $totalFpOut; @endphp
                    <tr class="bg-gray-50 font-bold">
                        <td class="px-4 sm:px-6 py-3 text-gray-800">Total</td>
                        <td class="px-4 sm:px-6 py-3 text-right text-green-700">RM {{ number_format($totalFpIn, 2) }}</td>
                        <td class="px-4 sm:px-6 py-3 text-right text-red-600">- RM {{ number_format($totalFpOut, 2) }}</td>
                        <td class="px-4 sm:px-6 py-3 text-right {{ $totalFpNet >= 0 ? 'text-emerald-700' : 'text-red-700' }}">RM {{ number_format($totalFpNet, 2) }}</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="md:hidden divide-y divide-gray-200">
            @foreach($fundPurposeBreakdown as $key => $data)
            <div class="p-4 space-y-2">
                <div class="flex justify-between items-start">
                    <div class="flex items-center gap-1">
                        <span class="inline-flex items-center gap-1 bg-gray-100 text-gray-700 text-xs px-2.5 py-1 rounded-full font-medium">{{ $data['purpose'] }}</span>
                        <span class="inline-flex items-center gap-1 bg-blue-50 text-blue-700 text-xs px-2 py-0.5 rounded-full font-medium">{{ $data['category'] }}</span>
                    </div>
                    <span class="text-sm font-bold {{ $data['net'] >= 0 ? 'text-emerald-700' : 'text-red-700' }}">Net: RM {{ number_format($data['net'], 2) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-green-700 font-medium">In: RM {{ number_format($data['in'], 2) }}</span>
                    <span class="text-red-600 font-medium">Out: RM {{ number_format($data['out'], 2) }}</span>
                </div>
                @php $pct = $data['in'] > 0 ? ($data['out'] / $data['in']) * 100 : 0; @endphp
                <div class="flex items-center gap-2">
                    <div class="flex-1 bg-gray-200 rounded-full h-2">
                        <div class="h-2 rounded-full {{ $pct > 80 ? 'bg-red-500' : ($pct > 50 ? 'bg-amber-500' : 'bg-green-500') }}" style="width: {{ min($pct, 100) }}%"></div>
                    </div>
                    <span class="text-xs text-gray-500">{{ number_format($pct, 0) }}% spent</span>
                </div>
            </div>
            @endforeach
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

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.1" integrity="sha384-jb8JQMbMoBUzgWatfe6COACi2ljcDdZQ2OxczGA3bGNeWe+6DChMTBJemed7ZnvJ" crossorigin="anonymous"></script>
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
                scales: { 
                    x: { ticks: { maxRotation: 45, minRotation: 0 } },
                    y: { beginAtZero: true, ticks: { callback: function(v) { return 'RM ' + v.toLocaleString(); } } } 
                }
            }
        });
    });
</script>
