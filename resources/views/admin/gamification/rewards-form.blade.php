@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-amber-50 via-white to-orange-50 py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Back --}}
        <a href="{{ route('admin.gamification.rewards.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 transition font-medium mb-6">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back
        </a>

        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $reward ? 'Edit Reward' : 'Create Reward' }}</h1>
                <p class="text-gray-600 mt-1">{{ $reward ? 'Update reward details' : 'Add a new reward to the catalog' }}</p>
            </div>
        </div>

        {{-- Form --}}
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <form action="{{ $reward ? route('admin.gamification.rewards.update', $reward) : route('admin.gamification.rewards.store') }}"
                  method="POST"
                  enctype="multipart/form-data">
                @csrf
                @if($reward)
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Code --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Code <span class="text-red-500">*</span></label>
                        <input type="text" name="code"
                               value="{{ old('code', $reward->code ?? '') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('code') border-red-500 @enderror"
                               placeholder="e.g., early_registration">
                        @error('code') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Category --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category <span class="text-red-500">*</span></label>
                        <select name="category"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('category') border-red-500 @enderror">
                            @php
                                $categories = [
                                    'facilities' => 'Facilities',
                                    'recognition' => 'Recognition',
                                    'events' => 'Events',
                                    'merchandise_common' => 'Merchandise (Common)',
                                    'merchandise_limited' => 'Merchandise (Limited Edition)',
                                ];
                            @endphp
                            @foreach($categories as $value => $label)
                                <option value="{{ $value }}" {{ old('category', $reward->category ?? '') === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('category') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Name (EN) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Name (English) <span class="text-red-500">*</span></label>
                        <input type="text" name="name"
                               value="{{ old('name', $reward->name ?? '') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('name') border-red-500 @enderror"
                               placeholder="e.g., Early Event Registration">
                        @error('name') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Name (MY) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Name (Malay) <span class="text-red-500">*</span></label>
                        <input type="text" name="name_my"
                               value="{{ old('name_my', $reward->name_my ?? '') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('name_my') border-red-500 @enderror"
                               placeholder="e.g., Pendaftaran Acara Awal">
                        @error('name_my') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Description (EN) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description (English) <span class="text-red-500">*</span></label>
                        <textarea name="description" rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('description') border-red-500 @enderror"
                                  placeholder="Describe what this reward is">{{ old('description', $reward->description ?? '') }}</textarea>
                        @error('description') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Description (MY) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description (Malay) <span class="text-red-500">*</span></label>
                        <textarea name="description_my" rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('description_my') border-red-500 @enderror"
                                  placeholder="Terangkan ganjaran ini">{{ old('description_my', $reward->description_my ?? '') }}</textarea>
                        @error('description_my') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Points Cost --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Points Cost <span class="text-red-500">*</span></label>
                        <input type="number" name="points_cost"
                               value="{{ old('points_cost', $reward->points_cost ?? 0) }}"
                               min="1"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('points_cost') border-red-500 @enderror"
                               placeholder="e.g., 100">
                        @error('points_cost') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Stock Quantity --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Stock Quantity <span class="text-gray-400">(optional)</span></label>
                        <input type="number" name="stock_quantity"
                               value="{{ old('stock_quantity', $reward->stock_quantity ?? '') }}"
                               min="0"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('stock_quantity') border-red-500 @enderror"
                               placeholder="Leave empty for unlimited">
                        <p class="mt-1 text-xs text-gray-400">Leave empty for unlimited stock.</p>
                        @error('stock_quantity') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Valid From --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Valid From <span class="text-gray-400">(optional)</span></label>
                        <input type="date" name="valid_from"
                               value="{{ old('valid_from', $reward->valid_from ?? '') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('valid_from') border-red-500 @enderror">
                        @error('valid_from') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Valid Until --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Valid Until <span class="text-gray-400">(optional)</span></label>
                        <input type="date" name="valid_until"
                               value="{{ old('valid_until', $reward->valid_until ?? '') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('valid_until') border-red-500 @enderror">
                        @error('valid_until') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Image Upload --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Image <span class="text-gray-400">(optional)</span></label>
                        <div class="relative">
                            <div id="image-drop-zone" class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center cursor-pointer hover:border-emerald-500 hover:bg-emerald-50/50 transition-all duration-200 group">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-12 h-12 mb-3 rounded-full bg-emerald-100 flex items-center justify-center group-hover:bg-emerald-200 transition-colors">
                                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium text-gray-700 group-hover:text-emerald-700">Click to upload or drag & drop</p>
                                    <p class="text-xs text-gray-400 mt-1">JPG, PNG, GIF, WebP (Max 2MB)</p>
                                </div>
                                <input type="file" name="image" id="image-input" accept="image/jpeg,image/png,image/gif,image/webp"
                                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            </div>
                        </div>
                        @error('image') <p class="mt-2 text-sm text-red-500">{{ $message }}</p> @enderror

                        {{-- Preview Area --}}
                        <div id="image-preview-container" class="hidden mt-4 p-4 bg-gray-50 rounded-xl border border-gray-200">
                            <p class="text-sm font-medium text-gray-700 mb-3">Preview</p>
                            <div class="flex items-center gap-4">
                                <div class="relative">
                                    <img id="image-preview" src="" alt="Preview" class="w-16 h-16 object-contain rounded-lg border border-gray-300 shadow-sm">
                                    <button type="button" id="image-remove" class="absolute -top-2 -right-2 w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 shadow-md">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                                <div class="flex-1">
                                    <p id="image-filename" class="text-sm font-medium text-gray-800 truncate"></p>
                                    <p id="image-filesize" class="text-xs text-gray-500"></p>
                                </div>
                            </div>
                        </div>

                        @if($reward && ($reward->image_url || $reward->is_raw_svg))
                            <div class="mt-4 p-4 bg-amber-50 rounded-xl border border-amber-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <p class="text-sm font-medium text-amber-800">Current image:</p>
                                        @if($reward->image_url)
                                            <img src="{{ $reward->image_url }}" alt="{{ $reward->name }}" class="w-12 h-12 object-contain rounded-lg border border-amber-300 shadow-sm">
                                        @elseif($reward->is_raw_svg)
                                            <span class="w-12 h-12 flex items-center justify-center text-3xl">{!! $reward->image_svg !!}</span>
                                        @endif
                                    </div>
                                    <span class="px-2 py-1 text-xs font-medium bg-amber-200 text-amber-800 rounded-full">Active</span>
                                </div>
                            </div>
                        @endif
                    </div>

                    @push('scripts')
                    <script>
                        const imageInput = document.getElementById('image-input');
                        const imageDropZone = document.getElementById('image-drop-zone');
                        const imagePreviewContainer = document.getElementById('image-preview-container');
                        const imagePreview = document.getElementById('image-preview');
                        const imageFilename = document.getElementById('image-filename');
                        const imageFilesize = document.getElementById('image-filesize');
                        const imageRemove = document.getElementById('image-remove');

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
                                imagePreview.src = e.target.result;
                                imageFilename.textContent = file.name;
                                imageFilesize.textContent = formatFileSize(file.size);
                                imagePreviewContainer.classList.remove('hidden');
                            };
                            reader.readAsDataURL(file);
                        }

                        imageInput.addEventListener('change', (e) => handleFile(e.target.files[0]));

                        imageDropZone.addEventListener('dragover', (e) => {
                            e.preventDefault();
                            imageDropZone.classList.add('border-emerald-500', 'bg-emerald-50');
                        });

                        imageDropZone.addEventListener('dragleave', () => {
                            imageDropZone.classList.remove('border-emerald-500', 'bg-emerald-50');
                        });

                        imageDropZone.addEventListener('drop', (e) => {
                            e.preventDefault();
                            imageDropZone.classList.remove('border-emerald-500', 'bg-emerald-50');
                            const files = e.dataTransfer.files;
                            if (files.length) {
                                imageInput.files = files;
                                handleFile(files[0]);
                            }
                        });

                        imageRemove.addEventListener('click', () => {
                            imageInput.value = '';
                            imagePreviewContainer.classList.add('hidden');
                            imagePreview.src = '';
                        });
                    </script>
                    @endpush
                </div>

                {{-- Submit --}}
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mt-8 pt-6 border-t border-gray-200">
                    <button type="button" onclick="autoFillReward()"
                            class="w-full sm:w-auto px-4 py-2 bg-blue-400 hover:bg-blue-500 text-white font-bold rounded-lg transition flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Auto Fill
                    </button>
                    <div class="flex flex-col-reverse sm:flex-row gap-3">
                        <a href="{{ route('admin.gamification.rewards.index') }}"
                           class="w-full sm:w-auto text-center px-6 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">
                            Cancel
                        </a>
                        <button type="submit"
                                class="w-full sm:w-auto px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                            {{ $reward ? 'Update Reward' : 'Create Reward' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
