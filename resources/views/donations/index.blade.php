@extends('layouts.app')

@section('back', '/dashboard')

@section('title', __('islamic.donations.page_title'))

@section('content')

    <!-- FORM ADD DONATION -->
    <div class="bg-white rounded-lg shadow-md p-4 sm:p-6 mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 border-b pb-3 gap-2">
            <h2 class="text-lg sm:text-xl font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ __('islamic.donations.form_title') }}
            </h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('donations.fund-purposes') }}" class="text-xs sm:text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium px-2.5 sm:px-3 py-1.5 rounded-lg transition flex items-center gap-1 whitespace-nowrap">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Purposes
                </a>
                <a href="{{ route('donations.batch.form') }}" class="text-xs sm:text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium px-2.5 sm:px-3 py-1.5 rounded-lg transition flex items-center gap-1 whitespace-nowrap">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                    </svg>
                    Batch
                </a>
            </div>
        </div>

        <form action="/donations" method="POST" data-loading>
            @csrf

            <!-- STEP 1: Inline validation errors -->
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
                    <label class="block text-gray-700 text-sm font-bold mb-2">{{ __('islamic.donations.amount_label') }}</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">RM</span>
                        <input type="number" step="0.01" name="amount" 
                            class="w-full border rounded-lg pl-9 pr-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition @error('amount') border-red-500 ring-2 ring-red-200 @enderror"
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

                {{-- Shariah Type --}}
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Shariah Type</label>
                    <select name="category" id="categorySelect" 
                        class="w-full border rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition @error('category') border-red-500 ring-2 ring-red-200 @enderror"
                        onchange="onCategoryChange()" required>
                        <option value="" disabled {{ old('category') == '' ? 'selected' : '' }}>Select Shariah Type</option>
                        <optgroup label="Obligatory (Wajib)">
                            <option value="zakat" {{ old('category') == 'zakat' ? 'selected' : '' }}>Zakat</option>
                            <option value="zakat_fitr" {{ old('category') == 'zakat_fitr' ? 'selected' : '' }}>Zakat Fitr</option>
                        </optgroup>
                        <optgroup label="Voluntary (Sunnah)">
                            <option value="sadaqah" {{ old('category') == 'sadaqah' ? 'selected' : '' }}>Sadaqah</option>
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

            {{-- Fund Purpose --}}
            <div id="fundPurposeGroup" class="mt-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    Fund Purpose
                    <span id="fundPurposeRequired" class="text-red-500 hidden">*</span>
                    <span id="fundPurposeHint" class="text-gray-400 font-normal">(e.g. General Fund, Kipas Gergasi)</span>
                </label>
                <input type="text" name="fund_purpose" id="fundPurposeInput"
                    class="w-full border rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition @error('fund_purpose') border-red-500 ring-2 ring-red-200 @enderror"
                    placeholder="Type a purpose or click a suggestion below" value="{{ old('fund_purpose') }}">
                @error('fund_purpose')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <div class="flex flex-wrap gap-1.5 mt-2">
                    @foreach($suggestedPurposes as $purpose)
                        <button type="button" onclick="setFundPurpose('{{ $purpose }}')"
                            class="px-2.5 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs rounded-full transition font-medium">
                            {{ $purpose }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Static Fund Purpose display for Zakat & Waqf --}}
            <div id="fundPurposeStatic" class="mt-4 hidden">
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

            {{-- Donor Info Section — shown only for Zakat & Waqf --}}
            <div id="donorInfoSection" class="mt-6 hidden">
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

            {{-- Akad Details Section — shown only for Zakat --}}
            <div id="akadSection" class="mt-6 hidden">
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

            <div class="mt-6 flex flex-col-reverse sm:flex-row gap-3">
                <button type="submit"
                    class="w-full sm:w-auto bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 sm:py-2.5 px-6 rounded-lg shadow transition flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    {{ __('islamic.donations.submit') }}
                </button>
                <button type="button" onclick="autoFillDonation()"
                    class="w-full sm:w-auto bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 sm:py-2.5 px-4 rounded-lg shadow transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Auto Fill
                </button>
            </div>
        </form>
    </div>

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
                    @if($pendingCount > 0 && $statusFilter !== 'pending')
                        <span class="bg-red-500 text-white text-[10px] font-bold px-1.5 rounded-full">{{ $pendingCount }}</span>
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
                    <p class="text-lg font-bold text-yellow-800">{{ $pendingCount }} entries</p>
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
        <div id="donations-table" class="hidden md:block p-6 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'donation_date', 'direction' => $sort === 'donation_date' && $direction === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-700">
                                Date
                                @if($sort === 'donation_date')
                                    <svg class="w-4 h-4 {{ $direction === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                @endif
                            </a>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'category', 'direction' => $sort === 'category' && $direction === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-700">
                                Shariah
                                @if($sort === 'category')
                                    <svg class="w-4 h-4 {{ $direction === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                @endif
                            </a>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            Purpose
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            Status
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            Donor
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'source', 'direction' => $sort === 'source' && $direction === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-700">
                                Source
                                @if($sort === 'source')
                                    <svg class="w-4 h-4 {{ $direction === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                @endif
                            </a>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'amount', 'direction' => $sort === 'amount' && $direction === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-700">
                                Amount
                                @if($sort === 'amount')
                                    <svg class="w-4 h-4 {{ $direction === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                @endif
                            </a>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'direction' => $sort === 'created_at' && $direction === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-700">
                                Recorded By
                                @if($sort === 'created_at')
                                    <svg class="w-4 h-4 {{ $direction === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                @endif
                            </a>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($donations as $donation)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-sm text-gray-700 flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                {{ $donation->donation_date->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if($donation->category === 'zakat')
                                    <span class="inline-flex items-center gap-1 bg-[#C5A059] text-white text-xs px-2.5 py-1 rounded-full font-medium">Zakat</span>
                                @elseif($donation->category === 'zakat_fitr')
                                    <span class="inline-flex items-center gap-1 bg-[#C5A059] text-white text-xs px-2.5 py-1 rounded-full font-medium">Zakat Fitr</span>
                                @elseif($donation->category === 'waqf')
                                    <span class="inline-flex items-center gap-1 bg-purple-100 text-purple-800 text-xs px-2.5 py-1 rounded-full font-medium">Waqf</span>
                                @else
                                    <span class="inline-flex items-center gap-1 bg-emerald-100 text-emerald-800 text-xs px-2.5 py-1 rounded-full font-medium">Sadaqah</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if($donation->fund_purpose)
                                    <span class="inline-flex items-center gap-1 bg-gray-100 text-gray-700 text-xs px-2.5 py-1 rounded-full font-medium">{{ $donation->fund_purpose_label }}</span>
                                @else
                                    <span class="text-gray-400 text-xs italic">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if($donation->status === 'pending')
                                    <span class="inline-flex items-center gap-1 bg-yellow-100 text-yellow-800 text-xs px-2.5 py-1 rounded-full font-medium">
                                        <span class="w-1.5 h-1.5 bg-yellow-500 rounded-full"></span>
                                        Pending
                                    </span>
                                @elseif($donation->status === 'confirmed')
                                    <span class="inline-flex items-center gap-1 bg-green-100 text-green-800 text-xs px-2.5 py-1 rounded-full font-medium">
                                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                        Confirmed
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 bg-red-100 text-red-800 text-xs px-2.5 py-1 rounded-full font-medium">
                                        <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                                        Disputed
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if($donation->has_donor_info)
                                    <div class="flex items-center gap-1.5">
                                        <div class="w-6 h-6 bg-amber-100 rounded-full flex items-center justify-center flex-shrink-0">
                                            <span class="text-[10px] text-amber-700 font-bold">{{ substr($donation->donor_name, 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <p class="text-gray-800 font-medium text-xs">{{ $donation->donor_display_name }}</p>
                                            @if($donation->donor_display_ic)
                                                <p class="text-gray-400 text-[10px]">{{ $donation->donor_display_ic }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <span class="text-gray-400 text-xs italic">Anonymous</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if($donation->source == 'cash')
                                    <span class="inline-flex items-center gap-1 bg-blue-100 text-blue-800 text-xs px-2.5 py-1 rounded-full font-medium">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        Cash
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 bg-purple-100 text-purple-800 text-xs px-2.5 py-1 rounded-full font-medium">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0 3-4.03 3-9s-1.343-9-3-9m-9 9a9 9 0 019-9"></path></svg>
                                        Online
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm font-bold text-emerald-600">RM {{ number_format($donation->amount, 2) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                <div class="flex items-center gap-1">
                                    @if($donation->user->avatar_url)
                                        <img src="{{ $donation->user->avatar_url }}" alt="{{ $donation->user->name }}" class="w-5 h-5 rounded-full object-cover">
                                    @else
                                        <div class="w-5 h-5 bg-emerald-100 rounded-full flex items-center justify-center">
                                            <span class="text-[10px] text-emerald-700 font-bold">{{ $donation->user->initials }}</span>
                                        </div>
                                    @endif
                                    {{ $donation->user->name }}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if($donation->can_verify)
                                    <div class="flex items-center gap-1">
                                        <button type="button" onclick="showConfirmModal('Confirm Donation', 'Confirm this donation? This will mark it as verified.', 'Confirm', 'bg-green-500 hover:bg-green-600', '{{ route('donations.confirm', $donation->id) }}', 'PATCH')" class="bg-green-500 hover:bg-green-600 text-white text-xs px-2.5 py-1.5 rounded-lg transition font-medium">
                                            Confirm
                                        </button>
                                        <button type="button" onclick="showConfirmModal('Dispute Donation', 'Mark this donation as disputed? This should only be done if there is a mismatch.', 'Dispute', 'bg-red-500 hover:bg-red-600', '{{ route('donations.dispute', $donation->id) }}', 'PATCH')" class="bg-red-500 hover:bg-red-600 text-white text-xs px-2.5 py-1.5 rounded-lg transition font-medium">
                                            Dispute
                                        </button>
                                    </div>
                                @elseif($donation->status === 'confirmed')
                                    <div class="flex items-center gap-1">
                                        <span class="text-green-600 text-xs font-medium">✓ Verified</span>
                                        @if($donation->zakatAkad)
                                            <a href="{{ route('donations.akad.print', $donation->id) }}" target="_blank" class="text-[#C5A059] hover:text-amber-700 transition" title="Print Akad Slip">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                                </svg>
                                            </a>
                                        @endif
                                    </div>
                                @elseif($donation->status === 'disputed')
                                    <div class="flex items-center gap-1">
                                        <span class="text-red-600 text-xs font-medium">✗ Flagged</span>
                                        @if($donation->zakatAkad)
                                            <a href="{{ route('donations.akad.print', $donation->id) }}" target="_blank" class="text-[#C5A059] hover:text-amber-700 transition" title="Print Akad Slip">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                                </svg>
                                            </a>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400 text-xs">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-8 text-gray-500">{{ __('islamic.donations.empty') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($donations->hasPages())
            <div id="donations-pagination" class="hidden md:block px-4 sm:px-6 py-3 bg-gray-50 border-t border-gray-100">
                {{ $donations->appends(request()->except('page'))->links() }}
            </div>
        @endif

        <!-- Mobile Card View -->
        <div class="md:hidden divide-y divide-gray-200">
            @forelse($donations as $donation)
                <div class="p-4 hover:bg-gray-50 transition">
                    <div class="flex items-start justify-between mb-2">
                        <div>
                            <div class="flex items-center gap-1.5 flex-wrap mb-1">
                                @if($donation->category === 'zakat')
                                    <span class="inline-flex items-center gap-0.5 bg-[#C5A059] text-white text-[10px] px-1.5 py-0.5 rounded-full font-medium">Zakat</span>
                                @elseif($donation->category === 'zakat_fitr')
                                    <span class="inline-flex items-center gap-0.5 bg-[#C5A059] text-white text-[10px] px-1.5 py-0.5 rounded-full font-medium">Zakat Fitr</span>
                                @elseif($donation->category === 'waqf')
                                    <span class="inline-flex items-center gap-0.5 bg-purple-100 text-purple-800 text-[10px] px-1.5 py-0.5 rounded-full font-medium">Waqf</span>
                                @else
                                    <span class="inline-flex items-center gap-0.5 bg-emerald-100 text-emerald-800 text-[10px] px-1.5 py-0.5 rounded-full font-medium">Sadaqah</span>
                                @endif
                                @if($donation->fund_purpose)
                                    <span class="inline-flex items-center gap-0.5 bg-gray-100 text-gray-600 text-[10px] px-1.5 py-0.5 rounded-full font-medium">{{ $donation->fund_purpose_label }}</span>
                                @endif
                                @if($donation->status === 'pending')
                                    <span class="inline-flex items-center gap-0.5 bg-yellow-100 text-yellow-800 text-[10px] px-1.5 py-0.5 rounded-full font-medium">Pending</span>
                                @elseif($donation->status === 'confirmed')
                                    <span class="inline-flex items-center gap-0.5 bg-green-100 text-green-800 text-[10px] px-1.5 py-0.5 rounded-full font-medium">Confirmed</span>
                                @else
                                    <span class="inline-flex items-center gap-0.5 bg-red-100 text-red-800 text-[10px] px-1.5 py-0.5 rounded-full font-medium">Disputed</span>
                                @endif
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span class="text-xs text-gray-500">{{ $donation->donation_date->format('d M Y') }}</span>
                            </div>
                            @if($donation->has_donor_info)
                                <p class="text-xs text-amber-700 mt-1 font-medium">{{ $donation->donor_display_name }}</p>
                            @endif
                        </div>
                        <p class="text-lg font-bold text-emerald-600">RM {{ number_format($donation->amount, 2) }}</p>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            @if($donation->source == 'cash')
                                <span class="inline-flex items-center gap-1 bg-blue-100 text-blue-800 text-xs px-2 py-0.5 rounded-full font-medium">Cash</span>
                            @else
                                <span class="inline-flex items-center gap-1 bg-purple-100 text-purple-800 text-xs px-2 py-0.5 rounded-full font-medium">Online</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-1.5 text-xs text-gray-500">
                            @if($donation->user->avatar_url)
                                <img src="{{ $donation->user->avatar_url }}" alt="{{ $donation->user->name }}" class="w-5 h-5 rounded-full object-cover">
                            @else
                                <div class="w-5 h-5 bg-emerald-100 rounded-full flex items-center justify-center">
                                    <span class="text-[10px] text-emerald-700 font-bold">{{ $donation->user->initials }}</span>
                                </div>
                            @endif
                            <span>{{ $donation->user->name }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-gray-500">{{ __('islamic.donations.empty') }}</p>
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
function onCategoryChange() {
    const select = document.getElementById('categorySelect');
    const donorSection = document.getElementById('donorInfoSection');
    const akadSection = document.getElementById('akadSection');
    const fundPurposeGroup = document.getElementById('fundPurposeGroup');
    const fundPurposeStatic = document.getElementById('fundPurposeStatic');
    const fundPurposeRequired = document.getElementById('fundPurposeRequired');
    const fundPurposeHint = document.getElementById('fundPurposeHint');

    const requiresDonor = ['zakat', 'zakat_fitr', 'waqf'];
    if (requiresDonor.includes(select.value)) {
        donorSection.classList.remove('hidden');
    } else {
        donorSection.classList.add('hidden');
    }

    if (select.value === 'zakat' || select.value === 'zakat_fitr') {
        akadSection.classList.remove('hidden');
    } else {
        akadSection.classList.add('hidden');
    }

    if (select.value === 'sadaqah') {
        fundPurposeGroup.classList.remove('hidden');
        fundPurposeStatic.classList.add('hidden');
        fundPurposeRequired.classList.remove('hidden');
        fundPurposeHint.textContent = '(e.g. General Fund, Kipas Gergasi)';
        document.getElementById('fundPurposeInput').disabled = false;
    } else {
        fundPurposeGroup.classList.add('hidden');
        fundPurposeStatic.classList.remove('hidden');
        fundPurposeRequired.classList.add('hidden');
        fundPurposeHint.textContent = '(Auto-set to General Fund)';
        document.getElementById('fundPurposeInput').disabled = true;
    }
}

function setFundPurpose(purpose) {
    document.getElementById('fundPurposeInput').value = purpose;
}

function autoFillDonation() {
    const amounts = [10.00, 20.00, 50.00, 100.00, 150.00, 200.00, 250.00, 500.00, 1000.00];
    const categories = ['zakat', 'zakat_fitr', 'sadaqah', 'waqf'];
    const purposes = ['General Fund', 'Kipas Gergasi', 'Construction', 'Operations', 'Education'];
    const sources = ['cash', 'online'];
    const donorNames = ['Ali bin Ahmad', 'Siti binti Tan', 'Ahmad bin Lim', 'Fatimah binti Ismail', 'Mohamad bin Isa'];
    const donorICs = ['810203-10-5678', '920415-01-2345', '750630-08-9012', '880112-14-3456', '710825-04-6789'];
    const descriptions = [
        'Monthly donation for mosque maintenance',
        'Zakat al-Fitr for Ramadan',
        'Contribution for new prayer mats',
        'Sadaqah for the poor and needy',
        'Donation for Quran class program',
        'Waqf for mosque expansion fund',
    ];

    document.querySelector('input[name="amount"]').value = amounts[Math.floor(Math.random() * amounts.length)].toFixed(2);

    const select = document.getElementById('categorySelect');
    const randomCategory = categories[Math.floor(Math.random() * categories.length)];
    for (let i = 0; i < select.options.length; i++) {
        if (select.options[i].value === randomCategory) {
            select.selectedIndex = i;
            break;
        }
    }
    onCategoryChange();

    document.getElementById('fundPurposeInput').value = purposes[Math.floor(Math.random() * purposes.length)];

    const sourceSelect = document.querySelector('select[name="source"]');
    sourceSelect.value = sources[Math.floor(Math.random() * sources.length)];

    const today = new Date();
    const randomDays = Math.floor(Math.random() * 30);
    const pastDate = new Date(today.getTime() - (randomDays * 24 * 60 * 60 * 1000));
    document.querySelector('input[name="donation_date"]').value = pastDate.toISOString().slice(0, 10);

    document.querySelector('textarea[name="description"]').value = descriptions[Math.floor(Math.random() * descriptions.length)];

    const donorIdx = Math.floor(Math.random() * donorNames.length);
    document.querySelector('input[name="donor_name"]').value = donorNames[donorIdx];
    document.querySelector('input[name="donor_ic"]').value = donorICs[donorIdx];
    document.querySelector('input[name="donor_phone"]').value = '012' + Math.floor(100000 + Math.random() * 900000).toString();
    document.querySelector('input[name="donor_email"]').value = donorNames[donorIdx].toLowerCase().replace(/\s+/g, '.') + '@example.com';
    document.querySelector('textarea[name="donor_address"]').value = 'No. ' + Math.floor(Math.random() * 100 + 1) + ', Jalan Contoh, Taman Damai, 50000 Kuala Lumpur';

    // Set akad fields
    const amilNames = ['Ustaz Mohamad', 'Ustazah Fatimah', 'Imam Ahmad', 'Bilal Ismail'];
    document.querySelector('input[name="amil_name"]').value = amilNames[Math.floor(Math.random() * amilNames.length)];
    document.querySelector('input[name="akad_date"]').value = pastDate.toISOString().slice(0, 10);
    document.querySelector('input[name="akad_notes"]').value = 'Akad conducted after maghrib prayer';
}

document.addEventListener('DOMContentLoaded', function() {
    onCategoryChange();

    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('page')) {
        const table = document.getElementById('donations-table');
        if (table) {
            setTimeout(() => {
                table.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 100);
        }
    }
});
</script>
@endsection

