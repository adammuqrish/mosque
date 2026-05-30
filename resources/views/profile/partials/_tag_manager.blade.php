@php
    $colors = [
        'blue' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'border' => 'border-blue-200', 'ring' => 'focus:ring-blue-500', 'btn' => 'text-blue-600 hover:text-blue-900'],
        'emerald' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-800', 'border' => 'border-emerald-200', 'ring' => 'focus:ring-emerald-500', 'btn' => 'text-emerald-600 hover:text-emerald-900'],
        'purple' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-800', 'border' => 'border-purple-200', 'ring' => 'focus:ring-purple-500', 'btn' => 'text-purple-600 hover:text-purple-900'],
        'yellow' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'border' => 'border-yellow-200', 'ring' => 'focus:ring-yellow-500', 'btn' => 'text-yellow-600 hover:text-yellow-900'],
    ];
    $c = $colors[$color] ?? $colors['blue'];
    $rawValue = $profile->$name ?? '[]';
    $tagsJson = json_encode(
        is_array($rawValue) ? $rawValue : (json_decode($rawValue) ?? [])
    );
@endphp

<div x-data="tagManager({{ $tagsJson }}, '{{ $name }}')">
    <label class="block text-gray-700 text-sm font-bold mb-2">{{ $label }}@if($required) <span class="text-red-500">*</span>@else <span class="text-gray-400 font-normal">(Optional)</span>@endif</label>

    <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
        <div class="flex flex-wrap gap-2 mb-3">
            <template x-for="(tag, index) in tags" :key="index">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $c['bg'] }} {{ $c['text'] }} border {{ $c['border'] }} transition">
                    <span x-text="tag"></span>
                    <button type="button" @click="removeTag(index)" class="ml-1.5 focus:outline-none {{ $c['btn'] }} transition font-bold text-lg leading-none">&times;</button>
                    <input type="hidden" :name="inputName + '[]'" :value="tag">
                </span>
            </template>
            <span x-show="tags.length === 0" class="text-sm text-gray-400 italic py-1">No tags added yet.</span>
        </div>

        <div class="relative">
            <input type="text" x-model="newTag"
                @keydown.enter.prevent="addTag()"
                @keydown.comma.prevent="addTag()"
                class="w-full border rounded-lg px-4 py-2 focus:ring-2 {{ $c['ring'] }} focus:outline-none transition block text-sm"
                placeholder="Type and press Enter/Comma to add"
                :class="duplicateError ? 'border-red-400 focus:ring-red-500 bg-red-50' : 'border-gray-300'">
            <p x-show="duplicateError" x-transition class="absolute -bottom-5 text-xs text-red-500 font-medium">This tag already exists!</p>
        </div>
    </div>
    @if($errors->has($name))
        <p class="text-red-500 text-xs mt-1">{{ $errors->first($name) }}</p>
    @endif
    @if($errors->has($name . '.*'))
        <p class="text-red-500 text-xs mt-1">{{ $errors->first($name . '.*') }}</p>
    @endif
    @if(!empty($hint))
        <p class="text-gray-400 text-xs mt-2">{{ $hint }}</p>
    @endif
</div>
