@extends('layouts.app')

@section('title', 'Manage Donations')

@section('content')

    <!-- FORM ADD DONATION -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Record New Donation</h2>

        <form action="/donations" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Amount (RM)</label>
                    <input type="number" step="0.01" name="amount" class="w-full border rounded px-3 py-2" required>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Category</label>
                    <input type="text" name="category" class="w-full border rounded px-3 py-2"
                        placeholder="e.g., Kutusan Jumaat" required>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Source</label>
                    <select name="source" class="w-full border rounded px-3 py-2">
                        <option value="cash">Cash (Box/Tepi Masjid)</option>
                        <option value="online">Online (Bank Transfer)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Date Received</label>
                    <input type="date" name="donation_date" class="w-full border rounded px-3 py-2" required>
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Description (Optional)</label>
                <textarea name="description" class="w-full border rounded px-3 py-2" rows="2"></textarea>
            </div>

            <div class="mt-6">
                <button type="submit"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-6 rounded shadow">
                    Save Donation
                </button>
            </div>
        </form>
    </div>

    <!-- TABLE HISTORY -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b">
            <h2 class="text-xl font-bold text-gray-800">Donation History</h2>
        </div>
        <div class="p-6 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Source</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Recorded By</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($donations as $donation)
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $donation->donation_date->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $donation->category }}</td>
                            <td class="px-4 py-3 text-sm">
                                @if($donation->source == 'cash')
                                    <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">Cash</span>
                                @else
                                    <span class="bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded-full">Online</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm font-bold text-gray-900">RM {{ number_format($donation->amount, 2) }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $donation->user->name }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-gray-500">No donations recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection