@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-amber-50 via-white to-orange-50 py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Back --}}
        <a href="{{ route('admin.gamification.badges.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 transition font-medium mb-6">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back
        </a>

        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $badge ? 'Edit Badge' : 'Create Badge' }}</h1>
                <p class="text-gray-600 mt-1">{{ $badge ? 'Update badge details' : 'Add a new achievement badge' }}</p>
            </div>
        </div>

        {{-- Form --}}
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <form action="{{ $badge ? route('admin.gamification.badges.update', $badge) : route('admin.gamification.badges.store') }}"
                  method="POST"
                  enctype="multipart/form-data">
                @csrf
                @if($badge)
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Code --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Code <span class="text-red-500">*</span></label>
                        <input type="text" name="code"
                               value="{{ old('code', $badge->code ?? '') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('code') border-red-500 @enderror"
                               placeholder="e.g., first_step">
                        @error('code') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Tier --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tier <span class="text-red-500">*</span></label>
                        <select name="tier"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('tier') border-red-500 @enderror">
                            @foreach(['bronze', 'silver', 'gold', 'platinum', 'diamond'] as $tier)
                                <option value="{{ $tier }}" {{ old('tier', $badge->tier ?? '') === $tier ? 'selected' : '' }}>
                                    {{ ucfirst($tier) }}
                                </option>
                            @endforeach
                        </select>
                        @error('tier') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Name (EN) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Name (English) <span class="text-red-500">*</span></label>
                        <input type="text" name="name"
                               value="{{ old('name', $badge->name ?? '') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('name') border-red-500 @enderror"
                               placeholder="e.g., First Step">
                        @error('name') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Name (MY) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Name (Malay) <span class="text-red-500">*</span></label>
                        <input type="text" name="name_my"
                               value="{{ old('name_my', $badge->name_my ?? '') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('name_my') border-red-500 @enderror"
                               placeholder="e.g., Langkah Pertama">
                        @error('name_my') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Description (EN) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description (English) <span class="text-red-500">*</span></label>
                        <textarea name="description" rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('description') border-red-500 @enderror"
                                  placeholder="Describe what this badge is for">{{ old('description', $badge->description ?? '') }}</textarea>
                        @error('description') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Description (MY) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description (Malay) <span class="text-red-500">*</span></label>
                        <textarea name="description_my" rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('description_my') border-red-500 @enderror"
                                  placeholder="Terangkan tujuan lencana ini">{{ old('description_my', $badge->description_my ?? '') }}</textarea>
                        @error('description_my') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Points Awarded --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Points Awarded <span class="text-red-500">*</span></label>
                        <input type="number" name="points_awarded"
                               value="{{ old('points_awarded', $badge->points_awarded ?? 0) }}"
                               min="0"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('points_awarded') border-red-500 @enderror"
                               placeholder="e.g., 100">
                        @error('points_awarded') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Icon Upload --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Icon Image <span class="text-gray-400">(optional)</span></label>
                        <div class="relative">
                            <div id="icon-drop-zone" class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center cursor-pointer hover:border-emerald-500 hover:bg-emerald-50/50 transition-all duration-200 group">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-12 h-12 mb-3 rounded-full bg-emerald-100 flex items-center justify-center group-hover:bg-emerald-200 transition-colors">
                                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium text-gray-700 group-hover:text-emerald-700">Click to upload or drag & drop</p>
                                    <p class="text-xs text-gray-400 mt-1">JPG, PNG, GIF, WebP (Max 2MB)</p>
                                </div>
                                <input type="file" name="icon" id="icon-input" accept="image/jpeg,image/png,image/gif,image/webp"
                                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            </div>
                        </div>
                        @error('icon') <p class="mt-2 text-sm text-red-500">{{ $message }}</p> @enderror

                        {{-- Preview Area --}}
                        <div id="icon-preview-container" class="hidden mt-4 p-4 bg-gray-50 rounded-xl border border-gray-200">
                            <p class="text-sm font-medium text-gray-700 mb-3">Preview</p>
                            <div class="flex items-center gap-4">
                                <div class="relative">
                                    <img id="icon-preview" src="" alt="Preview" class="w-16 h-16 object-contain rounded-lg border border-gray-300 shadow-sm">
                                    <button type="button" id="icon-remove" class="absolute -top-2 -right-2 w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 shadow-md">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                                <div class="flex-1">
                                    <p id="icon-filename" class="text-sm font-medium text-gray-800 truncate"></p>
                                    <p id="icon-filesize" class="text-xs text-gray-500"></p>
                                </div>
                            </div>
                        </div>

                        @if($badge && ($badge->icon_url || $badge->is_raw_svg))
                            <div class="mt-4 p-4 bg-amber-50 rounded-xl border border-amber-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <p class="text-sm font-medium text-amber-800">Current icon:</p>
                                        @if($badge->icon_url)
                                            <img src="{{ $badge->icon_url }}" alt="{{ $badge->name }}" class="w-12 h-12 object-contain rounded-lg border border-amber-300 shadow-sm">
                                        @elseif($badge->is_raw_svg)
                                            <span class="w-12 h-12 flex items-center justify-center text-3xl">{!! $badge->icon_svg !!}</span>
                                        @endif
                                    </div>
                                    <span class="px-2 py-1 text-xs font-medium bg-amber-200 text-amber-800 rounded-full">Active</span>
                                </div>
                            </div>
                        @endif
                    </div>

                    @push('scripts')
                    <script>
                        const iconInput = document.getElementById('icon-input');
                        const iconDropZone = document.getElementById('icon-drop-zone');
                        const iconPreviewContainer = document.getElementById('icon-preview-container');
                        const iconPreview = document.getElementById('icon-preview');
                        const iconFilename = document.getElementById('icon-filename');
                        const iconFilesize = document.getElementById('icon-filesize');
                        const iconRemove = document.getElementById('icon-remove');

                        function formatFileSize(bytes) {
                            if (bytes === 0) return '0 Bytes';
                            const k = 1024;
                            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                            const i = Math.floor(Math.log(bytes) / Math.log(k));
                            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
                        }

                        function handleFile(file) {
                            if (!file) return;
                            if (!file.type.match(/image.*/)) return;

                            const reader = new FileReader();
                            reader.onload = (e) => {
                                iconPreview.src = e.target.result;
                                iconFilename.textContent = file.name;
                                iconFilesize.textContent = formatFileSize(file.size);
                                iconPreviewContainer.classList.remove('hidden');
                            };
                            reader.readAsDataURL(file);
                        }

                        iconInput.addEventListener('change', (e) => handleFile(e.target.files[0]));

                        iconDropZone.addEventListener('dragover', (e) => {
                            e.preventDefault();
                            iconDropZone.classList.add('border-emerald-500', 'bg-emerald-50');
                        });

                        iconDropZone.addEventListener('dragleave', () => {
                            iconDropZone.classList.remove('border-emerald-500', 'bg-emerald-50');
                        });

                        iconDropZone.addEventListener('drop', (e) => {
                            e.preventDefault();
                            iconDropZone.classList.remove('border-emerald-500', 'bg-emerald-50');
                            const files = e.dataTransfer.files;
                            if (files.length) {
                                iconInput.files = files;
                                handleFile(files[0]);
                            }
                        });

                        iconRemove.addEventListener('click', () => {
                            iconInput.value = '';
                            iconPreviewContainer.classList.add('hidden');
                            iconPreview.src = '';
                        });
                    </script>
                    @endpush
                </div>

