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
