<div id="events-table" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="bg-gradient-to-r from-purple-50 to-indigo-50 px-4 sm:px-6 py-4 border-b border-purple-200">
        <h3 class="font-bold text-gray-800 flex items-center gap-2">
            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            Events ({{ $monthName }} {{ $year }})
        </h3>
    </div>
    <div class="hidden md:block overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                        <a href="{{ request()->fullUrlWithQuery(['sort_event' => 'title', 'direction_event' => $sortEvent === 'title' && $directionEvent === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-700">
                            Title
                            @if($sortEvent === 'title')
                                <svg class="w-4 h-4 {{ $directionEvent === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                            @endif
                        </a>
                    </th>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                        <a href="{{ request()->fullUrlWithQuery(['sort_event' => 'event_date', 'direction_event' => $sortEvent === 'event_date' && $directionEvent === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-700">
                            Date
                            @if($sortEvent === 'event_date')
                                <svg class="w-4 h-4 {{ $directionEvent === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                            @endif
                        </a>
                    </th>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                        <a href="{{ request()->fullUrlWithQuery(['sort_event' => 'event_location', 'direction_event' => $sortEvent === 'event_location' && $directionEvent === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-700">
                            Location
                            @if($sortEvent === 'event_location')
                                <svg class="w-4 h-4 {{ $directionEvent === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                            @endif
                        </a>
                    </th>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                        <a href="{{ request()->fullUrlWithQuery(['sort_event' => 'status', 'direction_event' => $sortEvent === 'status' && $directionEvent === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-700">
                            Status
                            @if($sortEvent === 'status')
                                <svg class="w-4 h-4 {{ $directionEvent === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                            @endif
                        </a>
                    </th>
                    <th class="px-4 sm:px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase">Volunteers</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 text-sm">
                @forelse($events as $event)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 sm:px-6 py-3 text-gray-800 font-medium">{{ $event->title }}</td>
                        <td class="px-4 sm:px-6 py-3 text-gray-800">{{ $event->event_date->format('d M Y, h:i A') }}</td>
                        <td class="px-4 sm:px-6 py-3 text-gray-800">{{ $event->event_location ?? '-' }}</td>
                        <td class="px-4 sm:px-6 py-3">
                            @if($event->status === 'open')
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Open</span>
                            @elseif($event->status === 'closed')
                                <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs">Closed</span>
                            @elseif($event->status === 'cancelled')
                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">Cancelled</span>
                            @else
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">{{ ucfirst($event->status) }}</span>
                            @endif
                        </td>
                        <td class="px-4 sm:px-6 py-3 text-center">
                            <span class="font-semibold text-purple-700">{{ $event->volunteers_count }}</span> / {{ $event->max_volunteers }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-6 text-gray-400">No events for this period</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="md:hidden divide-y divide-gray-200">
        @forelse($events as $event)
            <div class="p-4 space-y-2">
                <div class="flex justify-between items-start">
                    <span class="text-sm font-medium text-gray-900">{{ $event->title }}</span>
                    <span class="text-xs">
                        @if($event->status === 'open')
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Open</span>
                        @elseif($event->status === 'closed')
                            <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs">Closed</span>
                        @elseif($event->status === 'cancelled')
                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">Cancelled</span>
                        @else
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">{{ ucfirst($event->status) }}</span>
                        @endif
                    </span>
                </div>
                <div class="flex justify-between text-sm text-gray-600">
                    <span>{{ $event->event_date->format('d M Y, h:i A') }}</span>
                    <span>{{ $event->event_location ?? '-' }}</span>
                </div>
                <div class="text-xs text-gray-500">
                    Capacity: <span class="font-semibold text-purple-700">{{ $event->volunteers_count }}</span> / {{ $event->max_volunteers }}
                </div>
            </div>
        @empty
            <div class="p-4 text-center text-gray-400 text-sm">No events for this period</div>
        @endforelse
    </div>
    @if($events->hasPages())
        <div class="px-4 sm:px-6 py-3 bg-gray-50 border-t border-gray-100">
            {{ $events->appends(request()->except('page'))->links() }}
        </div>
    @endif
</div>
