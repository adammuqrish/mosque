@extends('layouts.app')

@section('title', 'Manage Amils')

@php
    $breadcrumbs = [
        ['label' => 'Manage Amils'],
    ];
@endphp

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Manage Amils</h1>
    <p class="text-gray-500 text-sm mt-1">Appoint or revoke Amil authorization. Only appointed Amils can be selected as the Amil for Zakat akad records.</p>
</div>

{{-- Search --}}
<div class="mb-6">
    <form method="GET" action="{{ route('admin.amils') }}" class="flex flex-col sm:flex-row gap-3">
        <input type="text" name="search" placeholder="Search by name or email..." value="{{ $search ?? '' }}" class="w-full sm:max-w-md border rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-6 py-2.5 rounded-lg transition text-sm">Search</button>
        @if($search)
            <a href="{{ route('admin.amils') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold px-6 py-2.5 rounded-lg transition text-sm text-center">Clear</a>
        @endif
    </form>
</div>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    {{-- Desktop Table --}}
    <div class="hidden md:block overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amil</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-3 text-sm font-medium text-gray-800">{{ $user->name }}</td>
                    <td class="px-6 py-3 text-sm text-gray-600">{{ $user->email }}</td>
                    <td class="px-6 py-3 text-sm">
                        <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold
                            @if($user->role === 'admin') bg-purple-100 text-purple-800
                            @elseif($user->role === 'treasurer') bg-blue-100 text-blue-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td class="px-6 py-3 text-sm">
                        @if($user->is_amil)
                            <span class="inline-flex items-center gap-1 text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full text-xs font-semibold">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                Appointed
                            </span>
                        @else
                            <span class="text-gray-400 text-xs">Not appointed</span>
                        @endif
                    </td>
                    <td class="px-6 py-3 text-sm">
                        @if($user->is_amil)
                            <button type="button" onclick="showConfirmModal('Revoke Amil', 'Revoke Amil status from {{ $user->name }}? They will no longer be able to record Zakat akad.', 'Revoke', 'bg-red-600 hover:bg-red-700', '{{ route('admin.amils.toggle', $user) }}', 'POST')" class="text-red-600 hover:text-red-800 text-sm font-medium">Revoke</button>
                        @else
                            <button type="button" onclick="showConfirmModal('Appoint Amil', 'Appoint {{ $user->name }} as an Amil? They will be able to record Zakat akad.', 'Appoint', 'bg-emerald-600 hover:bg-emerald-700', '{{ route('admin.amils.toggle', $user) }}', 'POST')" class="text-emerald-600 hover:text-emerald-800 text-sm font-medium">Appoint</button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500 text-sm">No users found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile Cards --}}
    <div class="md:hidden divide-y divide-gray-200">
        @forelse($users as $user)
        <div class="p-4 space-y-3">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-sm font-medium text-gray-800">{{ $user->name }}</span>
                    <span class="text-xs text-gray-500 ml-2">{{ $user->email }}</span>
                </div>
                <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold
                    @if($user->role === 'admin') bg-purple-100 text-purple-800
                    @elseif($user->role === 'treasurer') bg-blue-100 text-blue-800
                    @else bg-gray-100 text-gray-800 @endif">
                    {{ ucfirst($user->role) }}
                </span>
            </div>
            <div class="flex items-center justify-between">
                <div>
                    @if($user->is_amil)
                        <span class="inline-flex items-center gap-1 text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full text-xs font-semibold">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            Appointed
                        </span>
                    @else
                        <span class="text-gray-400 text-xs">Not appointed</span>
                    @endif
                </div>
                @if($user->is_amil)
                    <button type="button" onclick="showConfirmModal('Revoke Amil', 'Revoke Amil status from {{ $user->name }}? They will no longer be able to record Zakat akad.', 'Revoke', 'bg-red-600 hover:bg-red-700', '{{ route('admin.amils.toggle', $user) }}', 'POST')" class="text-red-600 hover:text-red-800 text-sm font-medium">Revoke</button>
                @else
                    <button type="button" onclick="showConfirmModal('Appoint Amil', 'Appoint {{ $user->name }} as an Amil? They will be able to record Zakat akad.', 'Appoint', 'bg-emerald-600 hover:bg-emerald-700', '{{ route('admin.amils.toggle', $user) }}', 'POST')" class="text-emerald-600 hover:text-emerald-800 text-sm font-medium">Appoint</button>
                @endif
            </div>
        </div>
        @empty
        <div class="p-8 text-center text-gray-500 text-sm">No users found.</div>
        @endforelse
    </div>
</div>

{{-- Pagination --}}
<div class="mt-6">
    {{ $users->appends(['search' => $search])->links() }}
</div>

<div class="mt-4 text-xs text-gray-400">
    <p>Only users appointed as Amil will appear in the Amil dropdown when recording Zakat donations.</p>
</div>

@endsection
