@extends('layouts.auth')

@section('title', 'Register')

@section('heading', 'Join Community')

@section('subheading', 'Create an account to start volunteering and earn rewards.')

@push('head')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.9/dist/cdn.min.js"></script>
@endpush

@push('styles')
[x-cloak] { display: none !important; }
@endpush

@section('content')
    @php
        $initialStep = 1;
        if ($errors->has('referral_code') || $errors->has('special_code')) {
            $initialStep = 3;
        } elseif ($errors->has('password') || $errors->has('password_confirmation')) {
            $initialStep = 2;
        }
    @endphp

    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg animate-slideIn">
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

    <form method="POST" action="/register" data-loading>
        @csrf

        <div x-data="registerForm({{ $initialStep }})">
            <!-- Step Indicators -->
            <div class="flex items-center mb-6">
                <template x-for="(s, i) in ['Personal Info', 'Password', 'Optional Codes']" :key="i">
                    <div class="flex items-center flex-1">
                        <div class="flex items-center gap-2">
                            <div :class="step >= i + 1 ? 'bg-emerald-600 text-white' : 'bg-gray-200 text-gray-500'"
                                class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-colors duration-300"
                                x-text="i + 1">
                            </div>
                            <span :class="step === i + 1 ? 'text-emerald-700 font-semibold' : 'text-gray-400'"
                                class="text-xs hidden sm:inline transition-colors duration-300"
                                x-text="s">
                            </span>
                        </div>
                        <div x-show="i < 2" :class="step > i + 1 ? 'bg-emerald-400' : 'bg-gray-200'"
                            class="flex-1 h-0.5 mx-2 transition-colors duration-300">
                        </div>
                    </div>
                </template>
            </div>

            <!-- Validation Error Banner -->
            <div x-show="hasErrors()"
                class="mb-4 p-3 bg-red-50 border-l-4 border-red-500 rounded-lg animate-slideIn">
                <template x-for="(msg, name) in allErrors()" :key="name">
                    <div class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-red-700 text-sm" x-text="msg"></p>
                    </div>
                </template>
            </div>

            <!-- Step 1: Personal Info -->
            <div x-ref="step1" x-show="step === 1" x-cloak>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Full Name</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="w-full border rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none transition @error('name') border-red-500 ring-2 ring-red-200 @enderror"
                        placeholder="Enter your full name" required>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                    <p x-show="fieldErrors['name'] || formatErrors['name']" x-text="fieldErrors['name'] || formatErrors['name']" class="text-red-500 text-xs mt-1 flex items-center gap-1">
                    </p>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="w-full border rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none transition @error('email') border-red-500 ring-2 ring-red-200 @enderror"
                        placeholder="you@example.com" required>
                    @error('email')
                        <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                    <p x-show="fieldErrors['email'] || formatErrors['email']" x-text="fieldErrors['email'] || formatErrors['email']" class="text-red-500 text-xs mt-1 flex items-center gap-1">
                    </p>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Phone Number</label>
                    <input type="tel" name="phone" value="{{ old('phone') }}"
                        class="w-full border rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none transition @error('phone') border-red-500 ring-2 ring-red-200 @enderror"
                        placeholder="0123456789" required>
                    @error('phone')
                        <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                    <p x-show="fieldErrors['phone'] || formatErrors['phone']" x-text="fieldErrors['phone'] || formatErrors['phone']" class="text-red-500 text-xs mt-1 flex items-center gap-1">
                    </p>
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="button" onclick="if(confirm('This will fill in demo/test data. Are you sure?')) autoFillRegister()"
                        class="flex-1 bg-blue-400 hover:bg-blue-500 text-white font-semibold py-2.5 px-4 rounded-lg transition flex items-center justify-center gap-2 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Auto Fill (Demo)
                    </button>
                    <button type="button" @click="goNext(2)"
                        class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2.5 px-4 rounded-lg transition flex items-center justify-center gap-2 text-sm">
                        Next Step
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Step 2: Password -->
            <div x-ref="step2" x-show="step === 2" x-cloak>
                <div class="mb-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                        <div class="relative">
                            <input :type="showPassword ? 'text' : 'password'" name="password"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 pr-10 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition @error('password') border-red-500 ring-2 ring-red-200 @enderror"
                                placeholder="Min 8 chars" required>
                            <button type="button" @click="showPassword = !showPassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none">
                                <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"></path></svg>
                                {{ $message }}
                            </p>
                        @enderror
                        <p x-show="fieldErrors['password'] || formatErrors['password']" x-text="fieldErrors['password'] || formatErrors['password']" class="text-red-500 text-xs mt-1 flex items-center gap-1">
                        </p>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Confirm Password</label>
                        <div class="relative">
                            <input :type="showConfirmPassword ? 'text' : 'password'" name="password_confirmation"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 pr-10 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                                placeholder="Re-enter password" required>
                            <button type="button" @click="showConfirmPassword = !showConfirmPassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none">
                                <svg x-show="!showConfirmPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg x-show="showConfirmPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                        <p x-show="fieldErrors['password_confirmation'] || formatErrors['password_confirmation']" x-text="fieldErrors['password_confirmation'] || formatErrors['password_confirmation']" class="text-red-500 text-xs mt-1 flex items-center gap-1">
                        </p>
                    </div>
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="button" @click="goPrev(1)"
                        class="flex-1 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-semibold py-2.5 px-4 rounded-lg transition flex items-center justify-center gap-2 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Previous
                    </button>
                    <button type="button" @click="goNext(3)"
                        class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2.5 px-4 rounded-lg transition flex items-center justify-center gap-2 text-sm">
                        Next Step
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Step 3: Optional Codes -->
            <div x-ref="step3" x-show="step === 3" x-cloak>
                <div class="mb-4">
                    <label class="block text-gray-600 text-sm font-medium mb-2">Referral Code <span class="text-gray-400 font-normal">(Optional)</span></label>
                    <input type="text" name="referral_code" value="{{ old('referral_code') }}"
                        class="w-full border border-dashed rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none transition @error('referral_code') border-red-500 ring-2 ring-red-200 @enderror"
                        placeholder="Enter code from a friend (e.g., A3F9B2C1)">
                    @error('referral_code')
                        <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                    <p x-show="fieldErrors['referral_code'] || formatErrors['referral_code']" x-text="fieldErrors['referral_code'] || formatErrors['referral_code']" class="text-red-500 text-xs mt-1 flex items-center gap-1">
                    </p>
                    <p class="text-xs text-gray-400 mt-1">Got a referral code from a friend? Enter it here!</p>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-600 text-sm font-medium mb-2">Staff Special Code <span class="text-gray-400 font-normal">(Optional)</span></label>
                    <input type="text" name="special_code" value="{{ old('special_code') }}"
                        class="w-full border border-dashed rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none transition @error('special_code') border-red-500 ring-2 ring-red-200 @enderror"
                        placeholder="Enter code if you are Staff">
                    @error('special_code')
                        <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                    <p x-show="fieldErrors['special_code'] || formatErrors['special_code']" x-text="fieldErrors['special_code'] || formatErrors['special_code']" class="text-red-500 text-xs mt-1 flex items-center gap-1">
                    </p>
                    <p class="text-xs text-gray-400 mt-1">Contact committee for staff registration codes.</p>
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="button" @click="goPrev(2)"
                        class="flex-1 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-semibold py-2.5 px-4 rounded-lg transition flex items-center justify-center gap-2 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Previous
                    </button>
                    <button type="button" @click="submitForm()"
                        class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2.5 px-4 rounded-lg transition flex items-center justify-center gap-2 text-sm">
                        <span>Register Now</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </form>

    <div class="mt-4 text-center text-sm">
        <span class="text-gray-600">Already have an account?</span>
        <a href="/login" class="text-emerald-600 font-bold hover:underline">Login</a>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('registerForm', (initialStep) => ({
            step: initialStep,
            fieldErrors: {},
            formatErrors: {},
            showPassword: false,
            showConfirmPassword: false,

            validateStep(s) {
                this.fieldErrors = {};
                this.formatErrors = {};
                const el = s === 1 ? this.$refs.step1 : (s === 2 ? this.$refs.step2 : this.$refs.step3);
                const reqs = el.querySelectorAll('[required]');
                let valid = true;

                reqs.forEach(f => {
                    const val = f.value ? f.value.trim() : '';

                    if (!val) {
                        this.fieldErrors[f.name] = 'This field is required';
                        f.classList.add('border-red-500', 'ring-2', 'ring-red-200');
                        valid = false;
                        return;
                    }

                    f.classList.remove('border-red-500', 'ring-2', 'ring-red-200');

                    if (f.type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
                        this.formatErrors[f.name] = 'Please enter a valid email address';
                        f.classList.add('border-red-500', 'ring-2', 'ring-red-200');
                        valid = false;
                    }

                    if (f.name === 'phone') {
                        const digits = val.replace(/\D/g, '');
                        if (!/^01[0-9]{8,9}$/.test(digits)) {
                            this.formatErrors[f.name] = 'Phone must be a valid Malaysian number (e.g. 012-3456789)';
                            f.classList.add('border-red-500', 'ring-2', 'ring-red-200');
                            valid = false;
                        }
                    }

                    if (f.name === 'password' && val.length < 8) {
                        this.formatErrors[f.name] = 'Password must be at least 8 characters';
                        f.classList.add('border-red-500', 'ring-2', 'ring-red-200');
                        valid = false;
                    }

                    if (f.name === 'password_confirmation') {
                        const pw = el.querySelector('[name=password]');
                        if (pw && val !== pw.value.trim()) {
                            this.formatErrors[f.name] = 'Passwords do not match';
                            f.classList.add('border-red-500', 'ring-2', 'ring-red-200');
                            valid = false;
                        }
                    }
                });

                return valid;
            },

            goNext(s) {
                if (this.validateStep(this.step)) {
                    this.fieldErrors = {};
                    this.formatErrors = {};
                    this.step = s;
                }
            },

            goPrev(s) {
                this.fieldErrors = {};
                this.formatErrors = {};
                this.step = s;
            },

            hasErrors() {
                return Object.keys(this.fieldErrors).length > 0 || Object.keys(this.formatErrors).length > 0;
            },

            allErrors() {
                return Object.assign({}, this.fieldErrors, this.formatErrors);
            },

            submitForm() {
                let allValid = true;
                for (let i = 1; i <= 3; i++) {
                    if (!this.validateStep(i)) allValid = false;
                }
                if (allValid) {
                    this.$el.closest('form').submit();
                }
            }
        }));
    });

    function autoFillRegister() {
        const firstNames = ['Ahmad', 'Muhammad', 'Ali', 'Omar', 'Hassan', 'Ibrahim', 'Yusuf', 'Adam', 'Zayn', 'Farid', 'Aisha', 'Fatimah', 'Maryam', 'Khadijah', 'Nur', 'Siti', 'Amira', 'Zainab'];
        const lastNames = ['Abdullah', 'Rahman', 'Ismail', 'Hussein', 'Kamal', 'Razak', 'Harun', 'Sulaiman', 'Yahya', 'Malik', 'Aziz', 'Hassan', 'Ibrahim'];
        const phones = ['012', '013', '014', '016', '017', '018', '019'];

        const firstName = firstNames[Math.floor(Math.random() * firstNames.length)];
        const lastName = lastNames[Math.floor(Math.random() * lastNames.length)];
        const fullName = firstName + ' ' + lastName;
        const emailBase = firstName.toLowerCase() + '.' + Math.floor(Math.random() * 999);
        const phonePrefix = phones[Math.floor(Math.random() * phones.length)];
        const phoneSuffix = Math.floor(Math.random() * 9000000 + 1000000);

        document.querySelector('input[name="name"]').value = fullName;
        document.querySelector('input[name="email"]').value = emailBase + '@example.com';
        document.querySelector('input[name="phone"]').value = phonePrefix + phoneSuffix;
        document.querySelector('input[name="password"]').value = 'Password123!';
        document.querySelector('input[name="password_confirmation"]').value = 'Password123!';
    }
</script>
@endpush