function autoFillReward() {
    const codes = ['priority_parking', 'family_booking', 'iftar_meal', 'mosque_tshirt', 'kopiah'];
    const namesEn = ['Priority Parking (Friday)', 'Family Facility Booking', 'Free Iftar Meal (Ramadan)', 'Mosque T-shirt', 'Mosque Kopiah (Embroidered)'];
    const namesMy = ['Tempat Letak Kereta Keutamaan', 'Tempahan Kemudahan Keluarga', 'Makanan Berbuka Percuma', 'Kemeja-T Masjid', 'Kopiah Masjid (Sulaman)'];
    const descriptionsEn = ['Reserved parking near mosque entrance', 'Book mosque hall for family events', 'Complimentary iftar during Ramadan', 'Mosque-branded t-shirt', 'Premium embroidered kopiah'];
    const descriptionsMy = ['Tempat letak kenderaan berhampiran masjid', 'Tempah dewan masjid untuk keluarga', 'Makanan berbuka percuma semasa Ramadan', 'Kemeja-T berjenama masjid', 'Kopiah bersulam premium'];
    const categories = ['facilities', 'facilities', 'events', 'merchandise_common', 'merchandise_limited'];

    const randomIdx = Math.floor(Math.random() * codes.length);

    document.querySelector('input[name="code"]').value = codes[randomIdx];
    document.querySelector('select[name="category"]').value = categories[randomIdx];
    document.querySelector('input[name="name"]').value = namesEn[randomIdx];
    document.querySelector('input[name="name_my"]').value = namesMy[randomIdx];
    document.querySelector('textarea[name="description"]').value = descriptionsEn[randomIdx];
    document.querySelector('textarea[name="description_my"]').value = descriptionsMy[randomIdx];
    document.querySelector('input[name="points_cost"]').value = Math.floor(Math.random() * 400) + 50;
    document.querySelector('input[name="stock_quantity"]').value = Math.floor(Math.random() * 50) + 10;
}
</script>
@endsection

