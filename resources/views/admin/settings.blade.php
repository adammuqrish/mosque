@php
    $back = route('dashboard');
@endphp

@extends('layouts.app')

@section('title', 'Registration Codes')

@php
    $breadcrumbs = [
        ['label' => 'Registration Codes'],
    ];
@endphp

@section('content')
<div class="container mx-auto px-4 sm:px-6 max-w-2xl">
    <div class="bg-white rounded-lg shadow-md p-6 mt-6">
        <h1 class="text-2xl font-bold mb-6">Registration Codes</h1>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="space-y-6" x-data="{ showCodes: false }">
            <div class="flex items-center justify-end gap-3 mb-2">
                <span class="text-sm text-gray-500" x-text="showCodes ? 'Codes Visible' : 'Codes Hidden'"></span>
                <button type="button" @click="showCodes = !showCodes"
                    class="relative inline-flex items-center h-6 rounded-full w-11 transition-colors"
                    :class="showCodes ? 'bg-blue-600' : 'bg-gray-300'"
                    role="switch" :aria-checked="showCodes">
                    <span class="inline-block w-4 h-4 transform rounded-full bg-white transition-transform"
                        :class="showCodes ? 'translate-x-6' : 'translate-x-1'"></span>
                </button>
            </div>

            <div class="border rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold">Admin Code</h2>
                        <p class="text-sm text-gray-500">Use this code to register as an Admin</p>
                    </div>
                </div>
                <div class="mt-3 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    <code class="bg-gray-100 px-4 py-2.5 rounded text-base sm:text-lg font-mono select-all break-all text-center sm:text-left">
                        <span x-show="showCodes">{{ $adminCode ?? '— No code set —' }}</span>
                        <span x-show="!showCodes">{{ $adminCode ? str_repeat('•', strlen($adminCode)) : '— No code set —' }}</span>
                    </code>
                    <button type="button" onclick="showConfirmModal('Regenerate Admin Code', 'Change Admin code? The old code will stop working immediately.', 'Regenerate', 'bg-red-600 hover:bg-red-700', '{{ route('admin.settings.regenerate-admin') }}', 'POST')" class="w-full sm:w-auto bg-blue-600 text-white px-4 py-2.5 rounded hover:bg-blue-700 text-sm">
                        Regenerate
                    </button>
                </div>
            </div>

            <div class="border rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold">Treasurer Code</h2>
                        <p class="text-sm text-gray-500">Use this code to register as a Treasurer</p>
                    </div>
                </div>
                <div class="mt-3 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    <code class="bg-gray-100 px-4 py-2.5 rounded text-base sm:text-lg font-mono select-all break-all text-center sm:text-left">
                        <span x-show="showCodes">{{ $treasurerCode ?? '— No code set —' }}</span>
                        <span x-show="!showCodes">{{ $treasurerCode ? str_repeat('•', strlen($treasurerCode)) : '— No code set —' }}</span>
                    </code>
                    <button type="button" onclick="showConfirmModal('Regenerate Treasurer Code', 'Change Treasurer code? The old code will stop working immediately.', 'Regenerate', 'bg-red-600 hover:bg-red-700', '{{ route('admin.settings.regenerate-treasurer') }}', 'POST')" class="w-full sm:w-auto bg-blue-600 text-white px-4 py-2.5 rounded hover:bg-blue-700 text-sm">
                        Regenerate
                    </button>
                </div>
            </div>
        </div>

        <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded p-4 text-sm text-yellow-800">
            <strong>Note:</strong> When you change a code, the old code stops working immediately. Share the new code with anyone who needs it.
        </div>
    </div>
</div>
@endsection
