@extends('layouts.app')

{{-- Back goes to previous page via layout default --}}

@section('title', __('islamic.donations.page_title'))

@php
$breadcrumbs = [
['label' => __('islamic.donations.nav_label')],
];
@endphp

@section('content')

@if(Auth::user()->role == 'admin')
<div class="bg-white rounded-lg shadow-md p-4 sm:p-6 mb-8">
    <div class="flex items-center justify-between mb-4 border-b pb-3 gap-2">
        <h2 class="text-lg sm:text-xl font-bold text-gray-800 flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Record Contribution
        </h2>
        <a href="{{ route('donations.fund-purposes') }}" class="text-xs sm:text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium px-2.5 sm:px-3 py-1.5 rounded-lg transition flex items-center gap-1 whitespace-nowrap">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
            </svg>
            Purposes
        </a>
    </div>

    {{-- Tabs --}}
    <div class="flex border-b border-gray-200 mb-6">
        <button type="button" onclick="switchDonationTab('zakat-waqf')"
            class="tab-btn px-5 py-2.5 text-sm font-medium border-b-2 transition -mb-px active"
            data-tab="zakat-waqf">
            <svg class="w-4 h-4 inline -mt-0.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Zakat / Waqf
        </button>
        <button type="button" onclick="switchDonationTab('sadaqah')"
            class="tab-btn px-5 py-2.5 text-sm font-medium border-b-2 transition -mb-px text-gray-500 border-transparent hover:text-gray-700 hover:border-gray-300"
            data-tab="sadaqah">
            <svg class="w-4 h-4 inline -mt-0.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            Sadaqah
        </button>
    </div>

    {{-- Tab: Zakat / Waqf --}}
    <div id="tab-zakat-waqf" class="tab-panel">
        @php
        $donationStep = 1;
        if ($errors->has('amil_name') || $errors->has('akad_date') || $errors->has('akad_notes') || $errors->has('amil_user_id')) {
        $donationStep = 3;
        } elseif ($errors->has('donor_name') || $errors->has('donor_ic') || $errors->has('donor_phone') || $errors->has('donor_email') || $errors->has('donor_address')) {
        $donationStep = 2;
        }
        @endphp

        <form action="/donations" method="POST" data-loading>
            @csrf

            @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="font-semibold text-red-800">Please fix the following errors:</p>
                        <ul class="list-disc list-inside text-sm text-red-700 mt-1 space-y-0.5">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif

            <div x-data="donationForm({{ $donationStep }}, '{{ old('category', '') }}')">
                {{-- Step Indicators --}}
                <div class="flex items-center mb-6">
                    <template x-for="(s, i) in ['Amount &amp; Details', 'Donor Information', 'Akad (Contract)']" :key="i">
                        <div class="flex items-center flex-1">
                            <div class="flex flex-col items-center gap-1">
                                <div :class="step >= i + 1 ? 'bg-emerald-600 text-white' : 'bg-gray-200 text-gray-500'"
                                    class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-colors duration-300"
                                    x-text="i + 1">
                                </div>
                                <span :class="step === i + 1 ? 'text-emerald-700 font-semibold' : 'text-gray-400'"
                                    class="text-[10px] leading-tight text-center hidden sm:inline transition-colors duration-300 max-w-[80px]"
                                    x-text="s">
                                </span>
                            </div>
                            <div x-show="i < 2" :class="step > i + 1 ? 'bg-emerald-400' : 'bg-gray-200'"
                                class="flex-1 h-0.5 mx-2 mt-[-1.25rem] transition-colors duration-300">
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Step 1: Amount & Category --}}
                <div x-show="step === 1" x-transition.opacity.duration.200ms>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">{{ __('islamic.donations.amount_label') }}</label>
                            <div class="relative">
                                <input type="number" step="0.01" name="amount"
                                    class="w-full border rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition @error('amount') border-red-500 ring-2 ring-red-200 @enderror"
                                    placeholder="0.00" value="{{ old('amount') }}" required>
                            </div>
                            @error('amount')
                            <p class="text-red-500 text-sm mt-1 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"></path>
                                </svg>
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Fund Type</label>
                            <select name="category" id="categorySelect" x-model="selectedCategory"
                                class="w-full border rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition @error('category') border-red-500 ring-2 ring-red-200 @enderror"
                                required>
                                <option value="" disabled>Select Fund Type</option>
                                <optgroup label="Obligatory (Wajib)">
                                    <option value="zakat" {{ old('category') == 'zakat' ? 'selected' : '' }}>Zakat</option>
                                    <option value="zakat_fitr" {{ old('category') == 'zakat_fitr' ? 'selected' : '' }}>Zakat Fitr</option>
                                </optgroup>
                                <optgroup label="Endowment (Waqf)">
                                    <option value="waqf" {{ old('category') == 'waqf' ? 'selected' : '' }}>Waqf</option>
                                </optgroup>
                            </select>
                            @error('category')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Fund Purpose</label>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 bg-emerald-100 text-emerald-800 text-sm font-medium px-3 py-2 rounded-lg border border-emerald-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                General Fund
                            </span>
                            <span class="text-xs text-gray-400">(Auto-set — Zakat/Waqf donations are pooled into the general fund)</span>
                        </div>
                        <input type="hidden" name="fund_purpose" value="General Fund">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">{{ __('islamic.donations.source_label') }}</label>
                            <select name="source" class="w-full border rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition">
                                <option value="cash" {{ old('source') == 'cash' ? 'selected' : '' }}>{{ __('islamic.donations.sources.cash') }}</option>
                                <option value="online" {{ old('source') == 'online' ? 'selected' : '' }}>{{ __('islamic.donations.sources.online') }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">{{ __('islamic.donations.date_label') }}</label>
                            <input type="date" name="donation_date"
                                class="w-full border rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition @error('donation_date') border-red-500 ring-2 ring-red-200 @enderror"
                                value="{{ old('donation_date', date('Y-m-d')) }}" required>
                            @error('donation_date')
                            <p class="text-red-500 text-sm mt-1 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"></path>
                                </svg>
                                {{ $message }}
                            </p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">{{ __('islamic.donations.description_label') }} <span class="text-gray-400 font-normal">(Optional)</span></label>
                        <textarea name="description" rows="2"
                            class="w-full border rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition resize-none @error('description') border-red-500 ring-2 ring-red-200 @enderror"
                            placeholder="{{ __('islamic.donations.description_placeholder') }}">{{ old('description') }}</textarea>
                    </div>

                    <div class="mt-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Reference <span class="text-gray-400 font-normal">(Optional — bank ref, WhatsApp ref, receipt no.)</span></label>
                        <input type="text" name="reference"
                            class="w-full border rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition @error('reference') border-red-500 ring-2 ring-red-200 @enderror"
                            placeholder="e.g. BANK-12345, WA-msg-001, receipt #001" value="{{ old('reference') }}">
                        @error('reference')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Step 2: Donor Information --}}
                <div x-show="step === 2" x-transition.opacity.duration.200ms>
                    <div class="border-t-2 border-[#C5A059] pt-4">
                        <div class="flex items-center gap-2 mb-1">
                            <svg class="w-5 h-5 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <h3 class="font-bold text-gray-800">Donor Information</h3>
                            <span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-0.5 rounded-full">REQUIRED</span>
                        </div>
                        <p class="text-xs text-gray-500 mb-4">Donor details are required for Zakat and Waqf donations per Shariah requirements.</p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Donor Name <span class="text-red-500">*</span></label>
                                <input type="text" name="donor_name"
                                    class="w-full border rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition @error('donor_name') border-red-500 ring-2 ring-red-200 @enderror"
                                    placeholder="e.g. Ali bin Ahmad" value="{{ old('donor_name') }}">
                                @error('donor_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Donor IC / MyKad <span class="text-red-500">*</span></label>
                                <input type="text" name="donor_ic"
                                    class="w-full border rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition @error('donor_ic') border-red-500 ring-2 ring-red-200 @enderror"
                                    placeholder="010203-10-1234" value="{{ old('donor_ic') }}">
                                @error('donor_ic')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Phone <span class="text-gray-400 font-normal">(Optional)</span></label>
                                <input type="text" name="donor_phone"
                                    class="w-full border rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition @error('donor_phone') border-red-500 ring-2 ring-red-200 @enderror"
                                    placeholder="012-3456789" value="{{ old('donor_phone') }}">
                                @error('donor_phone')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Email <span class="text-gray-400 font-normal">(Optional)</span></label>
                                <input type="email" name="donor_email"
                                    class="w-full border rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition @error('donor_email') border-red-500 ring-2 ring-red-200 @enderror"
                                    placeholder="ali@example.com" value="{{ old('donor_email') }}">
                                @error('donor_email')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Address <span class="text-gray-400 font-normal">(Optional)</span></label>
                                <textarea name="donor_address" rows="2"
                                    class="w-full border rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition resize-none @error('donor_address') border-red-500 ring-2 ring-red-200 @enderror"
                                    placeholder="Donor's home address">{{ old('donor_address') }}</textarea>
                                @error('donor_address')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Step 3: Akad (Contract) --}}
                <div x-show="step === 3" x-transition.opacity.duration.200ms>
                    <div x-show="!isWaqf">
                        <div class="border-t-2 border-[#C5A059] pt-4">
                            <div class="flex items-center gap-2 mb-1">
                                <svg class="w-5 h-5 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <h3 class="font-bold text-gray-800">Akad Details</h3>
                                <span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-0.5 rounded-full">REQUIRED</span>
                            </div>
                            <p class="text-xs text-gray-500 mb-4">Zakat akad (contract) information — who conducted the akad and when.</p>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Amil Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="amil_name"
                                        class="w-full border rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition @error('amil_name') border-red-500 ring-2 ring-red-200 @enderror"
                                        placeholder="e.g. Ustaz Mohamad" value="{{ old('amil_name') }}">
                                    @error('amil_name')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Amil (System User) <span class="text-gray-400 font-normal">(Optional)</span></label>
                                    <select name="amil_user_id"
                                        class="w-full border rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition @error('amil_user_id') border-red-500 ring-2 ring-red-200 @enderror">
                                        <option value="" {{ old('amil_user_id') == '' ? 'selected' : '' }}>— Select if registered —</option>
                                        @foreach($amilUsers as $user)
                                        <option value="{{ $user->id }}" {{ old('amil_user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('amil_user_id')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Akad Date <span class="text-red-500">*</span></label>
                                    <input type="date" name="akad_date"
                                        class="w-full border rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition @error('akad_date') border-red-500 ring-2 ring-red-200 @enderror"
                                        value="{{ old('akad_date', date('Y-m-d')) }}">
                                    @error('akad_date')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Notes <span class="text-gray-400 font-normal">(Optional)</span></label>
                                    <input type="text" name="akad_notes"
                                        class="w-full border rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition"
                                        placeholder="e.g. Akad after solat Jumaat" value="{{ old('akad_notes') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div x-show="isWaqf">
                        <div class="border-t-2 border-[#C5A059] pt-4">
                            <div class="flex items-center gap-2 mb-1">
                                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <h3 class="font-bold text-gray-800">Akad (Contract)</h3>
                            </div>
                            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 text-center">
                                <svg class="w-8 h-8 mx-auto text-amber-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-sm font-medium text-amber-800">Akad is not required for Waqf donations.</p>
                                <p class="text-xs text-amber-600 mt-1">You can submit this donation directly.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Navigation & Submit --}}
                <div class="mt-6 flex flex-col-reverse sm:flex-row items-center justify-between gap-3">
                    <button type="button" @click="if(confirm('This will fill in demo/test data. Are you sure?')) autoFill()"
                        class="w-full sm:w-auto bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 sm:py-2.5 px-4 rounded-lg shadow transition flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Auto Fill (Demo)
                    </button>
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <button type="button" x-show="step > 1" @click="prevStep()"
                            class="flex-1 sm:flex-none bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 sm:py-2.5 px-5 rounded-lg shadow transition flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            <span class="hidden sm:inline">Previous</span>
                        </button>
                        <button type="button" x-show="step < 3" @click="nextStep()"
                            class="flex-1 sm:flex-none bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 sm:py-2.5 px-5 rounded-lg shadow transition flex items-center justify-center gap-1.5">
                            <span class="hidden sm:inline">Next</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                        <button type="submit" x-show="step === 3"
                            class="flex-1 sm:flex-none bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 sm:py-2.5 px-6 rounded-lg shadow transition flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('islamic.donations.submit') }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Tab: Sadaqah --}}
    <div id="tab-sadaqah" class="tab-panel hidden">
        <div id="sadaqah-choices">
            <p class="text-sm text-gray-500 mb-5">Choose how you want to record this sadaqah contribution.</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <button type="button" onclick="showSadaqahForm()"
                    class="text-left p-5 border-2 border-dashed border-gray-200 rounded-xl hover:border-emerald-300 hover:bg-emerald-50 transition group">
                    <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center mb-3 group-hover:bg-emerald-200 transition">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-1">Single Entry</h3>
                    <p class="text-xs text-gray-500">Record one sadaqah donation from a known donor who wants a receipt.</p>
                </button>

                <a href="{{ route('donations.batch.form') }}"
                    class="block p-5 border-2 border-gray-200 rounded-xl hover:border-blue-300 hover:bg-blue-50 transition group">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mb-3 group-hover:bg-blue-200 transition">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-1">Batch Entry</h3>
                    <p class="text-xs text-gray-500">Enter multiple donors at once — each gets their own receipt number.</p>
                </a>

                <a href="{{ route('donations.bulk.form') }}"
                    class="block p-5 border-2 border-amber-200 rounded-xl hover:border-amber-400 hover:bg-amber-50 transition group">
                    <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center mb-3 group-hover:bg-amber-200 transition">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-1">Kotak Collection</h3>
                    <p class="text-xs text-gray-500">Anonymous box/tabung collection — one receipt for the total amount.</p>
                </a>
            </div>
        </div>

        <div id="sadaqah-form" class="hidden">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Single Sadaqah Entry
                </h3>
                <button type="button" onclick="showSadaqahChoices()" class="text-sm text-gray-500 hover:text-gray-700 transition font-medium">
                    &larr; Back
                </button>
            </div>
            <form action="/donations" method="POST" data-loading>
                @csrf
                <input type="hidden" name="category" value="sadaqah">

                @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <p class="font-semibold text-red-800">Please fix the following errors:</p>
                            <ul class="list-disc list-inside text-sm text-red-700 mt-1 space-y-0.5">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Amount (RM)</label>
                        <input type="number" step="0.01" name="amount"
                            class="w-full border rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition"
                            placeholder="0.00" value="{{ old('amount') }}" required>
                        @error('amount')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Source</label>
                        <select name="source" class="w-full border rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition">
                            <option value="cash" {{ old('source') == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="online" {{ old('source') == 'online' ? 'selected' : '' }}>Online</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Fund Purpose <span class="text-gray-400 font-normal">(e.g. General Fund, Kipas Gergasi)</span>
                        </label>
                        <input type="text" name="fund_purpose" id="sadaqahFundPurpose"
                            class="w-full border rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition"
                            placeholder="Type a purpose or click below" value="{{ old('fund_purpose') }}">
                        @error('fund_purpose')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <div class="flex flex-wrap gap-1.5 mt-2">
                            @foreach($suggestedPurposes as $purpose)
                            <button type="button" onclick="document.getElementById('sadaqahFundPurpose').value='{{ $purpose }}'"
                                class="px-2.5 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs rounded-full transition font-medium">
                                {{ $purpose }}
                            </button>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Donation Date</label>
                        <input type="date" name="donation_date"
                            class="w-full border rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition"
                            value="{{ old('donation_date', date('Y-m-d')) }}" required>
                        @error('donation_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Description <span class="text-gray-400 font-normal">(Optional)</span></label>
                        <textarea name="description" rows="2"
                            class="w-full border rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition resize-none"
                            placeholder="e.g. Friday donation box collection">{{ old('description') }}</textarea>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Reference <span class="text-gray-400 font-normal">(Optional — bank ref, receipt no.)</span></label>
                        <input type="text" name="reference"
                            class="w-full border rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition"
                            placeholder="e.g. BANK-12345, receipt #001" value="{{ old('reference') }}">
                    </div>
                </div>

                <div class="mt-6 flex gap-3">
                    <button type="submit"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 sm:py-2.5 px-6 rounded-lg shadow transition flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Record Sadaqah
                    </button>
                    <button type="button" onclick="showSadaqahChoices()"
                        class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 sm:py-2.5 px-4 rounded-lg transition">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- TABLE HISTORY -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="bg-gray-50 px-4 sm:px-6 py-4 border-b border-t-2 border-t-[#C5A059]">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-3">
            <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                {{ __('islamic.donations.history') }}
            </h2>
        </div>

        {{-- Type Filter Buttons --}}
        <div class="flex flex-wrap gap-2 mb-2">
            <a href="{{ request()->fullUrlWithQuery(['type_filter' => 'all']) }}"
                class="px-3 py-1.5 rounded-lg text-xs font-medium transition {{ $typeFilter === 'all' ? 'bg-emerald-600 text-white shadow' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                All Types
            </a>
            <a href="{{ request()->fullUrlWithQuery(['type_filter' => 'obligatory']) }}"
                class="px-3 py-1.5 rounded-lg text-xs font-medium transition {{ $typeFilter === 'obligatory' ? 'bg-emerald-600 text-white shadow' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Zakat (Obligatory)
            </a>
            <a href="{{ request()->fullUrlWithQuery(['type_filter' => 'voluntary']) }}"
                class="px-3 py-1.5 rounded-lg text-xs font-medium transition {{ $typeFilter === 'voluntary' ? 'bg-emerald-600 text-white shadow' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Sadaqah (Voluntary)
            </a>
            <a href="{{ request()->fullUrlWithQuery(['type_filter' => 'endowment']) }}"
                class="px-3 py-1.5 rounded-lg text-xs font-medium transition {{ $typeFilter === 'endowment' ? 'bg-emerald-600 text-white shadow' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Waqf (Endowment)
            </a>
        </div>
        <div class="flex flex-wrap gap-2 mb-3">
            <a href="{{ request()->fullUrlWithQuery(['status_filter' => 'all']) }}"
                class="px-3 py-1.5 rounded-lg text-xs font-medium transition {{ $statusFilter === 'all' ? 'bg-gray-700 text-white shadow' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                All Status
            </a>
            <a href="{{ request()->fullUrlWithQuery(['status_filter' => 'pending']) }}"
                class="px-3 py-1.5 rounded-lg text-xs font-medium transition inline-flex items-center gap-1 {{ $statusFilter === 'pending' ? 'bg-yellow-500 text-white shadow' : 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200' }}">
                Pending
                @if($donationPendingCount > 0 && $statusFilter !== 'pending')
                <span class="bg-red-500 text-white text-[10px] font-bold px-1.5 rounded-full">{{ $donationPendingCount }}</span>
                @endif
            </a>
            <a href="{{ request()->fullUrlWithQuery(['status_filter' => 'confirmed']) }}"
                class="px-3 py-1.5 rounded-lg text-xs font-medium transition {{ $statusFilter === 'confirmed' ? 'bg-green-600 text-white shadow' : 'bg-green-100 text-green-800 hover:bg-green-200' }}">
                Confirmed
            </a>
            <a href="{{ request()->fullUrlWithQuery(['status_filter' => 'disputed']) }}"
                class="px-3 py-1.5 rounded-lg text-xs font-medium transition {{ $statusFilter === 'disputed' ? 'bg-red-600 text-white shadow' : 'bg-red-100 text-red-800 hover:bg-red-200' }}">
                Disputed
            </a>
        </div>

        {{-- Shariah-compliant breakdown --}}
        <div class="flex flex-wrap gap-2">
            <span class="bg-[#C5A059] text-white text-xs px-2.5 py-1 rounded-full font-semibold">Zakat: RM {{ number_format($zakatTotal, 2) }}</span>
            <span class="bg-amber-100 text-amber-800 text-xs px-2.5 py-1 rounded-full font-semibold">Zakat Fitr: RM {{ number_format($zakatFitrTotal, 2) }}</span>
            <span class="bg-blue-100 text-blue-800 text-xs px-2.5 py-1 rounded-full font-semibold">Sadaqah: RM {{ number_format($sadaqahTotal, 2) }}</span>
            <span class="bg-purple-100 text-purple-800 text-xs px-2.5 py-1 rounded-full font-semibold">Waqf: RM {{ number_format($waqfTotal, 2) }}</span>
            <span class="bg-gray-100 text-gray-700 text-xs px-2.5 py-1 rounded-full">{{ $cashCount }} Cash | {{ $onlineCount }} Online</span>
        </div>

        {{-- Summary Stats Cards --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mt-4">
            <div class="bg-yellow-50 rounded-lg p-3 border border-yellow-200">
                <p class="text-[10px] font-semibold text-yellow-700 uppercase">Pending</p>
                <p class="text-lg font-bold text-yellow-800">{{ $donationPendingCount }} entries</p>
                <p class="text-xs text-yellow-600">RM {{ number_format($pendingTotal, 2) }}</p>
            </div>
            <div class="bg-green-50 rounded-lg p-3 border border-green-200">
                <p class="text-[10px] font-semibold text-green-700 uppercase">Confirmed</p>
                <p class="text-lg font-bold text-green-800">RM {{ number_format($confirmedTotal, 2) }}</p>
                <p class="text-xs text-green-600">Verified funds</p>
            </div>
            <div class="bg-red-50 rounded-lg p-3 border border-red-200">
                <p class="text-[10px] font-semibold text-red-700 uppercase">Disputed</p>
                <p class="text-lg font-bold text-red-800">RM {{ number_format($disputedTotal, 2) }}</p>
                <p class="text-xs text-red-600">Needs review</p>
            </div>
        </div>

        @if($fundPurposeBreakdown->count() > 0)
        <div class="mt-3 pt-3 border-t border-gray-100">
            <p class="text-[10px] font-semibold text-gray-500 uppercase mb-2">By Fund Purpose</p>
            <div class="flex flex-wrap gap-1.5">
                @foreach($fundPurposeBreakdown as $purpose => $total)
                <span class="inline-flex items-center gap-1 bg-gray-100 text-gray-700 text-[10px] px-2 py-0.5 rounded-full font-medium">
                    {{ $purpose }}: RM {{ number_format($total, 0) }}
                </span>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- Desktop Table View -->
    <div id="donations-table" class="hidden lg:block p-4 sm:p-6 overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="w-8 px-2 py-3"></th>
                    <th class="px-3 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'donation_date', 'direction' => $sort === 'donation_date' && $direction === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-700">
                            Date
                            @if($sort === 'donation_date')
                            <svg class="w-3 h-3 {{ $direction === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                            </svg>
                            @endif
                        </a>
                    </th>
                    <th class="px-3 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'category', 'direction' => $sort === 'category' && $direction === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-700">
                            Category
                            @if($sort === 'category')
                            <svg class="w-3 h-3 {{ $direction === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                            </svg>
                            @endif
                        </a>
                    </th>
                    <th class="px-3 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Donor</th>
                    <th class="px-3 py-3 text-right text-[11px] font-semibold text-gray-500 uppercase tracking-wider">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'amount', 'direction' => $sort === 'amount' && $direction === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center justify-end gap-1 hover:text-gray-700">
                            Amount
                            @if($sort === 'amount')
                            <svg class="w-3 h-3 {{ $direction === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                            </svg>
                            @endif
                        </a>
                    </th>
                    <th class="px-3 py-3 text-center text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-3 py-3 text-right text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($donations as $donation)
                {{-- Main Row --}}
                <tr class="hover:bg-gray-50 transition cursor-pointer donation-row" onclick="toggleDonationRow({{ $donation->id }})">
                    <td class="px-2 py-3 text-center">
                        <svg id="chevron-{{ $donation->id }}" class="w-4 h-4 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </td>
                    <td class="px-3 py-3">
                        <div class="flex items-center gap-2">
                            <div class="text-xs">
                                <p class="font-medium text-gray-900">{{ $donation->donation_date->format('d M') }}</p>
                                <p class="text-gray-400 text-[10px]">{{ $donation->donation_date->format('Y') }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-3 py-3">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            @if($donation->category === 'zakat')
                            <span class="inline-flex items-center bg-[#C5A059] text-white text-[10px] px-2 py-0.5 rounded-full font-semibold">Zakat</span>
                            @elseif($donation->category === 'zakat_fitr')
                            <span class="inline-flex items-center bg-[#C5A059] text-white text-[10px] px-2 py-0.5 rounded-full font-semibold">Zakat Fitr</span>
                            @elseif($donation->category === 'waqf')
                            <span class="inline-flex items-center bg-purple-100 text-purple-800 text-[10px] px-2 py-0.5 rounded-full font-semibold">Waqf</span>
                            @else
                            <span class="inline-flex items-center bg-emerald-100 text-emerald-800 text-[10px] px-2 py-0.5 rounded-full font-semibold">Sadaqah</span>
                            @endif
                            @if($donation->fund_purpose)
                            <span class="text-[10px] text-gray-500 hidden xl:inline">{{ Str::limit($donation->fund_purpose_label, 15) }}</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-3 py-3">
                        @if($donation->has_donor_info)
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 bg-amber-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <span class="text-[10px] text-amber-700 font-bold">{{ substr($donation->donor_name, 0, 1) }}</span>
                            </div>
                            <div class="min-w-0">
                                <p class="text-gray-900 font-medium text-xs truncate max-w-[140px]">{{ $donation->donor_display_name }}</p>
                            </div>
                        </div>
                        @else
                        <span class="text-gray-400 text-xs italic">Anonymous</span>
                        @endif
                    </td>
                    <td class="px-3 py-3 text-right">
                        <span class="text-sm font-bold text-gray-900">RM {{ number_format($donation->amount, 2) }}</span>
                    </td>
                    <td class="px-3 py-3 text-center">
                        @if($donation->status === 'pending')
                        <span class="inline-flex items-center gap-1 bg-yellow-100 text-yellow-800 text-[10px] px-2 py-0.5 rounded-full font-semibold">
                            <span class="w-1.5 h-1.5 bg-yellow-500 rounded-full"></span>
                            Pending
                        </span>
                        @elseif($donation->status === 'confirmed')
                        <span class="inline-flex items-center gap-1 bg-green-100 text-green-800 text-[10px] px-2 py-0.5 rounded-full font-semibold">
                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                            Confirmed
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1 bg-red-100 text-red-800 text-[10px] px-2 py-0.5 rounded-full font-semibold">
                            <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                            Disputed
                        </span>
                        @endif
                    </td>
                    <td class="px-3 py-3 text-right" onclick="event.stopPropagation()">
                        <div class="flex items-center justify-end gap-1">
                            @if(Auth::user()->role == 'treasurer' && $donation->can_verify)
                            <button type="button" onclick="showConfirmModal('Confirm Donation', 'Confirm this donation? This will mark it as verified.', 'Confirm', 'bg-green-500 hover:bg-green-600', '{{ route('donations.confirm', $donation->id) }}', 'PATCH')" class="bg-green-500 hover:bg-green-600 text-white text-[10px] px-2 py-1 rounded-md transition font-medium">
                                Confirm
                            </button>
                            <button type="button" onclick="showConfirmModal('Dispute Donation', 'Mark this donation as disputed?', 'Dispute', 'bg-red-500 hover:bg-red-600', '{{ route('donations.dispute', $donation->id) }}', 'PATCH')" class="bg-red-500 hover:bg-red-600 text-white text-[10px] px-2 py-1 rounded-md transition font-medium">
                                Dispute
                            </button>
                            @elseif($donation->status === 'confirmed')
                            <span class="text-green-600 text-[10px] font-medium mr-1">✓ Verified</span>
                            @if($donation->receipt_number)
                            <a href="{{ route('donations.receipt.print', $donation->id) }}" target="_blank" class="bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] px-2 py-1 rounded-md transition font-medium" title="Print Receipt">
                                Receipt
                            </a>
                            @endif
                            @if($donation->zakatAkad)
                            <a href="{{ route('donations.akad.print', $donation->id) }}" target="_blank" class="bg-[#C5A059] hover:bg-amber-700 text-white text-[10px] px-2 py-1 rounded-md transition font-medium" title="Print Akad">
                                Akad
                            </a>
                            @endif
                            @elseif($donation->status === 'disputed')
                            <span class="text-red-600 text-[10px] font-medium mr-1">✗ Flagged</span>
                            @if($donation->receipt_number)
                            <a href="{{ route('donations.receipt.print', $donation->id) }}" target="_blank" class="bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] px-2 py-1 rounded-md transition font-medium" title="Print Receipt">
                                Receipt
                            </a>
                            @endif
                            @if($donation->zakatAkad)
                            <a href="{{ route('donations.akad.print', $donation->id) }}" target="_blank" class="bg-[#C5A059] hover:bg-amber-700 text-white text-[10px] px-2 py-1 rounded-md transition font-medium" title="Print Akad">
                                Akad
                            </a>
                            @endif
                            @else
                            <span class="text-gray-400 text-[10px]">—</span>
                            @endif
                        </div>
                    </td>
                </tr>
                {{-- Expanded Detail Row --}}
                <tr id="detail-{{ $donation->id }}" class="hidden bg-gray-50/80">
                    <td colspan="7" class="px-6 py-3">
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 text-xs">
                            <div>
                                <p class="text-gray-400 font-medium uppercase text-[10px] mb-0.5">Receipt #</p>
                                <p class="font-mono text-gray-700">{{ $donation->receipt_number ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-400 font-medium uppercase text-[10px] mb-0.5">Fund Purpose</p>
                                <p class="text-gray-700">{{ $donation->fund_purpose_label ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-400 font-medium uppercase text-[10px] mb-0.5">Source</p>
                                <div class="flex items-center gap-1">
                                    @if($donation->source == 'cash')
                                    <svg class="w-3 h-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <span class="text-gray-700">Cash</span>
                                    @else
                                    <svg class="w-3 h-3 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0 3-4.03 3-9s-1.343-9-3-9m-9 9a9 9 0 019-9"></path>
                                    </svg>
                                    <span class="text-gray-700">Online</span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <p class="text-gray-400 font-medium uppercase text-[10px] mb-0.5">Recorded By</p>
                                <div class="flex items-center gap-1.5">
                                    @if($donation->user?->avatar_url)
                                    <img src="{{ $donation->user->avatar_url }}" alt="{{ $donation->user->name ?? '' }}" class="w-4 h-4 rounded-full object-cover">
                                    @else
                                    <div class="w-4 h-4 bg-emerald-100 rounded-full flex items-center justify-center">
                                        <span class="text-[8px] text-emerald-700 font-bold">{{ $donation->user?->initials ?? '?' }}</span>
                                    </div>
                                    @endif
                                    <span class="text-gray-700">{{ $donation->user->name ?? 'Deleted User' }}</span>
                                </div>
                            </div>
                            @if($donation->donor_ic)
                            <div>
                                <p class="text-gray-400 font-medium uppercase text-[10px] mb-0.5">Donor IC</p>
                                <p class="font-mono text-gray-700">{{ $donation->donor_display_ic ?? '—' }}</p>
                            </div>
                            @endif
                            @if($donation->reference)
                            <div>
                                <p class="text-gray-400 font-medium uppercase text-[10px] mb-0.5">Reference</p>
                                <p class="text-gray-700">{{ $donation->reference }}</p>
                            </div>
                            @endif
                            @if($donation->description)
                            <div class="col-span-2 lg:col-span-3">
                                <p class="text-gray-400 font-medium uppercase text-[10px] mb-0.5">Description</p>
                                <p class="text-gray-600 text-[11px]">{{ Str::limit($donation->description, 120) }}</p>
                            </div>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-12 text-gray-400">
                        <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-sm">{{ __('islamic.donations.empty') }}</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($donations->hasPages())
    <div id="donations-pagination" class="hidden lg:block px-4 sm:px-6 py-3 bg-gray-50 border-t border-gray-100">
        {{ $donations->appends(request()->except('page'))->links() }}
    </div>
    @endif

    <!-- Mobile Card View -->
    <div class="lg:hidden divide-y divide-gray-200">
        @forelse($donations as $donation)
        <div class="p-4 hover:bg-gray-50 transition">
            {{-- Top: Category + Amount --}}
            <div class="flex items-start justify-between mb-2">
                <div class="flex items-center gap-1.5 flex-wrap">
                    @if($donation->category === 'zakat')
                    <span class="inline-flex items-center bg-[#C5A059] text-white text-[10px] px-2 py-0.5 rounded-full font-semibold">Zakat</span>
                    @elseif($donation->category === 'zakat_fitr')
                    <span class="inline-flex items-center bg-[#C5A059] text-white text-[10px] px-2 py-0.5 rounded-full font-semibold">Zakat Fitr</span>
                    @elseif($donation->category === 'waqf')
                    <span class="inline-flex items-center bg-purple-100 text-purple-800 text-[10px] px-2 py-0.5 rounded-full font-semibold">Waqf</span>
                    @else
                    <span class="inline-flex items-center bg-emerald-100 text-emerald-800 text-[10px] px-2 py-0.5 rounded-full font-semibold">Sadaqah</span>
                    @endif
                    @if($donation->status === 'pending')
                    <span class="inline-flex items-center gap-0.5 bg-yellow-100 text-yellow-800 text-[10px] px-1.5 py-0.5 rounded-full font-semibold">Pending</span>
                    @elseif($donation->status === 'confirmed')
                    <span class="inline-flex items-center gap-0.5 bg-green-100 text-green-800 text-[10px] px-1.5 py-0.5 rounded-full font-semibold">Confirmed</span>
                    @else
                    <span class="inline-flex items-center gap-0.5 bg-red-100 text-red-800 text-[10px] px-1.5 py-0.5 rounded-full font-semibold">Disputed</span>
                    @endif
                </div>
                <p class="text-base font-bold text-gray-900">RM {{ number_format($donation->amount, 2) }}</p>
            </div>

            {{-- Middle: Donor + Date --}}
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2 min-w-0">
                    @if($donation->has_donor_info)
                    <div class="w-6 h-6 bg-amber-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-[10px] text-amber-700 font-bold">{{ substr($donation->donor_name, 0, 1) }}</span>
                    </div>
                    <span class="text-xs font-medium text-gray-800 truncate">{{ $donation->donor_display_name }}</span>
                    @else
                    <span class="text-xs text-gray-400 italic">Anonymous</span>
                    @endif
                </div>
                <div class="flex items-center gap-1 text-gray-400 flex-shrink-0">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span class="text-[10px]">{{ $donation->donation_date->format('d M Y') }}</span>
                </div>
            </div>

            {{-- Expand/Collapse Toggle --}}
            <button type="button" onclick="toggleMobileDonation(this)" class="w-full flex items-center justify-center gap-1 text-[10px] text-gray-400 hover:text-gray-600 py-1 transition">
                <span class="expand-text">More details</span>
                <svg class="w-3 h-3 expand-icon transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            {{-- Expanded Details --}}
            <div class="hidden mobile-details mt-2 pt-2 border-t border-gray-100 space-y-1.5">
                <div class="grid grid-cols-2 gap-2 text-[11px]">
                    <div>
                        <span class="text-gray-400">Receipt:</span>
                        <span class="font-mono text-gray-600 ml-1">{{ $donation->receipt_number ?? '—' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400">Purpose:</span>
                        <span class="text-gray-600 ml-1">{{ $donation->fund_purpose_label ?? '—' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400">Source:</span>
                        <span class="text-gray-600 ml-1">{{ ucfirst($donation->source) }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400">By:</span>
                        <span class="text-gray-600 ml-1">{{ $donation->user->name ?? 'Deleted User' }}</span>
                    </div>
                    @if($donation->reference)
                    <div class="col-span-2">
                        <span class="text-gray-400">Ref:</span>
                        <span class="text-gray-600 ml-1">{{ $donation->reference }}</span>
                    </div>
                    @endif
                    @if($donation->description)
                    <div class="col-span-2">
                        <span class="text-gray-400">Note:</span>
                        <span class="text-gray-600 ml-1">{{ Str::limit($donation->description, 80) }}</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-1.5 mt-2">
                @if(Auth::user()->role == 'treasurer' && $donation->can_verify)
                <button type="button" onclick="showConfirmModal('Confirm Donation', 'Confirm this donation?', 'Confirm', 'bg-green-500 hover:bg-green-600', '{{ route('donations.confirm', $donation->id) }}', 'PATCH')" class="bg-green-500 hover:bg-green-600 text-white text-[10px] px-2.5 py-1 rounded-md transition font-medium">
                    Confirm
                </button>
                <button type="button" onclick="showConfirmModal('Dispute Donation', 'Mark as disputed?', 'Dispute', 'bg-red-500 hover:bg-red-600', '{{ route('donations.dispute', $donation->id) }}', 'PATCH')" class="bg-red-500 hover:bg-red-600 text-white text-[10px] px-2.5 py-1 rounded-md transition font-medium">
                    Dispute
                </button>
                @elseif($donation->status === 'confirmed')
                @if($donation->receipt_number)
                <a href="{{ route('donations.receipt.print', $donation->id) }}" target="_blank" class="inline-flex items-center gap-1 bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] px-2.5 py-1 rounded-md transition font-medium">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Receipt
                </a>
                @endif
                @if($donation->zakatAkad)
                <a href="{{ route('donations.akad.print', $donation->id) }}" target="_blank" class="inline-flex items-center gap-1 bg-[#C5A059] hover:bg-amber-700 text-white text-[10px] px-2.5 py-1 rounded-md transition font-medium">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Akad
                </a>
                @endif
                @elseif($donation->status === 'disputed')
                @if($donation->receipt_number)
                <a href="{{ route('donations.receipt.print', $donation->id) }}" target="_blank" class="inline-flex items-center gap-1 bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] px-2.5 py-1 rounded-md transition font-medium">
                    Receipt
                </a>
                @endif
                @if($donation->zakatAkad)
                <a href="{{ route('donations.akad.print', $donation->id) }}" target="_blank" class="inline-flex items-center gap-1 bg-[#C5A059] hover:bg-amber-700 text-white text-[10px] px-2.5 py-1 rounded-md transition font-medium">
                    Akad
                </a>
                @endif
                @endif
            </div>
        </div>
        @empty
        <div class="p-8 text-center">
            <svg class="w-10 h-10 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-gray-500 text-sm">{{ __('islamic.donations.empty') }}</p>
        </div>
        @endforelse
    </div>

    @if($donations->hasPages())
    <div id="donations-pagination" class="px-4 sm:px-6 py-3 bg-gray-50 border-t border-gray-100 flex justify-center md:hidden">
        {{ $donations->appends(request()->except('page'))->links() }}
    </div>
    @endif
</div>

@endsection

@section('scripts')
<script>
    function switchDonationTab(tab) {
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('text-gray-500', 'border-transparent', 'hover:text-gray-700', 'hover:border-gray-300');
            btn.classList.add('text-emerald-600', 'border-emerald-600');
            if (btn.dataset.tab !== tab) {
                btn.classList.remove('text-emerald-600', 'border-emerald-600');
                btn.classList.add('text-gray-500', 'border-transparent', 'hover:text-gray-700', 'hover:border-gray-300');
            }
        });
        document.querySelectorAll('.tab-panel').forEach(panel => {
            panel.classList.add('hidden');
        });
        document.getElementById('tab-' + tab).classList.remove('hidden');
    }

    function toggleDonationRow(id) {
        const detailRow = document.getElementById('detail-' + id);
        const chevron = document.getElementById('chevron-' + id);
        if (detailRow && chevron) {
            detailRow.classList.toggle('hidden');
            chevron.classList.toggle('rotate-90');
        }
    }

    function toggleMobileDonation(btn) {
        const card = btn.closest('.p-4');
        const details = card.querySelector('.mobile-details');
        const icon = btn.querySelector('.expand-icon');
        const text = btn.querySelector('.expand-text');
        if (details) {
            details.classList.toggle('hidden');
            icon.classList.toggle('rotate-180');
            text.textContent = details.classList.contains('hidden') ? 'More details' : 'Less details';
        }
    }

    function showSadaqahForm() {
        document.getElementById('sadaqah-choices').classList.add('hidden');
        document.getElementById('sadaqah-form').classList.remove('hidden');
    }

    function showSadaqahChoices() {
        document.getElementById('sadaqah-form').classList.add('hidden');
        document.getElementById('sadaqah-choices').classList.remove('hidden');
    }

    function donationForm(initialStep, initialCategory) {
        return {
            step: initialStep,
            selectedCategory: initialCategory || '',
            nextStep() {
                if (this.step < 3) this.step++;
            },
            prevStep() {
                if (this.step > 1) this.step--;
            },
            get isWaqf() {
                return this.selectedCategory === 'waqf';
            },
            autoFill() {
                const amounts = [10.00, 20.00, 50.00, 100.00, 150.00, 200.00, 250.00, 500.00, 1000.00];
                const categories = ['zakat', 'zakat_fitr', 'waqf'];
                const donNames = ['Ali bin Ahmad', 'Siti binti Tan', 'Ahmad bin Lim', 'Fatimah binti Ismail', 'Mohamad bin Isa'];
                const donICs = ['810203-10-5678', '920415-01-2345', '750630-08-9012', '880112-14-3456', '710825-04-6789'];
                const descriptions = [
                    'Monthly donation for mosque maintenance',
                    'Zakat al-Fitr for Ramadan',
                    'Contribution for new prayer mats',
                    'Donation for Quran class program',
                    'Waqf for mosque expansion fund',
                ];

                const amt = this.$el.querySelector('input[name="amount"]');
                if (amt) amt.value = amounts[Math.floor(Math.random() * amounts.length)].toFixed(2);

                const cat = categories[Math.floor(Math.random() * categories.length)];
                this.selectedCategory = cat;

                const src = this.$el.querySelector('select[name="source"]');
                if (src) src.value = ['cash', 'online'][Math.floor(Math.random() * 2)];

                const d = new Date();
                d.setDate(d.getDate() - Math.floor(Math.random() * 30));
                const dateStr = d.toISOString().slice(0, 10);

                const dt = this.$el.querySelector('input[name="donation_date"]');
                if (dt) dt.value = dateStr;

                const desc = this.$el.querySelector('textarea[name="description"]');
                if (desc) desc.value = descriptions[Math.floor(Math.random() * descriptions.length)];

                const di = Math.floor(Math.random() * donNames.length);
                const dn = this.$el.querySelector('input[name="donor_name"]');
                if (dn) dn.value = donNames[di];
                const dic = this.$el.querySelector('input[name="donor_ic"]');
                if (dic) dic.value = donICs[di];
                const dph = this.$el.querySelector('input[name="donor_phone"]');
                if (dph) dph.value = '012' + Math.floor(100000 + Math.random() * 900000).toString();
                const dem = this.$el.querySelector('input[name="donor_email"]');
                if (dem) dem.value = donNames[di].toLowerCase().replace(/\s+/g, '.') + '@example.com';
                const dad = this.$el.querySelector('textarea[name="donor_address"]');
                if (dad) dad.value = 'No. ' + Math.floor(Math.random() * 100 + 1) + ', Jalan Contoh, Taman Damai, 50000 Kuala Lumpur';

                const amilNames = ['Ustaz Mohamad', 'Ustazah Fatimah', 'Imam Ahmad', 'Bilal Ismail'];
                const an = this.$el.querySelector('input[name="amil_name"]');
                if (an) an.value = amilNames[Math.floor(Math.random() * amilNames.length)];
                const ad = this.$el.querySelector('input[name="akad_date"]');
                if (ad) ad.value = dateStr;
                const an2 = this.$el.querySelector('input[name="akad_notes"]');
                if (an2) an2.value = 'Akad conducted after maghrib prayer';

                this.step = 3;
            }
        };
    }

    document.addEventListener('DOMContentLoaded', function() {
        @if(old('category') === 'sadaqah')
        switchDonationTab('sadaqah');
        showSadaqahForm();
        @endif
    });
</script>
@endsection