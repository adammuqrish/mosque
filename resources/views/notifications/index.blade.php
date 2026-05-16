@extends('layouts.app')

@section('back', '/dashboard')

@section('title', __('islamic.navigation.notifications'))

@section('content')

    <!-- STEP 1: Page Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
            <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
            </svg>
            Notifications
        </h1>
        <p class="text-gray-600 mt-1">Stay updated with withdrawal requests and approvals.</p>
    </div>

    <!-- STEP 2: Notifications Card -->
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Card Header -->
            <div class="px-6 py-4 border-b border-t-2 border-t-[#C5A059] border-gray-100 bg-gray-50/50 flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    All Notifications
                </h2>
                @if($notifications->count() > 0)
                    <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-sm text-blue-600 hover:text-blue-800">
                            Mark all as read
                        </button>
                    </form>
                @endif
            </div>

            <div class="divide-y">
                @forelse($notifications as $notification)
                    <div class="p-4 hover:bg-gray-50 transition {{ is_null($notification->read_at) ? 'bg-blue-50' : '' }}">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 mt-1">
                                @switch($notification->data['action'] ?? 'info')
                                    @case('created')
                                        <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                        </div>
                                        @break
                                    @case('approved')
                                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                        @break
                                    @case('rejected')
                                        <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </div>
                                        @break
                                    @default
                                        <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                            </svg>
                                        </div>
                                @endswitch
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-800">{{ $notification->data['title'] ?? 'Notification' }}</p>
                                <p class="text-sm text-gray-600 mt-1">{!! strip_tags($notification->data['message'] ?? '', '<strong><em><span><br>') !!}</p>
                                <div class="flex items-center gap-4 mt-2">
                                    <span class="text-xs text-gray-400">{{ $notification->created_at->format('d M Y, h:i A') }}</span>
                                    @if(is_null($notification->read_at))
                                        <span class="text-xs text-blue-600 font-medium">New</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                @if(is_null($notification->read_at))
                                    <form action="{{ route('notifications.mark-read', $notification->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-xs text-gray-500 hover:text-gray-700">
                                            Mark as read
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <p class="text-gray-500 font-medium">No notifications yet</p>
                        <p class="text-sm text-gray-400 mt-1">You'll see notifications here when something happens</p>
                    </div>
                @endforelse
            </div>

            @if($notifications->hasPages())
                <div class="px-6 py-4 border-t bg-gray-50">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection


