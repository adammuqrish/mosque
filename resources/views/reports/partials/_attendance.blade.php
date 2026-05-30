<div id="attendance-table" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="bg-gradient-to-r from-yellow-50 to-amber-50 px-4 sm:px-6 py-4 border-b border-yellow-200">
        <h3 class="font-bold text-gray-800 flex items-center gap-2">
            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            Volunteer Attendance ({{ $monthName }} {{ $year }})
        </h3>
    </div>
    <div class="hidden md:block overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                        <a href="{{ request()->fullUrlWithQuery(['sort_attendance' => 'event_title', 'direction_attendance' => $sortAttendance === 'event_title' && $directionAttendance === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-700">
                            Event
                            @if($sortAttendance === 'event_title')
                                <svg class="w-4 h-4 {{ $directionAttendance === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                            @endif
                        </a>
                    </th>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                        <a href="{{ request()->fullUrlWithQuery(['sort_attendance' => 'event_date', 'direction_attendance' => $sortAttendance === 'event_date' && $directionAttendance === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-700">
                            Event Date
                            @if($sortAttendance === 'event_date')
                                <svg class="w-4 h-4 {{ $directionAttendance === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                            @endif
                        </a>
                    </th>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                        <a href="{{ request()->fullUrlWithQuery(['sort_attendance' => 'volunteer_name', 'direction_attendance' => $sortAttendance === 'volunteer_name' && $directionAttendance === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-700">
                            Volunteer
                            @if($sortAttendance === 'volunteer_name')
                                <svg class="w-4 h-4 {{ $directionAttendance === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                            @endif
                        </a>
                    </th>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                        <a href="{{ request()->fullUrlWithQuery(['sort_attendance' => 'email', 'direction_attendance' => $sortAttendance === 'email' && $directionAttendance === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-700">
                            Email
                            @if($sortAttendance === 'email')
                                <svg class="w-4 h-4 {{ $directionAttendance === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                            @endif
                        </a>
                    </th>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                        <a href="{{ request()->fullUrlWithQuery(['sort_attendance' => 'attendance_status', 'direction_attendance' => $sortAttendance === 'attendance_status' && $directionAttendance === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-700">
                            Status
                            @if($sortAttendance === 'attendance_status')
                                <svg class="w-4 h-4 {{ $directionAttendance === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                            @endif
                        </a>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 text-sm">
                @forelse($attendance as $record)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 sm:px-6 py-3 text-gray-800 font-medium">{{ $record->event_title }}</td>
                        <td class="px-4 sm:px-6 py-3 text-gray-800">{{ \Carbon\Carbon::parse($record->event_date)->format('d M Y') }}</td>
                        <td class="px-4 sm:px-6 py-3 text-gray-800">{{ $record->volunteer_name }}</td>
                        <td class="px-4 sm:px-6 py-3 text-gray-600">{{ $record->email }}</td>
                        <td class="px-4 sm:px-6 py-3">
                            @if($record->attendance_status === 'confirmed')
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">Confirmed</span>
                            @elseif($record->attendance_status === 'completed')
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Completed</span>
                            @elseif($record->attendance_status === 'absent')
                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">Absent</span>
                            @elseif($record->attendance_status === 'pending_review')
                                <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">Pending Review</span>
                            @else
                                <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs">{{ ucfirst(str_replace('_', ' ', $record->attendance_status)) }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-6 text-gray-400">No attendance records for this period</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="md:hidden divide-y divide-gray-200">
        @forelse($attendance as $record)
            <div class="p-4 space-y-2">
                <div class="flex justify-between items-start">
                    <span class="text-sm font-medium text-gray-900">{{ $record->event_title }}</span>
                    <span class="text-sm text-gray-600">{{ $record->volunteer_name }}</span>
                </div>
                <div class="flex justify-between text-sm text-gray-600">
                    <span>{{ $record->email }}</span>
                    <span>
                        @if($record->attendance_status === 'confirmed')
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">Confirmed</span>
                        @elseif($record->attendance_status === 'completed')
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Completed</span>
                        @elseif($record->attendance_status === 'absent')
                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">Absent</span>
                        @elseif($record->attendance_status === 'pending_review')
                            <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">Pending Review</span>
                        @else
                            <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs">{{ ucfirst(str_replace('_', ' ', $record->attendance_status)) }}</span>
                        @endif
                    </span>
                </div>
                <div class="text-xs text-gray-500">Joined: {{ \Carbon\Carbon::parse($record->event_date)->format('d M Y') }}</div>
            </div>
        @empty
            <div class="p-4 text-center text-gray-400 text-sm">No attendance records for this period</div>
        @endforelse
    </div>
    @if($attendance->hasPages())
        <div class="px-4 sm:px-6 py-3 bg-gray-50 border-t border-gray-100">
            {{ $attendance->appends(request()->except('page'))->links() }}
        </div>
    @endif
</div>
