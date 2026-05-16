@extends('layouts.app')

@section('title', 'Manage Fund Purposes')

@section('back', '/donations')

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Manage Fund Purposes</h1>
    <p class="text-gray-500 text-sm mt-1">Add, edit, or remove fund purpose suggestions for the donation form.</p>
</div>

@if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm font-medium">{{ session('success') }}</div>
@endif

<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h2 class="text-lg font-bold text-gray-800 mb-4">Add New Purpose</h2>
    <form action="{{ route('donations.fund-purposes.store') }}" method="POST" class="flex flex-col sm:flex-row gap-3">
        @csrf
        <input type="text" name="name" placeholder="e.g. Kipas Gergasi" class="w-full sm:flex-1 border rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none" required maxlength="100">
        <button type="submit" class="w-full sm:w-auto bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-6 py-2.5 rounded-lg transition text-sm">Add</button>
    </form>
    @error('name')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    {{-- Desktop Table --}}
    <div class="hidden md:block overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sort</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Active</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($purposes as $p)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-3 text-sm text-gray-500">{{ $loop->iteration }}</td>
                    <td class="px-6 py-3 text-sm font-medium text-gray-800">
                        <form action="{{ route('donations.fund-purposes.update', $p) }}" method="POST" class="flex items-center gap-2">
                            @csrf @method('PUT')
                            <input type="text" name="name" value="{{ $p->name }}" class="border rounded px-2 py-1 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none w-40" required maxlength="100">
                            <button type="submit" class="text-emerald-600 hover:text-emerald-800 text-sm font-medium whitespace-nowrap">Save</button>
                        </form>
                    </td>
                    <td class="px-6 py-3 text-sm text-gray-600">{{ $p->sort_order }}</td>
                    <td class="px-6 py-3 text-sm">{{ $p->is_active ? 'Yes' : 'No' }}</td>
                    <td class="px-6 py-3 text-sm">
                        <button type="button" onclick="showConfirmModal('Delete Fund Purpose', 'Delete this fund purpose? It will no longer appear as a suggestion chip.', 'Delete', 'bg-red-600 hover:bg-red-700', '{{ route('donations.fund-purposes.destroy', $p) }}', 'DELETE')" class="text-red-600 hover:text-red-800 text-sm font-medium">Delete</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500 text-sm">No fund purposes yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile Cards --}}
    <div class="md:hidden divide-y divide-gray-200">
        @forelse($purposes as $p)
        <div class="p-4 space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-gray-800">{{ $p->name }}</span>
                <span class="text-xs text-gray-400">#{{ $loop->iteration }}</span>
            </div>
            <div class="flex items-center gap-4 text-xs text-gray-500">
                <span>Sort: {{ $p->sort_order }}</span>
                <span>{{ $p->is_active ? 'Active' : 'Inactive' }}</span>
            </div>
            <div class="flex items-center gap-2 pt-1">
                <button type="button" onclick="showConfirmModal('Delete Fund Purpose', 'Delete this fund purpose? It will no longer appear as a suggestion chip.', 'Delete', 'bg-red-600 hover:bg-red-700', '{{ route('donations.fund-purposes.destroy', $p) }}', 'DELETE')" class="text-red-600 hover:text-red-800 text-sm font-medium">Delete</button>
            </div>
        </div>
        @empty
        <div class="p-8 text-center text-gray-500 text-sm">No fund purposes yet.</div>
        @endforelse
    </div>
</div>

<div class="mt-4 text-xs text-gray-400">
    <p>These purposes appear as suggestion chips in the donation form. The Sort Order determines their display sequence.</p>
</div>

@endsection
