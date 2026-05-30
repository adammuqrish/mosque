@extends('layouts.auth')

@section('title', 'Reset Password')

@section('heading', 'Reset Password')

@section('subheading', 'Enter your email to receive a reset link.')

@section('content')
    @if (session('success'))
        <div class="text-center">
            <div class="mx-auto w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-gray-800 mb-2">Check Your Email</h2>
            <p class="text-gray-600 text-sm mb-6">
                We've sent a password reset link to <strong class="text-gray-800">{{ session('success') }}</strong>.
                Please check your inbox and follow the instructions to reset your password.
            </p>
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6 text-left">
                <p class="text-amber-800 text-sm font-medium mb-1">Didn't receive the email?</p>
                <ul class="text-amber-700 text-xs space-y-1 list-disc list-inside">
                    <li>Check your spam or junk folder</li>
                    <li>Make sure you entered the correct email address</li>
                    <li>The link expires in 60 minutes</li>
                </ul>
            </div>
            <div class="flex flex-col gap-3">
                <form method="POST" action="{{ route('password.email') }}" data-loading>
                    @csrf
                    <button type="submit"
                        class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 px-6 rounded-lg transition text-center cursor-pointer">
                        Resend Reset Link
                    </button>
                </form>
                <a href="{{ route('login') }}"
                    class="text-emerald-600 font-bold hover:underline text-sm">Back to Login</a>
            </div>
        </div>
    @else
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg animate-slideIn">
                <p class="text-red-800 font-semibold">Error</p>
                <p class="text-red-700 text-sm">{{ $errors->first() }}</p>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" data-loading>
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
                Send Reset Link
            </button>
        </form>

        <div class="mt-6 text-center text-sm">
            <a href="{{ route('login') }}" class="text-emerald-600 font-bold hover:underline">Back to Login</a>
        </div>
    @endif
@endsection
