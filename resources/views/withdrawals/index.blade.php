@extends('layouts.app')

@section('title', 'Withdrawal Requests')

@section('content')

    <!-- SECTION 1: FORM REQUEST (Hanya ADMIN nampak) -->
    @if(Auth::user()->role == 'admin')
        <div class="bg-white rounded-lg shadow-md p-6 mb-8 border-l-4 border-blue-500">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Request Withdrawal</h2>

            <form action="/withdrawals" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Amount (RM)</label>
                        <input type="number" step="0.01" name="amount" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Purpose</label>
                        <input type="text" name="purpose" class="w-full border rounded px-3 py-2"
                            placeholder="e.g., Beli minuman hari raya" required>
                    </div>
                </div>
                <div class="mt-6">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded shadow">
                        Submit Request
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- SECTION 2: SENARAI REQUESTS -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b">
            <h2 class="text-xl font-bold text-gray-800">Request History</h2>
        </div>
        <div class="p-6 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Requested By</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Purpose</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($requests as $req)
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $req->created_at->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $req->requester->name }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $req->purpose }}</td>
                            <td class="px-4 py-3 text-sm font-bold text-gray-900">RM {{ number_format($req->amount, 2) }}</td>

                            <!-- Status Badge -->
                            <td class="px-4 py-3 text-sm">
                                @if($req->status == 'pending')
                                    <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full">Pending</span>
                                @elseif($req->status == 'approved')
                                    <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Approved</span>
                                @else
                                    <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full">Rejected</span>
                                @endif
                            </td>

                            <!-- Action Buttons (Hanya BENDAHARI boleh approve/reject yang status PENDING) -->
                            <td class="px-4 py-3 text-sm">
                                @if(Auth::user()->role == 'treasurer' && $req->status == 'pending')
                                    <form action="{{ route('withdrawals.approve', $req->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit"
                                            class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mr-2">Approve</button>
                                    </form>
                                    <form action="{{ route('withdrawals.reject', $req->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit"
                                            class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded mr-2">Reject</button>
                                    </form>
                                @elseif(in_array($req->status, ['approved', 'rejected']))
                                    @if($req->approver)
                                        <span class="text-xs text-gray-500">By {{ $req->approver->name }}</span>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-gray-500">No requests found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection