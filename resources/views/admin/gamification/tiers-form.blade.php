@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-amber-50 via-white to-orange-50 py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Back --}}
        <a href="{{ route('admin.gamification.tiers.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 transition font-medium mb-6">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back
        </a>

        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $tier ? 'Edit Tier' : 'Create Tier' }}</h1>
                <p class="text-gray-600 mt-1">{{ $tier ? 'Update tier milestone details' : 'Add a new volunteer tier level' }}</p>
            </div>
        </div>

        {{-- Form --}}
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <form action="{{ $tier ? route('admin.gamification.tiers.update', $tier) : route('admin.gamification.tiers.store') }}"
                  method="POST">
                @csrf
                @if($tier)
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Tier Name --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tier Name <span class="text-red-500">*</span></label>
                        <select name="tier"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('tier') border-red-500 @enderror">
                            @foreach(['bronze', 'silver', 'gold', 'platinum', 'diamond'] as $tierOpt)
                                <option value="{{ $tierOpt }}" {{ old('tier', $tier->tier ?? '') === $tierOpt ? 'selected' : '' }}>
                                    {{ ucfirst($tierOpt) }}
                                </option>
                            @endforeach
                        </select>
                        @error('tier') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Min Points --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Points <span class="text-red-500">*</span></label>
                        <input type="number" name="min_points"
                               value="{{ old('min_points', $tier->min_points ?? 0) }}"
                               min="0"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('min_points') border-red-500 @enderror"
                               placeholder="e.g., 500">
                        @error('min_points') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Name (EN) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Name (English) <span class="text-red-500">*</span></label>
                        <input type="text" name="name"
                               value="{{ old('name', $tier->name ?? '') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('name') border-red-500 @enderror"
                               placeholder="e.g., Gold Volunteer">
                        @error('name') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Name (MY) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Name (Malay) <span class="text-red-500">*</span></label>
                        <input type="text" name="name_my"
                               value="{{ old('name_my', $tier->name_my ?? '') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('name_my') border-red-500 @enderror"
                               placeholder="e.g., Sukarelawan Emas">
                        @error('name_my') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Benefits (EN) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Benefits (English) <span class="text-red-500">*</span></label>
                        <textarea name="benefits" rows="4"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('benefits') border-red-500 @enderror"
                                  placeholder="Comma-separated list, e.g., Certificate eligibility, VIP event access">{{ old('benefits', $tier->benefits ?? '') }}</textarea>
                        <p class="mt-1 text-xs text-gray-400">Separate multiple benefits with commas.</p>
                        @error('benefits') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Benefits (MY) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Benefits (Malay) <span class="text-red-500">*</span></label>
                        <textarea name="benefits_my" rows="4"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('benefits_my') border-red-500 @enderror"
                                  placeholder="Senarai dipisahkan dengan koma">{{ old('benefits_my', $tier->benefits_my ?? '') }}</textarea>
                        <p class="mt-1 text-xs text-gray-400">Pisahkan faedah berganda dengan koma.</p>
                        @error('benefits_my') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Icon SVG --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Icon SVG <span class="text-gray-400">(optional)</span></label>
                        <textarea name="icon_svg" rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('icon_svg') border-red-500 @enderror font-mono text-sm"
                                  placeholder="<svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='#FFD700'>...</svg>">{{ old('icon_svg', $tier->icon_svg ?? '') }}</textarea>
                        <p class="mt-1 text-xs text-gray-400">Paste inline SVG markup. Leave empty for default emoji.</p>
                        @error('icon_svg') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                        @if($tier && $tier->icon_svg)
                            <div class="mt-3 flex items-center gap-3">
                                <p class="text-sm text-gray-500">Current icon:</p>
                                <span class="w-12 h-12 flex items-center justify-center text-3xl">{!! $tier->icon_svg !!}</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Submit --}}
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mt-8 pt-6 border-t border-gray-200">
                    <button type="button" onclick="autoFillTier()"
                            class="w-full sm:w-auto px-4 py-2 bg-blue-400 hover:bg-blue-500 text-white font-bold rounded-lg transition flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Auto Fill
                    </button>
                    <div class="flex flex-col-reverse sm:flex-row gap-3">
                        <a href="{{ route('admin.gamification.tiers.index') }}"
                           class="w-full sm:w-auto text-center px-6 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">
                            Cancel
                        </a>
                        <button type="submit"
                                class="w-full sm:w-auto px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                            {{ $tier ? 'Update Tier' : 'Create Tier' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
function autoFillTier() {
    const tierNames = [
        { en: 'Bronze Volunteer', my: 'Sukarelawan Gangsa', min: 100, benefitsEn: 'Basic recognition, Certificate of participation', benefitsMy: 'Pengiktirafan asas, Sijil penyertaan' },
        { en: 'Silver Volunteer', my: 'Sukarelawan Perak', min: 500, benefitsEn: 'Priority event registration, Monthly newsletter', benefitsMy: 'Pendaftaran acara keutamaan, Buletin bulanan' },
        { en: 'Gold Volunteer', my: 'Sukarelawan Emas', min: 1000, benefitsEn: 'VIP event access, Exclusive volunteer t-shirt', benefitsMy: 'Akses acara VIP, Baju sukarelawan eksklusif' },
        { en: 'Platinum Volunteer', my: 'Sukarelawan Platinum', min: 2500, benefitsEn: 'Leadership opportunities, Free event tickets', benefitsMy: 'Peluang kepimpinan, Tiket acara percuma' },
        { en: 'Diamond Volunteer', my: 'Sukarelawan Berlian', min: 5000, benefitsEn: 'Special recognition ceremony, Exclusive community benefits', benefitsMy: 'Majlis pengiktirafan khas, Faedah komuniti eksklusif' }
    ];

    const randomIdx = Math.floor(Math.random() * tierNames.length);
    const selected = tierNames[randomIdx];
    const tiers = ['bronze', 'silver', 'gold', 'platinum', 'diamond'];

    document.querySelector('select[name="tier"]').value = tiers[randomIdx];
    document.querySelector('input[name="min_points"]').value = selected.min;
    document.querySelector('input[name="name"]').value = selected.en;
    document.querySelector('input[name="name_my"]').value = selected.my;
    document.querySelector('textarea[name="benefits"]').value = selected.benefitsEn;
    document.querySelector('textarea[name="benefits_my"]').value = selected.benefitsMy;
}
</script>
@endsection

