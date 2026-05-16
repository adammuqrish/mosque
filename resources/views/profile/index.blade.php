@extends('layouts.app')

@section('back', '/dashboard')

@section('title', __('islamic.navigation.profile'))

@section('content')

<div class="max-w-4xl mx-auto space-y-6">

    <!-- STEP 1: Personal Information Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Card Header -->
        <div class="bg-emerald-700 px-4 sm:px-6 py-4 flex justify-between items-center pattern-islamic">
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Personal Information
            </h2>
            <span class="bg-emerald-700 text-xs px-2 py-1 rounded text-white">Account Settings</span>
        </div>
        <div class="p-6">
            <!-- STEP 1: Show validation errors -->
            @if ($errors->has('name') || $errors->has('phone') || $errors->has('age') || $errors->has('address'))
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg flex items-start gap-2">
                    <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-red-700 text-sm">Please fix the errors below.</span>
                </div>
            @endif

            <form action="{{ route('profile.update.info') }}" method="POST" data-loading>
                @csrf

                {{-- Avatar Upload Section --}}
                <div class="mb-6 flex items-center gap-4 p-4 bg-gray-50 rounded-xl border border-gray-200">
                    <div class="relative">
                        @if($user->avatar_url)
                            <img id="avatar-preview" src="{{ $user->avatar_url }}" alt="Profile Picture" 
                                 class="w-20 h-20 rounded-full object-cover border-4 border-emerald-500 shadow-md">
                        @else
                            <div id="avatar-initials" class="w-20 h-20 rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white text-2xl font-bold border-4 border-emerald-300 shadow-md">
                                {{ $user->initials }}
                            </div>
                            <img id="avatar-preview" src="" alt="Profile Picture" class="hidden w-20 h-20 rounded-full object-cover border-4 border-emerald-500 shadow-md">
                        @endif
                        <button type="button" id="avatar-remove" class="absolute -top-1 -right-1 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 shadow-md {{ $user->avatar_url ? '' : 'hidden' }}">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-800">Profile Picture</p>
                        <p class="text-xs text-gray-500 mb-2">Upload a photo to personalize your profile</p>
                        <div class="relative inline-block">
                            <button type="button" id="avatar-upload-btn" onclick="document.getElementById('avatar-input').click()"
                                    class="px-3 py-1.5 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 text-xs font-medium rounded-lg transition flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                {{ $user->avatar_url ? 'Change' : 'Upload' }}
                            </button>
                            <input type="file" id="avatar-input" name="avatar" accept="image/jpeg,image/png,image/gif,image/webp" 
                                   class="hidden" onchange="handleAvatarUpload(event)">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:p-6">
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Full Name</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" 
                            class="w-full border rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition @error('name') border-red-500 ring-2 ring-red-200 @enderror" 
                            required>
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Email (Cannot be changed)</label>
                        <input type="email" value="{{ $user->email }}" 
                            class="w-full border rounded-lg px-4 py-2.5 bg-gray-100 text-gray-500" disabled>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Phone Number</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" 
                            class="w-full border rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition @error('phone') border-red-500 ring-2 ring-red-200 @enderror" 
                            required>
                        @error('phone')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Age <span class="text-gray-400 font-normal">(Optional)</span></label>
                        <input type="number" name="age" value="{{ old('age', $user->age ?? '') }}" 
                            class="w-full border rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition @error('age') border-red-500 ring-2 ring-red-200 @enderror" 
                            placeholder="e.g. 25">
                        @error('age')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Address <span class="text-gray-400 font-normal">(Optional)</span></label>
                        <input type="text" name="address" value="{{ old('address', $user->address ?? '') }}" 
                            class="w-full border rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition @error('address') border-red-500 ring-2 ring-red-200 @enderror" 
                            placeholder="e.g. Jalan Masjid 1, Melaka">
                        @error('address')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
                <div class="mt-6">
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-4 sm:px-6 rounded-lg transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Save Personal Info
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- STEP 3: Volunteer Matching Criteria Section (Member only) -->
    @if(Auth::user()->role == 'member')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Card Header -->
        <div class="bg-blue-600 px-4 sm:px-6 py-4 flex justify-between items-center">
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                </svg>
                Your Volunteer Matching Criteria
            </h2>
            <span class="bg-blue-700 text-xs px-2 py-1 rounded text-white">Profile & Preferences</span>
        </div>
        
        <div class="p-6">
            
            <!-- Show validation errors -->
            @if ($errors->has('skills') || $errors->has('hobbies') || $errors->has('languages') || $errors->has('interests'))
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg flex items-start gap-2">
                    <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-red-700 text-sm">Please fix the validation errors below to continue.</span>
                </div>
            @endif

            <form action="{{ route('profile.update.skills') }}" method="POST" data-loading>
                @csrf
                
                <!-- Core Matching Criteria Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:p-6 mb-8 border-b border-gray-100 pb-6">
                    
                    <!-- SKILLS MANAGER -->
                    <div x-data="tagManager({{ json_encode(is_array($profile->skills ?? null) ? $profile->skills : (json_decode($profile->skills ?? '[]') ?? [])) }}, 'skills')">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Skills <span class="text-red-500">*</span></label>
                        
                        <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <!-- Tag Container -->
                            <div class="flex flex-wrap gap-2 mb-3">
                                <template x-for="(tag, index) in tags" :key="index">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 border border-blue-200 transition">
                                        <span x-text="tag"></span>
                                        <button type="button" @click="removeTag(index)" class="ml-1.5 focus:outline-none text-blue-600 hover:text-blue-900 transition font-bold text-lg leading-none">&times;</button>
                                        <input type="hidden" :name="inputName + '[]'" :value="tag">
                                    </span>
                                </template>
                                <span x-show="tags.length === 0" class="text-sm text-gray-400 italic py-1">No tags added yet.</span>
                            </div>

                            <!-- Input Area -->
                            <div class="relative">
                                <input type="text" x-model="newTag" 
                                    @keydown.enter.prevent="addTag()" 
                                    @keydown.comma.prevent="addTag()" 
                                    class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none transition block text-sm" 
                                    placeholder="Type and press Enter/Comma to add"
                                    :class="duplicateError ? 'border-red-400 focus:ring-red-500 bg-red-50' : 'border-gray-300'">
                                <p x-show="duplicateError" x-transition class="absolute -bottom-5 text-xs text-red-500 font-medium">This tag already exists!</p>
                            </div>
                        </div>
                        @error('skills')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        @error('skills.*')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- HOBBIES MANAGER -->
                    <div x-data="tagManager({{ json_encode(is_array($profile->hobbies ?? null) ? $profile->hobbies : (json_decode($profile->hobbies ?? '[]') ?? [])) }}, 'hobbies')">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Hobbies <span class="text-gray-400 font-normal">(Optional)</span></label>
                        
                        <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <!-- Tag Container -->
                            <div class="flex flex-wrap gap-2 mb-3">
                                <template x-for="(tag, index) in tags" :key="index">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-emerald-100 text-emerald-800 border border-emerald-200 transition">
                                        <span x-text="tag"></span>
                                        <button type="button" @click="removeTag(index)" class="ml-1.5 focus:outline-none text-emerald-600 hover:text-emerald-900 transition font-bold text-lg leading-none">&times;</button>
                                        <input type="hidden" :name="inputName + '[]'" :value="tag">
                                    </span>
                                </template>
                                <span x-show="tags.length === 0" class="text-sm text-gray-400 italic py-1">No tags added yet.</span>
                            </div>

                            <!-- Input Area -->
                            <div class="relative">
                                <input type="text" x-model="newTag" 
                                    @keydown.enter.prevent="addTag()" 
                                    @keydown.comma.prevent="addTag()" 
                                    class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition block text-sm" 
                                    placeholder="Type and press Enter/Comma to add"
                                    :class="duplicateError ? 'border-red-400 focus:ring-red-500 bg-red-50' : 'border-gray-300'">
                                <p x-show="duplicateError" x-transition class="absolute -bottom-5 text-xs text-red-500 font-medium">This tag already exists!</p>
                            </div>
                        </div>
                    </div>

                    <!-- INTERESTS MANAGER -->
                    <div x-data="tagManager({{ json_encode(is_array($profile->interests ?? null) ? $profile->interests : (json_decode($profile->interests ?? '[]') ?? [])) }}, 'interests')">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Interests <span class="text-gray-400 font-normal">(Optional)</span></label>
                        
                        <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <!-- Tag Container -->
                            <div class="flex flex-wrap gap-2 mb-3">
                                <template x-for="(tag, index) in tags" :key="index">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800 border border-purple-200 transition">
                                        <span x-text="tag"></span>
                                        <button type="button" @click="removeTag(index)" class="ml-1.5 focus:outline-none text-purple-600 hover:text-purple-900 transition font-bold text-lg leading-none">&times;</button>
                                        <input type="hidden" :name="inputName + '[]'" :value="tag">
                                    </span>
                                </template>
                                <span x-show="tags.length === 0" class="text-sm text-gray-400 italic py-1">No tags added yet.</span>
                            </div>

                            <!-- Input Area -->
                            <div class="relative">
                                <input type="text" x-model="newTag" 
                                    @keydown.enter.prevent="addTag()" 
                                    @keydown.comma.prevent="addTag()" 
                                    class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:outline-none transition block text-sm" 
                                    placeholder="Type and press Enter/Comma to add"
                                    :class="duplicateError ? 'border-red-400 focus:ring-red-500 bg-red-50' : 'border-gray-300'">
                                <p x-show="duplicateError" x-transition class="absolute -bottom-5 text-xs text-red-500 font-medium">This tag already exists!</p>
                            </div>
                        </div>
                    </div>

                    <!-- LANGUAGES MANAGER -->
                    <div x-data="tagManager({{ json_encode(is_array($profile->languages ?? null) ? $profile->languages : (json_decode($profile->languages ?? '[]') ?? [])) }}, 'languages')">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Languages <span class="text-gray-400 font-normal">(Optional)</span></label>
                        
                        <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <!-- Tag Container -->
                            <div class="flex flex-wrap gap-2 mb-3">
                                <template x-for="(tag, index) in tags" :key="index">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 border border-yellow-200 transition">
                                        <span x-text="tag"></span>
                                        <button type="button" @click="removeTag(index)" class="ml-1.5 focus:outline-none text-yellow-600 hover:text-yellow-900 transition font-bold text-lg leading-none">&times;</button>
                                        <input type="hidden" :name="inputName + '[]'" :value="tag">
                                    </span>
                                </template>
                                <span x-show="tags.length === 0" class="text-sm text-gray-400 italic py-1">No tags added yet.</span>
                            </div>

                            <!-- Input Area -->
                            <div class="relative">
                                <input type="text" x-model="newTag" 
                                    @keydown.enter.prevent="addTag()" 
                                    @keydown.comma.prevent="addTag()" 
                                    class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-yellow-500 focus:outline-none transition block text-sm" 
                                    placeholder="Type and press Enter/Comma to add"
                                    :class="duplicateError ? 'border-red-400 focus:ring-red-500 bg-red-50' : 'border-gray-300'">
                                <p x-show="duplicateError" x-transition class="absolute -bottom-5 text-xs text-red-500 font-medium">This tag already exists!</p>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Logistics & Availability Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:p-6 mb-8 border-b border-gray-100 pb-6">
                    
                    <!-- Availability -->
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">General Availability</label>
                        <div class="space-y-2 bg-gray-50 p-3 rounded-lg border border-gray-200">
                            <div class="flex items-center">
                                <input type="checkbox" name="availability[weekend]" value="true" 
                                @if($profile && is_array($profile->availability) && in_array('weekend', $profile->availability)) checked @endif
                                class="mr-2 h-5 w-5 text-blue-600 bg-white border-gray-300 rounded focus:ring-blue-500">
                                <label class="text-gray-700 text-sm">Weekend</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" name="availability[weekday]" value="true" 
                                @if($profile && is_array($profile->availability) && in_array('weekday', $profile->availability)) checked @endif
                                class="mr-2 h-5 w-5 text-blue-600 bg-white border-gray-300 rounded focus:ring-blue-500">
                                <label class="text-gray-700 text-sm">Weekday</label>
                            </div>
                        </div>
                    </div>

                    <!-- Health -->
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Health Status</label>
                        <select name="health_status" class="w-full border rounded-lg px-4 py-2.5 bg-white border-gray-300 focus:ring-2 focus:ring-blue-500 focus:outline-none transition text-sm">
                            <option value="Healthy" {{ $profile && $profile->health_status == 'Healthy' ? 'selected' : '' }}>Healthy & Fit</option>
                            <option value="Light" {{ $profile && $profile->health_status == 'Light' ? 'selected' : '' }}>Light Activities Only</option>
                            <option value="Limited" {{ $profile && $profile->health_status == 'Limited' ? 'selected' : '' }}>Limited Mobility</option>
                        </select>
                    </div>

                    <!-- Location -->
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Current Location <span class="text-gray-400 font-normal">(Optional)</span></label>
                        <input type="text" name="location" 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none transition text-sm" 
                            value="{{ $profile->location ?? '' }}" placeholder="e.g. Melaka">
                    </div>

                    <div class="hidden md:block"></div> <!-- Spacing -->

                    <!-- Experience (Full width) -->
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Volunteer Experience <span class="text-gray-400 font-normal">(Optional)</span></label>
                        <textarea name="experience" rows="3" 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none transition resize-none text-sm"
                            placeholder="Describe your past experience...">{{ $profile->experience ?? '' }}</textarea>
                    </div>

                    <!-- Long Term Availability (Full width) -->
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Long-Term Availability <span class="text-gray-400 font-normal">(Optional)</span></label>
                        <textarea name="long_term_availability" rows="2" 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none transition resize-none text-sm"
                            placeholder="e.g. Available every weekend for 6 months">{{ $profile->long_term_availability ?? '' }}</textarea>
                    </div>

                </div>

                <!-- BUTTON -->
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="autoFillProfile()" class="bg-blue-400 hover:bg-blue-500 text-white font-bold py-3 px-4 sm:px-6 rounded-lg shadow-md transition duration-200 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Auto Fill
                    </button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg shadow-md transition duration-200 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Save Profile Criteria
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tag Manager Alpine Component -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('tagManager', (initialTags, inputName) => ({
                tags: initialTags || [],
                inputName: inputName,
                newTag: '',
                duplicateError: false,
                
                addTag() {
                    let val = this.newTag.trim();
                    if (val === '') return;
                    
                    let lowerVal = val.toLowerCase();
                    let isDuplicate = this.tags.some(t => t.toLowerCase() === lowerVal);
                    
                    if (isDuplicate) {
                        this.duplicateError = true;
                        setTimeout(() => this.duplicateError = false, 2000);
                        return;
                    }
                    
                    this.tags.push(val);
                    this.newTag = '';
                    this.duplicateError = false;
                },
                
                removeTag(index) {
                    this.tags.splice(index, 1);
                }
            }))
        });
    </script>

    <script>
    function autoFillProfile() {
        const skillsPool = ['Public Speaking', 'Teaching', 'Cooking', 'First Aid', 'Driving', 'Photography', 'Graphic Design', 'Web Development', 'Accounting', 'Event Planning', 'Counseling', 'Translation'];
        const hobbiesPool = ['Reading Quran', 'Gardening', 'Football', 'Calligraphy', 'Cooking', 'Hiking', 'Photography', 'Fishing'];
        const interestsPool = ['Education', 'Community Service', 'Youth Development', 'Environmental', 'Health & Wellness', 'Technology', 'Arts & Culture'];
        const languagesPool = ['Malay', 'English', 'Arabic', 'Mandarin', 'Tamil', 'Urdu', 'Thai'];
        const locations = ['Melaka', 'Kuala Lumpur', 'Johor Bahru', 'Shah Alam', 'Petaling Jaya', 'Seremban', 'Port Dickson'];
        const experiences = [
            'Volunteered at mosque food distribution during Ramadan 2024. Helped coordinate 20+ volunteers for daily iftar preparation.',
            'Organized community Quran study circle for 2 years. Managed schedules and teaching materials for 30 participants.',
            'Participated in mosque cleaning and maintenance programs monthly for the past year.',
            'Led youth education program at local Islamic center. Taught basic Arabic reading to 15 children weekly.',
            'Volunteered as translator during international Islamic conference in 2023. Provided Malay-English translation services.'
        ];
        const longTermAvailabilities = [
            'Available every weekend for the next 12 months. Can commit to 4-6 hours per session.',
            'Available on weekdays after 5 PM. Looking for long-term commitment of at least 6 months.',
            'Can volunteer during school holidays and public holidays throughout the year.',
            'Available for special events and programs. Can commit to monthly community service activities.',
            'Free on Friday mornings and Saturday afternoons. Willing to commit for the full year.'
        ];

        const pickRandom = (arr, min, max) => {
            const count = Math.floor(Math.random() * (max - min + 1)) + min;
            return arr.sort(() => 0.5 - Math.random()).slice(0, count);
        };

        const addTags = (name, tags) => {
            const el = document.querySelector(`[x-data*="tagManager"][x-data*="${name}"]`);
            if (el && el.__x) {
                const component = el.__x.$data;
                tags.forEach(tag => {
                    if (!component.tags.includes(tag)) {
                        component.tags.push(tag);
                    }
                });
            }
        };

        addTags('skills', pickRandom(skillsPool, 3, 5));
        addTags('hobbies', pickRandom(hobbiesPool, 2, 3));
        addTags('interests', pickRandom(interestsPool, 1, 3));
        addTags('languages', pickRandom(languagesPool, 1, 2));

        // Set checkboxes
        const weekendCheck = document.querySelector('input[name="availability[weekend]"]');
        const weekdayCheck = document.querySelector('input[name="availability[weekday]"]');
        if (weekendCheck) weekendCheck.checked = Math.random() > 0.3;
        if (weekdayCheck) weekdayCheck.checked = Math.random() > 0.5;

        // Set health status
        const healthSelect = document.querySelector('select[name="health_status"]');
        if (healthSelect) {
            const options = ['Healthy', 'Light', 'Limited'];
            healthSelect.value = options[Math.floor(Math.random() * options.length)];
        }

        // Set location
        const locationInput = document.querySelector('input[name="location"]');
        if (locationInput) locationInput.value = locations[Math.floor(Math.random() * locations.length)];

        // Set experience
        const experienceTextarea = document.querySelector('textarea[name="experience"]');
        if (experienceTextarea) experienceTextarea.value = experiences[Math.floor(Math.random() * experiences.length)];

        // Set long-term availability
        const longTermTextarea = document.querySelector('textarea[name="long_term_availability"]');
        if (longTermTextarea) longTermTextarea.value = longTermAvailabilities[Math.floor(Math.random() * longTermAvailabilities.length)];
    }
    </script>
    @endif

    <!-- STEP 8: Referral Program Section (Member only) -->
    @if(Auth::user()->role == 'member')
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-8 rounded-2xl shadow-xl border border-blue-100 mb-8">
        <div class="flex items-center mb-6">
            <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center mr-4">
                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M15 5a1 1 0 00-1 1v3h-3a1 1 0 100 2h3v3a1 1 0 102 0v-3h3a1 1 0 100-2h-3V6a1 1 0 00-1-1z"/>
                    <path d="M5 5a1 1 0 00-1 1v3H1a1 1 0 100 2h3v3a1 1 0 102 0v-3h3a1 1 0 100-2H6V6a1 1 0 00-1-1z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-2xl font-bold text-gray-900">Referral Program</h3>
                <p class="text-gray-600">Invite friends and earn <span class="font-bold text-green-600">15 points</span> per successful referral!</p>
            </div>
        </div>

        <div x-data="referral({{ Js::from($referralCode) }}, {{ Js::from($canRegenerate) }})" class="space-y-4">
            
            <!-- STEP 1: Show Generate Button if user has no code -->
            <template x-if="!code">
                <div>
                    <button @click="generateCode()" 
                        :disabled="loading"
                        class="w-full bg-gradient-to-r from-blue-500 to-blue-600 text-white py-4 px-4 sm:px-6 rounded-2xl font-semibold text-lg shadow-lg hover:shadow-xl hover:from-blue-600 hover:to-blue-700 transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg x-show="!loading" class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <svg x-show="loading" class="animate-spin w-6 h-6 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="!loading ? 'Generate My Referral Code' : 'Generating...'"></span>
                    </button>
                    <p class="text-center text-sm text-gray-500 mt-2">Your unique code to share with friends during registration</p>
                </div>
            </template>
            
            <!-- STEP 2: Show Code Display if user has a code -->
            <template x-if="code">
                <div>
                    <div class="bg-white p-4 sm:p-6 rounded-2xl shadow-md border-2 border-dashed border-gray-200">
                        <div class="flex items-center space-x-3">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Your Referral Code</label>
                                <input :value="code" readonly 
                                       class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl text-2xl font-mono font-bold tracking-wider text-gray-900 focus:border-blue-300 focus:ring-2 focus:ring-blue-200 focus:bg-white transition-all duration-200" />
                            </div>
                            <!-- Copy Button -->
                            <button @click="copyCode()" 
                                class="bg-green-500 hover:bg-green-600 text-white p-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-200 flex items-center"
                                :class="{'animate-pulse ring-2 ring-green-200': copying}">
                                <svg x-show="!copying" class="w-6 h-6 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                                <span x-text="!copying ? 'Copy' : 'Copied!'"></span>
                            </button>
                            <!-- Regenerate Button (only if allowed) -->
                            <template x-if="canRegenerate">
                                <button @click="regenerateCode()" 
                                    :disabled="loading"
                                    class="bg-yellow-500 hover:bg-yellow-600 text-white p-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-200 flex items-center disabled:opacity-50">
                                    <svg class="w-6 h-6 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    New Code
                                </button>
                            </template>
                        </div>
                    </div>
                    <div class="bg-green-50 border border-green-200 rounded-xl p-4 mt-4">
                        <p class="text-sm text-green-800"><strong>How it works:</strong> Share this code with friends. When they register using your code, you get <strong>15 points</strong> instantly!</p>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Alpine.js Component for Referral Section -->
    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('referral', (initialCode, initialCanRegenerate) => ({
            code: initialCode,
            copying: false,
            loading: false,
            canRegenerate: initialCanRegenerate,
            
            /**
             * Generate or regenerate referral code via AJAX POST request.
             * Calls /profile/referral/generate endpoint.
             */
            generateCode() {
                this.loading = true;
                
                // STEP 1: Get CSRF token from meta tag
                const csrfToken = document.querySelector('[name="csrf-token"]')?.content || 
                                  document.querySelector('[name="_token"]')?.content;
                
                // STEP 2: Send POST request to generate code
                fetch('{{ route('profile.referral.generate') }}', { 
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    }
                })
                .then(res => res.json())
                .then(data => {
                    this.loading = false;
                    if (data.success) {
                        this.code = data.code;
                        this.$dispatch('notify', { type: 'success', message: 'Referral code generated successfully!' });
                    } else {
                        this.$dispatch('notify', { type: 'error', message: data.error || 'Failed to generate code.' });
                    }
                })
                .catch(() => {
                    this.loading = false;
                    this.$dispatch('notify', { type: 'error', message: 'Generation failed. Please try again.' });
                });
            },
            
            /**
             * Copy referral code to clipboard using Clipboard API.
             * Shows temporary "Copied!" feedback.
             */
            copyCode() {
                navigator.clipboard.writeText(this.code).then(() => {
                    this.copying = true;
                    this.$dispatch('notify', { type: 'success', message: 'Code copied to clipboard!' });
                    setTimeout(() => this.copying = false, 2000);
                }).catch(() => {
                    // Fallback for older browsers
                    const input = document.createElement('input');
                    input.value = this.code;
                    document.body.appendChild(input);
                    input.select();
                    document.execCommand('copy');
                    document.body.removeChild(input);
                    this.copying = true;
                    this.$dispatch('notify', { type: 'success', message: 'Code copied to clipboard!' });
                    setTimeout(() => this.copying = false, 2000);
                });
            },
            
            /**
             * Regenerate referral code with confirmation dialog.
             * Old referrals still work, but new ones need the new code.
             */
            regenerateCode() {
                showConfirmDialog('Generate New Referral Code', 'Generate a new code? Your existing referrals will still work, but new users will need the new code.', 'Generate', 'bg-blue-600 hover:bg-blue-700', () => this.generateCode());
            }
        }));
    });
    </script>
    @endif

    <!-- STEP 7: Security / Password Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Card Header -->
        <div class="bg-gray-700 px-4 sm:px-6 py-4">
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
                Security Settings
            </h2>
        </div>
        <div class="p-6">
            <!-- STEP 2: Show password validation errors -->
            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg flex items-start gap-2">
                    <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-red-700 text-sm">{{ $errors->first() }}</span>
                </div>
            @endif

            <form action="{{ route('profile.update.password') }}" method="POST" data-loading>
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:p-6">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Current Password</label>
                        <input type="password" name="current_password" 
                            class="w-full border rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-gray-500 focus:outline-none transition @error('current_password') border-red-500 ring-2 ring-red-200 @enderror" 
                            required>
                        @error('current_password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">New Password</label>
                        <input type="password" name="new_password" 
                            class="w-full border rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-gray-500 focus:outline-none transition @error('new_password') border-red-500 ring-2 ring-red-200 @enderror" 
                            placeholder="Min 8 characters" required>
                        @error('new_password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Confirm New Password</label>
                        <input type="password" name="new_password_confirmation" 
                            class="w-full border rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-gray-500 focus:outline-none transition" 
                            placeholder="Re-enter new password" required>
                    </div>
                </div>
                <div class="mt-6">
                    <button type="submit" class="bg-gray-700 hover:bg-gray-800 text-white font-bold py-2.5 px-4 sm:px-6 rounded-lg transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

@section('scripts')
<script>
function handleAvatarUpload(event) {
    const file = event.target.files[0];
    if (!file) return;

    // Validate file type
    if (!file.type.match(/image.*/)) {
        showNotification('error', 'Invalid File', 'Please select a valid image file (JPEG, PNG, GIF, or WebP)');
        return;
    }

    // Validate file size (2MB max)
    if (file.size > 2 * 1024 * 1024) {
        showNotification('error', 'File Too Large', 'File size must be less than 2MB');
        return;
    }

    const formData = new FormData();
    formData.append('avatar', file);

    // Show loading state
    const btn = document.getElementById('avatar-upload-btn');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<svg class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Uploading...';
    btn.disabled = true;

    fetch('{{ route("profile.update.avatar") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update avatar preview
            const preview = document.getElementById('avatar-preview');
            const initials = document.getElementById('avatar-initials');
            
            preview.src = data.avatar_url;
            preview.classList.remove('hidden');
            if (initials) initials.classList.add('hidden');
            
            // Show remove button
            document.getElementById('avatar-remove').classList.remove('hidden');
            
            // Update button text
            btn.innerHTML = 'Change';
            
            // Show success message
            showNotification('success', 'Success', 'Profile picture updated successfully!');
        } else {
            showNotification('error', 'Error', 'Failed to upload avatar');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('error', 'Error', 'An error occurred while uploading');
    })
    .finally(() => {
        btn.disabled = false;
    });
}

// Handle avatar remove with confirmation modal
document.getElementById('avatar-remove').addEventListener('click', function() {
    const removeBtn = this;
    
    // Use the system's confirm modal
    const modal = document.getElementById('global-modal');
    const modalBody = document.getElementById('modal-body');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    modalBody.innerHTML = `
        <h3 class="text-xl font-bold text-gray-800 mb-2">Remove Profile Picture</h3>
        <p class="text-gray-600 mb-4">Are you sure you want to remove your profile picture? This action cannot be undone.</p>
        <div class="flex justify-end gap-3">
            <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition font-medium">
                Cancel
            </button>
            <button type="button" onclick="processAvatarRemove()" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition font-medium">
                Remove
            </button>
        </div>
    `;
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    // Store remove button reference for later
    window.currentAvatarRemoveBtn = removeBtn;
});

function processAvatarRemove() {
    closeModal();
    const removeBtn = window.currentAvatarRemoveBtn;
    
    fetch('{{ route("profile.delete.avatar") }}', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show initials instead
            const preview = document.getElementById('avatar-preview');
            const initials = document.getElementById('avatar-initials');
            
            preview.classList.add('hidden');
            if (initials) initials.classList.remove('hidden');
            
            // Hide remove button
            removeBtn.classList.add('hidden');
            
            // Reset file input
            document.getElementById('avatar-input').value = '';
            
            // Update button text
            const uploadBtn = document.getElementById('avatar-upload-btn');
            uploadBtn.innerHTML = 'Upload';
            
            showNotification('success', 'Removed', 'Profile picture has been removed');
        }
    });
}
</script>
@endsection

@endsection

