@extends('layouts.auth')

@section('title', 'Resend Verification')

@section('heading', 'Resend Verification')

@section('subheading', 'Hantar semula pautan pengesahan e-mel.')

@section('content')
    @if (session('success'))
        <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 rounded-lg animate-slideIn">
            <p class="text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg animate-slideIn">
            <p class="text-red-800 font-semibold">Error</p>
            <p class="text-red-700 text-sm">{{ $errors->first() }}</p>
        </div>
    @endif

    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6">
        <p class="text-amber-800 text-sm">Masukkan alamat e-mel anda. Jika akaun anda belum disahkan, kami akan hantar semula pautan pengesahan.</p>
    </div>

    <form method="POST" action="{{ route('verification.resend') }}" data-loading>
        @csrf

        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2">Email Address</label>
            <input type="email" name="email"
                class="shadow appearance-none border rounded-lg w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-emerald-500 transition @error('email') border-red-500 ring-2 ring-red-200 @enderror"
                placeholder="you@example.com" value="{{ old('email') }}" required autofocus>
            @error('email')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 px-6 rounded-lg transition min-h-[48px]" type="submit">
            Resend Verification Email
        </button>
    </form>

    <div class="mt-6 text-center text-sm">
        <a href="{{ route('login') }}" class="text-emerald-600 font-bold hover:underline">Back to Login</a>
    </div>
@endsection
