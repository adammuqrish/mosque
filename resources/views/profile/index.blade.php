@extends('layouts.app')

@section('title', 'My Profile')

@section('content')

<div class="max-w-4xl mx-auto space-y-6">

    <!-- SECTION 1: PERSONAL INFO -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-emerald-600 px-6 py-4 flex justify-between items-center">
            <h2 class="text-xl font-bold text-white">Personal Information</h2>
            <span class="bg-emerald-700 text-xs px-2 py-1 rounded text-white">Account Settings</span>
        </div>
        <div class="p-6">
            <form action="{{ route('profile.update.info') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Full Name</label>
                        <input type="text" name="name" value="{{ $user->name }}" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Email (Cannot be changed)</label>
                        <input type="email" value="{{ $user->email }}" class="w-full border rounded px-3 py-2 bg-gray-100" disabled>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Phone Number</label>
                        <input type="text" name="phone" value="{{ $user->phone }}" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Age</label>
                        <input type="number" name="age" value="{{ $user->age ?? '' }}" class="w-full border rounded px-3 py-2" placeholder="e.g. 25">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Address</label>
                        <input type="text" name="address" value="{{ $user->address ?? '' }}" class="w-full border rounded px-3 py-2" placeholder="e.g. Jalan Masjid 1, Melaka">
                    </div>

                </div>
                <div class="mt-6">
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-6 rounded">
                        Save Personal Info
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- SECTION 2: VOLUNTEER SKILLS (Hanya untuk Member) -->
    @if(Auth::user()->role == 'member')
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-blue-600 px-6 py-4 flex justify-between items-center">
            <h2 class="text-xl font-bold text-white">My Skills & Interests</h2>
            <span class="bg-blue-700 text-xs px-2 py-1 rounded text-white">For Recommendations</span>
        </div>
        
        <div class="p-6">
            
            <!-- A. DISPLAY CURRENT SKILLS (VISUAL) -->
            @if($profile && !empty($profile->skills))
            <div class="mb-8 bg-blue-50 p-4 rounded-lg border border-blue-200">
                <h3 class="text-sm font-bold text-gray-600 uppercase mb-3">Your Current Skills</h3>
                <div class="flex flex-wrap gap-2">
                    @php
                        $skillList = $profile->skills ?? [];
                        if (is_string($skillList)) { $skillList = json_decode($skillList, true); }
                        if (!is_array($skillList)) { $skillList = []; }
                    @endphp
                    @foreach($skillList as $skill)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-emerald-100 text-emerald-800 border border-emerald-200">
                        {{ $skill }}
                    </span>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- B. EDIT FORM -->
            <form action="{{ route('profile.update.skills') }}" method="POST">
                @csrf
                
                <!-- HEADER FORM: CORE SKILLS & AVAILABILITY -->
                <div class="mb-8 pb-6 border-b border-gray-100">
                    <h4 class="text-sm font-bold text-gray-500 uppercase mb-4 tracking-wide">Core Information</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <!-- Skills (Penuh lebar) -->
                        <div class="md:col-span-2">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Your Skills</label>
                            <p class="text-xs text-gray-500 mb-1">Separate with comma (e.g., Cooking, Cleaning)</p>
                            <input type="text" name="skills" class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none" placeholder="Cooking, Cleaning, Audio Visual" 
                            value="{{ $profile && is_array($profile->skills) ? implode(', ', $profile->skills) : '' }}" required>
                        </div>

                        <!-- Availability (Kanan) -->
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Availability</label>
                            <div class="space-y-2 bg-gray-50 p-3 rounded border">
                                <div class="flex items-center">
                                    <input type="checkbox" name="availability[weekend]" value="true" 
                                    @if($profile && is_array($profile->availability) && in_array('weekend', $profile->availability)) checked @endif
                                    class="mr-2 h-4 w-4 text-blue-600 rounded focus:ring-blue-500">
                                    <label class="text-gray-700 text-sm">Weekend</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" name="availability[weekday]" value="true" 
                                    @if($profile && is_array($profile->availability) && in_array('weekday', $profile->availability)) checked @endif
                                    class="mr-2 h-4 w-4 text-blue-600 rounded focus:ring-blue-500">
                                    <label class="text-gray-700 text-sm">Weekday</label>
                                </div>
                            </div>
                        </div>

                        <!-- Health (Kiri) -->
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Health Status</label>
                            <select name="health_status" class="w-full border rounded px-3 py-2 bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <option value="Healthy" {{ $profile->health_status == 'Healthy' ? 'selected' : '' }}>Healthy & Fit</option>
                                <option value="Light" {{ $profile->health_status == 'Light' ? 'selected' : '' }}>Light Activities Only</option>
                                <option value="Limited" {{ $profile->health_status == 'Limited' ? 'selected' : '' }}>Limited Mobility</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- HEADER FORM: PERSONAL DETAILS -->
                <div class="mb-8 pb-6 border-b border-gray-100">
                    <h4 class="text-sm font-bold text-gray-500 uppercase mb-4 tracking-wide">Personal Details</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <!-- Location -->
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Current Location</label>
                            <input type="text" name="location" class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none" 
                            value="{{ $profile->location ?? '' }}" placeholder="e.g. Melaka">
                        </div>

                        <!-- Languages -->
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Languages Spoken</label>
                            <p class="text-xs text-gray-500 mb-1">e.g. Malay, English</p>
                            <input type="text" name="languages" class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            value="{{ $profile && is_array($profile->languages) ? implode(', ', $profile->languages) : '' }}"
                            placeholder="Malay, English, Arabic">
                        </div>

                    </div>
                </div>

                <!-- HEADER FORM: ADDITIONAL INFO -->
                <div class="mb-6">
                    <h4 class="text-sm font-bold text-gray-500 uppercase mb-4 tracking-wide">Additional Information</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <!-- Hobbies -->
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Hobbies</label>
                            <p class="text-xs text-gray-500 mb-1">e.g. Reading, Football</p>
                            <textarea name="hobbies" rows="2" class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            placeholder="Reading, Football...">{{ $profile && is_array($profile->hobbies) ? implode(', ', $profile->hobbies) : '' }}</textarea>
                        </div>

                        <!-- Interests -->
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Interests</label>
                            <p class="text-xs text-gray-500 mb-1">e.g. Community Service, Tech</p>
                            <textarea name="interests" rows="2" class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            placeholder="Community Service, Tech...">{{ $profile && is_array($profile->interests) ? implode(', ', $profile->interests) : '' }}</textarea>
                        </div>

                        <!-- Experience (Penuh lebar) -->
                        <div class="md:col-span-2">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Volunteer Experience</label>
                            <textarea name="experience" rows="3" class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            placeholder="Describe your past experience...">{{ $profile->experience ?? '' }}</textarea>
                        </div>

                        <!-- Long Term Availability (Penuh lebar) -->
                        <div class="md:col-span-2">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Long-Term Availability</label>
                            <textarea name="long_term_availability" rows="2" class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            placeholder="e.g. Available every weekend for 6 months">{{ $profile->long_term_availability ?? '' }}</textarea>
                        </div>

                    </div>
                </div>

                <!-- BUTTON -->
                <div class="mt-6">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded shadow transition duration-200">
                        Update Profile
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- SECTION 3: SECURITY / PASSWORD -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-gray-700 px-6 py-4">
            <h2 class="text-xl font-bold text-white">Security Settings</h2>
        </div>
        <div class="p-6">
            <form action="{{ route('profile.update.password') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Current Password</label>
                        <input type="password" name="current_password" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">New Password</label>
                        <input type="password" name="new_password" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Confirm New Password</label>
                        <input type="password" name="new_password_confirmation" class="w-full border rounded px-3 py-2" required>
                    </div>
                </div>
                <div class="mt-6">
                    <button type="submit" class="bg-gray-700 hover:bg-gray-800 text-white font-bold py-2 px-6 rounded">
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

@endsection