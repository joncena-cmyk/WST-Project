<x-dashboard-layout>
    <x-slot name="header">
        <h1 class="text-lg font-semibold text-gray-800">My Registered Events</h1>
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-5">

        @foreach(['success','error'] as $type)
            @if(session($type))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3500)" x-transition
                     class="p-4 rounded-xl text-sm border
                            {{ $type === 'success' ? 'bg-comsoc-50 border-comsoc-200 text-comsoc-700' : 'bg-red-50 border-red-200 text-red-700' }}">
                    {{ session($type) }}
                </div>
            @endif
        @endforeach

        <div class="flex items-center justify-between">
            <p class="text-sm text-gray-500">{{ $registrations->count() }} active registration{{ $registrations->count() !== 1 ? 's' : '' }}</p>
            <a href="{{ route('events.index') }}" class="text-sm font-medium text-comsoc-600 hover:text-comsoc-800 transition-colors">
                ← Browse Events
            </a>
        </div>

        @forelse($registrations as $reg)
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="flex flex-col sm:flex-row">
                    <div class="sm:w-40 shrink-0 bg-comsoc-50">
                        @if($reg->event->getImageUrl())
                            <img src="{{ $reg->event->getImageUrl() }}" class="w-full h-32 sm:h-full object-cover"/>
                        @else
                            <div class="w-full h-32 sm:h-full flex items-center justify-center bg-gradient-to-br from-comsoc-100 to-comsoc-200">
                                <svg class="w-10 h-10 text-comsoc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 p-5 flex flex-col justify-between">
                        <div>
                            <div class="flex items-start justify-between gap-3 mb-1">
                                <h3 class="font-bold text-gray-900">{{ $reg->event->title }}</h3>
                                <span class="inline-flex shrink-0 px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $reg->event->getStatusColor() }}">
                                    {{ $reg->event->getStatusLabel() }}
                                </span>
                            </div>
                            <div class="flex flex-wrap items-center gap-4 text-xs text-gray-500 mt-2">
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $reg->event->event_date->format('M d, Y') }} · {{ \Carbon\Carbon::parse($reg->event->start_time)->format('g:i A') }}
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    {{ $reg->event->venue }}
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between mt-4 pt-3 border-t border-gray-100">
                            <div class="text-xs text-gray-400">
                                Registered {{ $reg->created_at->diffForHumans() }}
                            </div>
                            <div class="flex items-center gap-3">
                                {{-- QR Pass button --}}
                                <a href="{{ route('attendance.pass', $reg->event) }}"
                                   class="inline-flex items-center gap-1 text-xs font-semibold text-gold-600 hover:text-gold-700 transition-colors bg-yellow-50 border border-yellow-200 px-2.5 py-1 rounded-lg">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                    </svg>
                                    QR Pass
                                </a>
                                <a href="{{ route('events.show', $reg->event) }}"
                                   class="text-xs font-semibold text-comsoc-600 hover:text-comsoc-800 transition-colors">
                                    View Event →
                                </a>
                                <form method="POST" action="{{ route('events.registration.cancel', $reg) }}">
                                    @csrf
                                    <button type="submit"
                                            onclick="return confirm('Cancel your registration for this event?')"
                                            class="text-xs text-red-500 hover:text-red-700 font-medium transition-colors">
                                        Cancel
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white border border-gray-200 rounded-xl p-12 text-center shadow-sm">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-gray-500 font-medium">No active registrations.</p>
                <a href="{{ route('events.index') }}" class="mt-2 inline-block text-sm text-comsoc-600 hover:text-comsoc-800 font-medium transition-colors">
                    Browse upcoming events →
                </a>
            </div>
        @endforelse
    </div>
</x-dashboard-layout>
