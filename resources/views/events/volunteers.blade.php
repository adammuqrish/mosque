@extends('layouts.app')

@section('back', '/events/manage')

@section('title', 'Event Volunteers')

@section('content')

    <!-- STEP 4: Event Info Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden p-4 sm:p-6 mb-6">
        <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">{{ $event->title }}</h1>
                <div class="flex items-center gap-4 mt-2 text-sm text-gray-600">
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        {{ $event->event_date->format('d M Y, h:i A') }}
                    </span>
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        {{ $event->event_location }}
                    </span>
                </div>
            </div>
            
            <!-- Status Badge -->
            <div>
                @if($event->status === 'open')
                    <span class="inline-flex items-center gap-1 bg-green-100 text-green-800 text-sm px-3 py-1 rounded-full font-medium">
                        <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                        Open
                    </span>
                @elseif($event->status === 'closed')
                    <span class="inline-flex items-center gap-1 bg-gray-100 text-gray-800 text-sm px-3 py-1 rounded-full font-medium">
                        <span class="w-2 h-2 bg-gray-500 rounded-full"></span>
                        Closed
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 bg-red-100 text-red-800 text-sm px-3 py-1 rounded-full font-medium">
                        <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                        Cancelled
                    </span>
                @endif
            </div>
        </div>

        <!-- Volunteer Stats -->
        <div class="mt-6 grid grid-cols-2 md:grid-cols-5 gap-4">
            <div class="bg-gray-50 rounded-lg p-4 text-center">
                <p class="text-3xl font-bold text-gray-800">{{ $volunteers->count() }}</p>
                <p class="text-sm text-gray-600">Total</p>
            </div>
            <div class="bg-blue-50 rounded-lg p-4 text-center">
                <p class="text-3xl font-bold text-blue-600">{{ $event->confirmedCount }}</p>
                <p class="text-sm text-gray-600">Confirmed</p>
            </div>
            <div class="bg-yellow-50 rounded-lg p-4 text-center">
                <p class="text-3xl font-bold text-yellow-600">{{ $event->pendingReviewCount }}</p>
                <p class="text-sm text-gray-600">Pending Review</p>
            </div>
            <div class="bg-green-50 rounded-lg p-4 text-center">
                <p class="text-3xl font-bold text-green-600">{{ $event->completedCount }}</p>
                <p class="text-sm text-gray-600">Completed</p>
            </div>
            <div class="bg-red-50 rounded-lg p-4 text-center">
                <p class="text-3xl font-bold text-red-600">{{ $event->absentCount }}</p>
                <p class="text-sm text-gray-600">Absent</p>
            </div>
        </div>

        <!-- Bulk Actions for Past Events -->
        @if($event->needsReview() && $event->hasReviewableAttendance())
            <div class="mt-4 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h3 class="font-semibold text-yellow-800">Attendance Review Needed</h3>
                        <p class="text-sm text-yellow-700">
                            @if($event->pendingReviewCount > 0)
                                {{ $event->pendingReviewCount }} volunteer(s) pending review
                            @else
                                {{ $event->confirmedCount }} confirmed volunteer(s) need attendance review
                            @endif
                        </p>
                    </div>
                    <div class="flex gap-2">
                        <form action="{{ route('events.attendance.bulk-approve', $event->id) }}" method="POST">
                            @csrf
                            <button type="button" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition" onclick="confirmBulkApprove()">
                                Approve All
                            </button>
                        </form>
                        <button type="button" onclick="openBulkAbsentModal()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                            Mark All Absent
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- STEP 6: Volunteers List Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Card Header -->
        <div class="px-4 sm:px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
            <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Volunteer List
            </h2>
            <span class="bg-purple-100 text-purple-800 text-xs px-2.5 py-0.5 rounded-full font-medium">{{ $volunteers->count() }} volunteers</span>
        </div>
        
        <div class="p-6">
            @if($volunteers->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($volunteers as $volunteer)
                        <!-- STEP 7: Volunteer Card -->
                        <div class="border border-gray-200 rounded-xl p-4 hover:shadow-md transition bg-white">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center gap-3">
                                    @if($volunteer->avatar_url)
                                        <img src="{{ $volunteer->avatar_url }}" alt="{{ $volunteer->name }}" class="w-10 h-10 rounded-full object-cover">
                                    @else
                                        <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center">
                                            <span class="text-emerald-700 font-bold">{{ $volunteer->initials }}</span>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-semibold text-gray-800">{{ $volunteer->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $volunteer->email }}</p>
                                    </div>
                                </div>
                                
                                <!-- Remove Volunteer Button -->
                                <form action="{{ route('events.volunteers.remove', ['eventId' => $event->id, 'userId' => $volunteer->id]) }}" method="POST"
                                    onsubmit="event.preventDefault(); confirmRemoveVolunteer('{{ route('events.volunteers.remove', ['eventId' => $event->id, 'userId' => $volunteer->id]) }}', '{{ $volunteer->name }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 transition p-1" title="Remove volunteer">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                            
                            <div class="mt-3 pt-3 border-t">
                                <div class="flex items-center justify-between text-xs text-gray-500">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                        </svg>
                                        {{ $volunteer->phone }}
                                    </span>
                                    <span class="px-2 py-0.5 bg-gray-100 rounded text-gray-600">
                                        {{ $volunteer->pivot->joined_at ? \Carbon\Carbon::parse($volunteer->pivot->joined_at)->diffForHumans() : 'Unknown' }}
                                    </span>
                                </div>
                                
                                <!-- Role Badge -->
                                <div class="mt-2 flex items-center gap-2">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium
                                        @if($volunteer->role === 'admin') bg-red-100 text-red-700
                                        @elseif($volunteer->role === 'treasurer') bg-yellow-100 text-yellow-700
                                        @else bg-blue-100 text-blue-700
                                        @endif">
                                        {{ ucfirst($volunteer->role) }}
                                    </span>
                                </div>

                                <!-- Attendance Status -->
                                <div class="mt-3 pt-2 border-t border-gray-100">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-gray-600">Attendance:</span>
                                        @php
                                            $statusConfig = [
                                                'confirmed' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'label' => 'Confirmed', 'icon' => '✓'],
                                                'pending_review' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'label' => 'Pending Review', 'icon' => '⏳'],
                                                'completed' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'label' => 'Completed', 'icon' => '✓'],
                                                'absent' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'label' => 'Absent', 'icon' => '✗'],
                                            ];
                                            $status = $volunteer->pivot->attendance_status ?? 'confirmed';
                                            $config = $statusConfig[$status] ?? $statusConfig['confirmed'];
                                        @endphp
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium {{ $config['bg'] }} {{ $config['text'] }}">
                                            {{ $config['icon'] }} {{ $config['label'] }}
                                        </span>
                                    </div>
                                    
                                    @if($status === 'absent' && $volunteer->pivot->absence_reason)
                                        <p class="text-xs text-red-600 mt-1 italic">{{ $volunteer->pivot->absence_reason }}</p>
                                    @endif

                                    @if($event->needsReview())
                                        <!-- Attendance Actions -->
                                        <div class="mt-3 flex gap-2">
                                            @if($status !== 'completed')
                                                <form action="{{ route('events.attendance.update', ['eventId' => $event->id, 'userId' => $volunteer->id]) }}" method="POST" class="flex-1">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="attendance_status" value="completed">
                                                    <button type="submit" class="w-full bg-green-100 hover:bg-green-200 text-green-700 text-sm font-medium px-3 py-2.5 rounded-lg transition min-h-[44px]" title="Mark as Completed">
                                                        ✓ Complete
                                                    </button>
                                                </form>
                                            @endif
                                            @if($status !== 'absent')
                                                <button type="button" onclick="openAbsentModal({{ $volunteer->id }}, '{{ $volunteer->name }}')" class="flex-1 bg-red-100 hover:bg-red-200 text-red-700 text-sm font-medium px-3 py-2.5 rounded-lg transition min-h-[44px]" title="Mark as Absent">
                                                    ✗ Absent
                                                </button>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <p class="text-gray-500">No volunteers have joined this event yet.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- STEP 11: Absence Modal using Global Modal System -->
    <script>
        // Confirm bulk approve all volunteers
        function confirmBulkApprove() {
            const modal = document.getElementById('global-modal');
            const modalBody = document.getElementById('modal-body');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            modalBody.innerHTML = `
                <h3 class="text-xl font-bold text-gray-800 mb-2">Approve All Volunteers</h3>
                <p class="text-gray-600 mb-4">Are you sure you want to mark all pending volunteers as completed? This action cannot be undone.</p>
                <form action="{{ route('events.attendance.bulk-approve', $event->id) }}" method="POST">
                    <input type="hidden" name="_token" value="${csrfToken}">
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition font-medium">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition font-medium">
                            Yes, Approve All
                        </button>
                    </div>
                </form>
            `;
            
            modal.classList.remove('hidden');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }

        // Confirm remove volunteer
        function confirmRemoveVolunteer(actionUrl, volunteerName) {
            const modal = document.getElementById('global-modal');
            const modalBody = document.getElementById('modal-body');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            modalBody.innerHTML = `
                <h3 class="text-xl font-bold text-gray-800 mb-2">Remove Volunteer</h3>
                <p class="text-gray-600 mb-4">Are you sure you want to remove ${volunteerName} from this event?</p>
                <form action="${actionUrl}" method="POST">
                    <input type="hidden" name="_token" value="${csrfToken}">
                    <input type="hidden" name="_method" value="DELETE">
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition font-medium">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition font-medium">
                            Yes, Remove
                        </button>
                    </div>
                </form>
            `;
            
            modal.classList.remove('hidden');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }

        // Open absent modal for single volunteer
        function openAbsentModal(userId, userName) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const modal = document.getElementById('global-modal');
            const modalBody = document.getElementById('modal-body');
            const actionUrl = "{{ route('events.attendance.update', ['eventId' => $event->id, 'userId' => ':userId']) }}".replace(':userId', userId);
            
            modalBody.innerHTML = `
                <h3 class="text-lg font-bold text-gray-800 mb-4">Mark ${userName} as Absent</h3>
                <form id="modal-absent-form" method="POST" action="${actionUrl}">
                    <input type="hidden" name="_token" value="${csrfToken}">
                    <input type="hidden" name="_method" value="PATCH">
                    <input type="hidden" name="attendance_status" value="absent">
                    <input type="hidden" name="attendance_status" value="absent">
                    <div class="mb-4">
                        <label for="modal_absence_reason" class="block text-sm font-medium text-gray-700 mb-2">Reason (optional)</label>
                        <textarea name="absence_reason" id="modal_absence_reason" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent transition resize-none" placeholder="Enter reason for absence..."></textarea>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeModal()" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition min-h-[44px]">Cancel</button>
                        <button type="submit" class="px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition min-h-[44px]">Mark Absent</button>
                    </div>
                </form>
            `;
            
            modal.classList.remove('hidden');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }

        // Open bulk absent modal
        function openBulkAbsentModal() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const modal = document.getElementById('global-modal');
            const modalBody = document.getElementById('modal-body');
            
            modalBody.innerHTML = `
                <h3 class="text-lg font-bold text-gray-800 mb-4">Mark All as Absent</h3>
                <p class="text-gray-600 mb-4">Are you sure you want to mark all pending volunteers as absent?</p>
                <form id="modal-bulk-absent-form" method="POST" action="{{ route('events.attendance.bulk-absent', $event->id) }}">
                    <input type="hidden" name="_token" value="${csrfToken}">
                    <div class="mb-4">
                        <label for="modal_bulk_absence_reason" class="block text-sm font-medium text-gray-700 mb-2">Reason (optional)</label>
                        <textarea name="absence_reason" id="modal_bulk_absence_reason" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent transition resize-none" placeholder="Enter reason for absence..."></textarea>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeModal()" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition min-h-[44px]">Cancel</button>
                        <button type="submit" class="px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition min-h-[44px]">Mark All Absent</button>
                    </div>
                </form>
            `;
            
            modal.classList.remove('hidden');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }
    </script>

@endsection


