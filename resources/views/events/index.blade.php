<x-dashboard-layout>
    <x-slot name="header">
        <h1 class="text-lg font-semibold text-gray-800">Events</h1>
    </x-slot>

    <div class="space-y-5">

        {{-- Flash --}}
        @foreach(['success','error','info'] as $type)
            @if(session($type))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3500)" x-transition
                     class="flex items-center gap-3 p-4 rounded-xl text-sm border
                            {{ $type === 'success' ? 'bg-comsoc-50 border-comsoc-200 text-comsoc-700' : ($type === 'error' ? 'bg-red-50 border-red-200 text-red-700' : 'bg-blue-50 border-blue-200 text-blue-700') }}">
                    {{ session($type) }}
                </div>
            @endif
        @endforeach

        {{-- Top bar --}}
        <div class="flex items-center justify-between">
            <p class="text-sm text-gray-500">{{ $events->total() }} upcoming event{{ $events->total() !== 1 ? 's' : '' }}</p>
            <div class="flex items-center gap-2">
                <a href="{{ route('events.my-events') }}"
                   class="text-sm font-medium text-comsoc-600 hover:text-comsoc-800 transition-colors">
                    My Registrations →
                </a>
                @if(Auth::user()->isAdviser() || Auth::user()->isAdmin())
                    @php
                        $pendingCount = \App\Models\Event::whereIn('status', [\App\Models\Event::STATUS_SUBMITTED, \App\Models\Event::STATUS_UNDER_REVIEW])->count();
                    @endphp
                    <a href="{{ route('events.approval.index') }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        Event Approvals
                        @if($pendingCount > 0)
                            <span class="px-1.5 py-0.5 text-xs bg-white text-amber-800 font-black rounded-full">
                                {{ $pendingCount }}
                            </span>
                        @endif
                    </a>
                @endif
                @can('create', App\Models\Event::class)
                    <a href="{{ route('events.manage') }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2 bg-comsoc-600 text-white text-sm font-semibold rounded-lg hover:bg-comsoc-700 transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Manage Events
                    </a>
                @endcan
            </div>
        </div>

        {{-- Grid --}}
        @forelse ($events as $event)
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                <div class="flex flex-col sm:flex-row">
                    {{-- Image --}}
                    <div class="sm:w-48 sm:shrink-0 bg-comsoc-50">
                        @if($event->getImageUrl())
                            <img src="{{ $event->getImageUrl() }}"
                                 alt="{{ $event->title }}"
                                 class="w-full h-40 sm:h-full object-cover"/>
                        @else
                            <div class="w-full h-40 sm:h-full flex items-center justify-center bg-gradient-to-br from-comsoc-100 to-comsoc-200">
                                <svg class="w-12 h-12 text-comsoc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                    </div>

                    {{-- Details --}}
                    <div class="flex-1 p-5 flex flex-col justify-between">
                        <div>
                            <div class="flex items-start justify-between gap-3 mb-2">
                                <h3 class="font-bold text-gray-900 text-base leading-snug">{{ $event->title }}</h3>
                                <span class="inline-flex shrink-0 px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $event->getStatusColor() }}">
                                    {{ $event->getStatusLabel() }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-500 line-clamp-2 leading-relaxed">{{ $event->description }}</p>
                        </div>

                        <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                            <div class="flex flex-wrap gap-4 text-xs text-gray-500">
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $event->event_date->format('M d, Y') }} · {{ \Carbon\Carbon::parse($event->start_time)->format('g:i A') }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    {{ $event->venue }}
                                </span>
                                @if($event->participant_limit)
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        {{ $event->registeredCount() }} / {{ $event->participant_limit }} slots
                                    </span>
                                @endif
                            </div>
                            <a href="{{ route('events.show', $event) }}"
                               class="inline-flex items-center gap-1.5 px-4 py-2 bg-comsoc-600 text-white text-xs font-semibold rounded-lg hover:bg-comsoc-700 transition-colors">
                                View Details
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white border border-gray-200 rounded-xl p-12 text-center shadow-sm">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-gray-500 font-medium">No upcoming events.</p>
                <p class="text-gray-400 text-sm mt-1">Check back soon for new events from COMSOC.</p>
            </div>
        @endforelse

        <div>{{ $events->links() }}</div>
    </div>
</x-dashboard-layout>