{{-- Submit --}}
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mt-8 pt-6 border-t border-gray-200">
                    <button type="button" onclick="autoFillBadge()"
                            class="w-full sm:w-auto px-4 py-2 bg-blue-400 hover:bg-blue-500 text-white font-bold rounded-lg transition flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Auto Fill
                    </button>
                    <div class="flex flex-col-reverse sm:flex-row gap-3">
                        <a href="{{ route('admin.gamification.badges.index') }}"
                           class="w-full sm:w-auto text-center px-6 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">
                            Cancel
                        </a>
                        <button type="submit"
                                class="w-full sm:w-auto px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                            {{ $badge ? 'Update Badge' : 'Create Badge' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
function autoFillBadge() {
    const codes = ['first_step', 'rising_star', 'dedicated_helper', 'event_master', 'community_pillar'];
    const namesEn = ['First Step', 'Rising Star', 'Dedicated Helper', 'Event Master', 'Community Pillar'];
    const namesMy = ['Langkah Pertama', 'Bintang Meningkat', 'Pembantu Setia', 'Tuan Acara', 'Pilar Komuniti'];
    const descriptionsEn = ['Complete your first volunteer task', 'Earn 500+ points in a month', 'Volunteer at 10+ events', 'Organize or lead 5+ events', 'Contribute significantly to the community'];
    const descriptionsMy = ['Lengkapkan tugas sukarelawan pertama', 'Peroleh 500+ mata dalam sebulan', 'Sukarelawan di 10+ acara', 'Anjur atau+pimpin 5+ acara', 'Sumbangkan dengan ketara kepada komuniti'];
    const tiers = ['bronze', 'silver', 'gold', 'platinum', 'diamond'];

    const randomIdx = Math.floor(Math.random() * codes.length);

    document.querySelector('input[name="code"]').value = codes[randomIdx];
    document.querySelector('select[name="tier"]').value = tiers[Math.floor(Math.random() * tiers.length)];
    document.querySelector('input[name="name"]').value = namesEn[randomIdx];
    document.querySelector('input[name="name_my"]').value = namesMy[randomIdx];
    document.querySelector('textarea[name="description"]').value = descriptionsEn[randomIdx];
    document.querySelector('textarea[name="description_my"]').value = descriptionsMy[randomIdx];
    document.querySelector('input[name="points_awarded"]').value = [50, 100, 150, 200, 250, 300][Math.floor(Math.random() * 6)];
}
</script>
@endsection

