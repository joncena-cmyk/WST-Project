@php
    $unreadCount = Auth::user()->unreadNotifications()->count();
    $recentNotifications = Auth::user()->notifications()->take(6)->get();
@endphp

<div x-data="{
        open: false,
        unreadCount: {{ $unreadCount }},
        markAllRead() {
            fetch('{{ route('notifications.mark-all-read') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                }
            }).then(() => {
                this.unreadCount = 0;
            });
        }
     }"
     class="relative">

    {{-- Bell Icon Button --}}
    <button @click="open = !open"
            class="relative w-9 h-9 flex items-center justify-center text-gray-500 hover:text-comsoc-700 hover:bg-gray-100 rounded-xl transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>

        {{-- Unread Badge --}}
        <template x-if="unreadCount > 0">
            <span class="absolute top-1 right-1 flex h-4 min-w-[16px] px-1 items-center justify-center rounded-full bg-red-500 text-[10px] font-black text-white shadow-xs">
                <span x-text="unreadCount > 9 ? '9+' : unreadCount"></span>
            </span>
        </template>
    </button>

    {{-- Floating Dropdown Panel --}}
    <div x-show="open"
         x-cloak
         @click.outside="open = false"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute right-0 mt-2 w-80 sm:w-96 bg-white border border-gray-200 rounded-2xl shadow-xl z-50 overflow-hidden">

        {{-- Header --}}
        <div class="px-4 py-3 bg-gradient-to-r from-comsoc-900 to-comsoc-800 text-white flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold uppercase tracking-wider">Notifications</span>
                <template x-if="unreadCount > 0">
                    <span class="px-1.5 py-0.5 rounded-full bg-gold-400 text-comsoc-950 text-[10px] font-black" x-text="unreadCount"></span>
                </template>
            </div>
            <template x-if="unreadCount > 0">
                <button @click="markAllRead()"
                        class="text-[11px] font-semibold text-gold-400 hover:text-gold-300 hover:underline">
                    Mark all read
                </button>
            </template>
        </div>

        {{-- List --}}
        <div class="max-h-80 overflow-y-auto divide-y divide-gray-100">
            @forelse ($recentNotifications as $n)
                @php
                    $d = $n->data;
                    $isUnread = is_null($n->read_at);
                    $type = $d['type'] ?? 'info';
                @endphp
                <a href="{{ !empty($d['action_url']) ? route('notifications.read', ['id' => $n->id, 'redirect' => $d['action_url']]) : route('notifications.index') }}"
                   class="block p-3.5 transition-colors {{ $isUnread ? 'bg-emerald-50/40 hover:bg-emerald-50/70' : 'hover:bg-gray-50' }}">
                    <div class="flex items-start gap-3">
                        <div class="w-2 h-2 rounded-full mt-1.5 shrink-0 {{ $isUnread ? 'bg-emerald-500' : 'bg-transparent' }}"></div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-bold text-gray-900 leading-tight truncate">{{ $d['title'] ?? 'Notification' }}</p>
                            <p class="text-xs text-gray-600 mt-0.5 line-clamp-2 leading-snug">{{ $d['message'] ?? '' }}</p>
                            <p class="text-[10px] text-gray-400 mt-1 font-medium">{{ $n->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </a>
            @empty
                <div class="p-8 text-center">
                    <p class="text-xs font-bold text-gray-700">No notifications</p>
                    <p class="text-[11px] text-gray-400 mt-0.5">You're all caught up!</p>
                </div>
            @endforelse
        </div>

        {{-- Footer --}}
        <div class="p-2.5 bg-gray-50 border-t border-gray-100 text-center">
            <a href="{{ route('notifications.index') }}"
               class="text-xs font-bold text-comsoc-700 hover:text-comsoc-800 hover:underline inline-flex items-center gap-1">
                <span>View all notifications</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
    </div>
</div>
