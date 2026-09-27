<x-dashboard-layout>
    <x-slot name="header">
        <h1 class="text-base font-bold text-gray-900">Notifications</h1>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-5">

        {{-- Flash --}}
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3500)" x-transition
                 class="flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-700 text-sm shadow-sm">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        {{-- Header Card --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl bg-comsoc-50 text-comsoc-600 flex items-center justify-center shrink-0 border border-comsoc-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-900">Your Activity & Alerts</h2>
                    <p class="text-xs text-gray-500">
                        You have <span class="font-bold text-comsoc-700">{{ $unreadCount }}</span> unread notification{{ $unreadCount === 1 ? '' : 's' }}
                    </p>
                </div>
            </div>

            @if($unreadCount > 0)
                <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-comsoc-50 hover:bg-comsoc-100 text-comsoc-700 text-xs font-bold rounded-xl border border-comsoc-200 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Mark all as read</span>
                    </button>
                </form>
            @endif
        </div>

        {{-- Notifications List --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm divide-y divide-gray-100 overflow-hidden">
            @forelse ($notifications as $notification)
                @php
                    $data   = $notification->data;
                    $isRead = !is_null($notification->read_at);
                    $type   = $data['type'] ?? 'info';
                @endphp
                <div class="p-4 sm:p-5 transition-colors flex items-start justify-between gap-4 {{ $isRead ? 'bg-white hover:bg-gray-50/50' : 'bg-emerald-50/30 hover:bg-emerald-50/50' }}">
                    <div class="flex items-start gap-3.5 min-w-0 flex-1">
                        {{-- Icon badge --}}
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 mt-0.5
                            {{ $type === 'success' ? 'bg-emerald-100 text-emerald-700' :
                               ($type === 'warning' ? 'bg-amber-100 text-amber-700' :
                               ($type === 'danger'  ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700')) }}">
                            @if($type === 'success')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            @elseif($type === 'warning')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            @elseif($type === 'danger')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            @else
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            @endif
                        </div>

                        {{-- Body --}}
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h4 class="text-sm font-bold text-gray-900">{{ $data['title'] ?? 'Notification' }}</h4>
                                @if(!$isRead)
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-600 mt-1 leading-relaxed">{{ $data['message'] ?? '' }}</p>
                            
                            <div class="flex items-center gap-3 mt-2.5 flex-wrap">
                                <span class="text-[11px] text-gray-400 font-medium">
                                    {{ $notification->created_at->diffForHumans() }}
                                </span>

                                @if(!empty($data['action_url']))
                                    <a href="{{ route('notifications.read', ['id' => $notification->id, 'redirect' => $data['action_url']]) }}"
                                       class="inline-flex items-center gap-1 text-xs font-bold text-comsoc-600 hover:text-comsoc-800 transition-colors">
                                        <span>View Details</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-1.5 shrink-0">
                        @if(!$isRead)
                            <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                @csrf
                                <button type="submit" title="Mark as read"
                                        class="p-1.5 rounded-lg text-gray-400 hover:text-comsoc-700 hover:bg-gray-100 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </button>
                            </form>
                        @endif

                        <form method="POST" action="{{ route('notifications.destroy', $notification->id) }}">
                            @csrf @method('DELETE')
                            <button type="submit" title="Delete notification"
                                    class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <div class="w-14 h-14 rounded-2xl bg-gray-100 text-gray-400 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <p class="text-sm font-bold text-gray-800">No notifications yet</p>
                    <p class="text-xs text-gray-400 mt-1">We'll alert you when there are updates on your events, tasks, or certificates.</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($notifications->hasPages())
            <div class="pt-2">
                {{ $notifications->links() }}
            </div>
        @endif

    </div>
</x-dashboard-layout>
