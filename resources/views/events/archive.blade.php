<x-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-base font-bold text-gray-900">Event Archive &amp; History</h1>
            <div class="flex items-center gap-2">
                <a href="{{ route('events.index') }}"
                   class="text-xs font-bold text-comsoc-600 hover:text-comsoc-800 transition flex items-center gap-1">
                    ← Back to Upcoming Events
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">

        {{-- Flash messages --}}
        @foreach(['success','error','info'] as $type)
            @if(session($type))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3500)" x-transition
                     class="flex items-center gap-3 p-4 rounded-xl text-sm border
                            {{ $type === 'success' ? 'bg-comsoc-50 border-comsoc-200 text-comsoc-700' : ($type === 'error' ? 'bg-red-50 border-red-200 text-red-700' : 'bg-blue-50 border-blue-200 text-blue-700') }}">
                    {{ session($type) }}
                </div>
            @endif
        @endforeach

        {{-- ===== TAB NAVIGATION & ACTION BAR ===== --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-200 pb-4">
            {{-- Tabs --}}
            <div class="flex items-center gap-2">
                <a href="{{ route('events.index') }}"
                   class="px-4 py-2 text-xs font-bold rounded-xl text-gray-600 hover:text-comsoc-800 hover:bg-gray-100 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Upcoming Events
                    <span class="px-2 py-0.5 text-[10px] font-black rounded-full bg-gray-200 text-gray-700">
                        {{ $upcomingCount }}
                    </span>
                </a>

                <a href="{{ route('events.archive') }}"
                   class="px-4 py-2 text-xs font-black rounded-xl bg-comsoc-600 text-white shadow-xs transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                    </svg>
                    Event Archive
                    <span class="px-2 py-0.5 text-[10px] font-black rounded-full bg-gold-400 text-comsoc-950">
                        {{ $archivedCount }}
                    </span>
                </a>
            </div>

            {{-- Right Actions --}}
            <div class="flex items-center gap-2">
                <a href="{{ route('events.my-events') }}"
                   class="text-xs font-bold text-comsoc-600 hover:text-comsoc-800 transition px-3 py-1.5 rounded-lg border border-comsoc-200 hover:bg-comsoc-50">
                    My Registrations →
                </a>
                @can('create', App\Models\Event::class)
                    <a href="{{ route('events.manage') }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-comsoc-700 hover:bg-comsoc-800 text-white text-xs font-bold rounded-lg transition shadow-xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Manage Events
                    </a>
                @endcan
            </div>
        </div>

        {{-- ===== ARCHIVE METRICS STRIP ===== --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
            <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-xs">
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Concluded Events</p>
                <p class="text-2xl font-black text-comsoc-800 mt-1">{{ $totalArchived }}</p>
            </div>

            <div class="bg-white border border-emerald-100 rounded-2xl p-4 shadow-xs bg-emerald-50/40">
                <p class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider">Recorded Attendees</p>
                <p class="text-2xl font-black text-emerald-800 mt-1">{{ $totalAttendees }}</p>
            </div>

            <div class="bg-white border border-blue-100 rounded-2xl p-4 shadow-xs bg-blue-50/40">
                <p class="text-[11px] font-bold text-blue-600 uppercase tracking-wider">Certificates Issued</p>
                <p class="text-2xl font-black text-blue-800 mt-1">{{ $totalCertificates }}</p>
            </div>

            <div class="bg-white border border-purple-100 rounded-2xl p-4 shadow-xs bg-purple-50/40">
                <p class="text-[11px] font-bold text-purple-600 uppercase tracking-wider">Historical Reach</p>
                <p class="text-2xl font-black text-purple-800 mt-1">100%</p>
            </div>
        </div>

        {{-- ===== SEARCH & FILTER BAR ===== --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-xs">
            <form method="GET" action="{{ route('events.archive') }}" class="flex flex-col sm:flex-row items-center gap-3">
                <div class="relative flex-1 w-full">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search archive by event title, venue, or description..."
                           class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-comsoc-500 focus:border-comsoc-500"/>
                </div>

                @if($years->isNotEmpty())
                    <select name="year"
                            class="w-full sm:w-44 py-2 px-3 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-comsoc-500">
                        <option value="">All Years</option>
                        @foreach($years as $yr)
                            <option value="{{ $yr }}" {{ request('year') == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                        @endforeach
                    </select>
                @endif

                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <button type="submit"
                            class="flex-1 sm:flex-none px-4 py-2 bg-comsoc-600 hover:bg-comsoc-700 text-white font-bold text-xs rounded-xl transition shadow-xs">
                        Filter Archive
                    </button>
                    @if(request()->hasAny(['search', 'year']))
                        <a href="{{ route('events.archive') }}"
                           class="px-3 py-2 text-xs font-bold text-gray-500 hover:text-gray-800 transition">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- ===== ARCHIVE EVENTS GRID ===== --}}
        <div class="space-y-4">
            @forelse ($events as $event)
                <div class="bg-white border border-gray-200 rounded-2xl shadow-xs overflow-hidden hover:shadow-md transition-shadow">
                    <div class="flex flex-col md:flex-row">
                        {{-- Image / Visual --}}
                        <div class="md:w-56 md:shrink-0 bg-slate-900 relative">
                            @if($event->getImageUrl())
                                <img src="{{ $event->getImageUrl() }}"
                                     alt="{{ $event->title }}"
                                     class="w-full h-44 md:h-full object-cover opacity-90"/>
                            @else
                                <div class="w-full h-44 md:h-full flex flex-col items-center justify-center bg-gradient-to-br from-slate-800 to-slate-950 text-white p-4">
                                    <svg class="w-10 h-10 text-slate-500 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                    </svg>
                                    <span class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">Archived Record</span>
                                </div>
                            @endif
                            <div class="absolute top-3 left-3">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider {{ $event->getStatusColor() }}">
                                    {{ $event->getStatusLabel() }}
                                </span>
                            </div>
                        </div>

                        {{-- Details & Accomplishment Strip --}}
                        <div class="flex-1 p-6 flex flex-col justify-between space-y-4">
                            <div>
                                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-2 mb-2">
                                    <div>
                                        <h3 class="text-base font-black text-gray-900">{{ $event->title }}</h3>
                                        <p class="text-xs text-gray-500 mt-0.5 flex items-center gap-3">
                                            <span class="flex items-center gap-1 font-semibold text-gray-700">
                                                <svg class="w-3.5 h-3.5 text-comsoc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                Held on {{ $event->event_date->format('F d, Y') }}
                                            </span>
                                            <span>·</span>
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                                {{ $event->venue }}
                                            </span>
                                        </p>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <span class="text-[11px] font-bold text-gray-400 uppercase">Organized by</span>
                                        <p class="text-xs font-bold text-gray-700">{{ $event->creator ? $event->creator->name : 'COMSOC' }}</p>
                                    </div>
                                </div>

                                <p class="text-xs text-gray-600 line-clamp-2 leading-relaxed mt-2">{{ $event->description }}</p>
                            </div>

                            {{-- Quantitative Metrics Ribbon --}}
                            <div class="grid grid-cols-3 gap-3 p-3 bg-gray-50 border border-gray-100 rounded-xl text-center">
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase">Turnout Rate</p>
                                    <p class="text-sm font-black text-comsoc-700 mt-0.5">{{ $event->turnoutPercentage() }}%</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase">Verified Attendance</p>
                                    <p class="text-sm font-black text-gray-900 mt-0.5">{{ $event->attendance_count }} attendees</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase">Certificates Issued</p>
                                    <p class="text-sm font-black text-blue-700 mt-0.5">{{ $event->certificates_count }} issued</p>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="flex flex-wrap items-center justify-between gap-3 pt-2 border-t border-gray-100">
                                <div class="text-xs text-gray-400 italic">
                                    Official Archive Record #{{ str_pad($event->id, 4, '0', STR_PAD_LEFT) }}
                                </div>

                                <div class="flex items-center gap-2">
                                    @if(Auth::user()->isOfficerOrAbove())
                                        <a href="{{ route('certificates.manage', $event) }}"
                                           class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold rounded-lg border border-blue-200 transition">
                                            Manage Certificates
                                        </a>
                                        <a href="{{ route('analytics.export.attendance', $event) }}"
                                           class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-lg transition">
                                            Export Attendance CSV ↓
                                        </a>
                                    @endif
                                    <a href="{{ route('events.show', $event) }}"
                                       class="px-4 py-1.5 bg-comsoc-600 hover:bg-comsoc-700 text-white text-xs font-bold rounded-lg transition shadow-xs flex items-center gap-1">
                                        View Event Archive →
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white border border-gray-200 rounded-2xl p-12 text-center shadow-xs">
                    <div class="w-16 h-16 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900">No Archived Events Found</h3>
                    <p class="text-xs text-gray-500 mt-1 max-w-sm mx-auto">
                        @if(request()->hasAny(['search', 'year']))
                            No past events match your current filter criteria. Try resetting the filters.
                        @else
                            Past concluded events and their documentation will be automatically organized here once finished.
                        @endif
                    </p>
                    <div class="mt-4">
                        <a href="{{ route('events.index') }}" class="px-4 py-2 bg-comsoc-600 text-white text-xs font-bold rounded-xl transition shadow-xs">
                            View Upcoming Events
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($events->hasPages())
            <div class="pt-4">
                {{ $events->links() }}
            </div>
        @endif

    </div>
</x-dashboard-layout>
