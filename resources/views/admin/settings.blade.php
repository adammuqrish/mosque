@php
    $back = route('dashboard');
@endphp

@extends('layouts.app')

@section('title', 'Tetapan Kod Pendaftaran')

@section('content')
<div class="container mx-auto px-4 sm:px-6 max-w-2xl">
    <div class="bg-white rounded-lg shadow-md p-6 mt-6">
        <h1 class="text-2xl font-bold mb-6">Tetapan Kod Pendaftaran</h1>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="space-y-6">
            <div class="border rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold">Kod Admin</h2>
                        <p class="text-sm text-gray-500">Gunakan kod ini untuk mendaftar sebagai Admin</p>
                    </div>
                </div>
                <div class="mt-3 flex items-center gap-3">
                    <code class="bg-gray-100 px-4 py-2 rounded text-lg font-mono select-all">
                        {{ $adminCode ?? '— Tiada kod —' }}
                    </code>
                    <form method="POST" action="{{ route('admin.settings.regenerate-admin') }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm"
                            onclick="return confirm('Tukar kod Admin? Kod lama akan tidak sah serta-merta.')">
                            Tukar Kod
                        </button>
                    </form>
                </div>
            </div>

            <div class="border rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold">Kod Bendahari</h2>
                        <p class="text-sm text-gray-500">Gunakan kod ini untuk mendaftar sebagai Bendahari</p>
                    </div>
                </div>
                <div class="mt-3 flex items-center gap-3">
                    <code class="bg-gray-100 px-4 py-2 rounded text-lg font-mono select-all">
                        {{ $treasurerCode ?? '— Tiada kod —' }}
                    </code>
                    <form method="POST" action="{{ route('admin.settings.regenerate-treasurer') }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm"
                            onclick="return confirm('Tukar kod Bendahari? Kod lama akan tidak sah serta-merta.')">
                            Tukar Kod
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded p-4 text-sm text-yellow-800">
            <strong>Peringatan:</strong> Apabila anda menukar kod, kod lama akan terus tidak sah. Sila maklumkan kod baru kepada sesiapa yang memerlukannya.
        </div>
    </div>
</div>
@endsection
