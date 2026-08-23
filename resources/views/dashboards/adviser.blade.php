<x-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-base font-bold text-gray-900">Academic Adviser Console</h1>
            <a href="{{ route('analytics.index') }}" class="px-3 py-1.5 bg-comsoc-50 hover:bg-comsoc-100 text-comsoc-700 text-xs font-bold rounded-lg border border-comsoc-200 transition flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Organization Analytics →
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">

        {{-- ===== HERO WELCOME BANNER ===== --}}
        <div class="relative rounded-2xl overflow-hidden shadow-md" style="min-height:140px;">
            <div class="absolute inset-0">
                <img src="{{ asset('images/hero_logo.jpg') }}" alt="" class="w-full h-full object-cover"/>
            </div>
            <div class="absolute inset-0" style="background:linear-gradient(135deg,rgba(30,27,75,0.95) 0%,rgba(5,46,22,0.85) 100%)"></div>
            <div class="absolute top-0 left-0 right-0 h-0.5 bg-gradient-to-r from-gold-600 via-gold-400 to-gold-600"></div>

            <div class="relative z-10 flex items-center justify-between p-6 text-white">
                <div class="flex items-center gap-4">
                    <img src="{{ $user->getProfilePictureUrl() }}"
                         alt="{{ $user->name }}"
                         class="w-14 h-14 rounded-full object-cover border-2 border-gold-400 shadow-md"/>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-gold-400 text-comsoc-950 uppercase tracking-wide">
                                Academic Adviser
                            </span>
                            <span class="text-xs text-indigo-200">Governance &amp; Approvals</span>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-black mt-1">{{ $user->name }}</h2>
                    </div>
                </div>

                <div class="hidden sm:flex items-center gap-3">
                    <a href="{{ route('events.approval.index') }}"
                       class="px-4 py-2 bg-gold-400 hover:bg-gold-500 text-comsoc-950 text-xs font-black rounded-xl transition shadow-xs flex items-center gap-1.5">
                        <span>Event Approvals</span>
                        @if($pendingEvents->count() > 0)
                            <span class="px-1.5 py-0.2 bg-comsoc-950 text-white rounded-full text-[10px]">
                                {{ $pendingEvents->count() }}
                            </span>
                        @endif
                    </a>
                    <a href="{{ route('documents.review.index') }}"
                       class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white text-xs font-bold rounded-xl transition border border-white/20 flex items-center gap-1.5">
                        <span>Doc Review Queue</span>
                        @if($pendingDocuments->count() > 0)
                            <span class="px-1.5 py-0.2 bg-amber-400 text-amber-950 rounded-full text-[10px]">
                                {{ $pendingDocuments->count() }}
                            </span>
                        @endif
                    </a>
                </div>
            </div>
        </div>

        {{-- ===== ADVISER SUMMARY METRICS ===== --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
            <div class="bg-white border border-amber-100 rounded-2xl p-4 shadow-xs bg-amber-50/40">
                <p class="text-[11px] font-bold text-amber-700 uppercase tracking-wider">Pending Event Approvals</p>
                <p class="text-2xl font-black text-amber-800 mt-1">{{ $pendingEvents->count() }}</p>
            </div>

            <div class="bg-white border border-purple-100 rounded-2xl p-4 shadow-xs bg-purple-50/40">
                <p class="text-[11px] font-bold text-purple-700 uppercase tracking-wider">Pending Document Reviews</p>
                <p class="text-2xl font-black text-purple-800 mt-1">{{ $pendingDocuments->count() }}</p>
            </div>

            <div class="bg-white border border-green-100 rounded-2xl p-4 shadow-xs bg-green-50/40">
                <p class="text-[11px] font-bold text-green-700 uppercase tracking-wider">Approved Events</p>
                <p class="text-2xl font-black text-green-800 mt-1">{{ $totalApprovedEvents }}</p>
            </div>

            <div class="bg-white border border-blue-100 rounded-2xl p-4 shadow-xs bg-blue-50/40">
                <p class="text-[11px] font-bold text-blue-700 uppercase tracking-wider">Official Documents</p>
                <p class="text-2xl font-black text-blue-800 mt-1">{{ $totalApprovedDocs }}</p>
            </div>
        </div>

        {{-- ===== TWO-COLUMN: Pending Event Queue & Pending Documents Queue ===== --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            {{-- Pending Events Queue --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-gray-900 text-base flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Events Awaiting Approval
                    </h3>
                    <a href="{{ route('events.approval.index') }}" class="text-xs font-bold text-comsoc-600 hover:text-comsoc-800 transition">
                        Full Queue →
                    </a>
                </div>

                <div class="space-y-3">
                    @forelse($pendingEvents as $evt)
                        <div class="p-3.5 rounded-xl border border-amber-100 bg-amber-50/30 flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <h4 class="font-bold text-gray-900 text-sm truncate">{{ $evt->title }}</h4>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    {{ $evt->event_date->format('M d, Y') }} · Created by {{ $evt->creator->name }}
                                </p>
                            </div>
                            <a href="{{ route('events.approval.show', $evt) }}"
                               class="px-3.5 py-1.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-lg transition shrink-0">
                                Review →
                            </a>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 italic text-center py-4">No events currently awaiting approval.</p>
                    @endforelse
                </div>
            </div>

            {{-- Pending Documents Queue --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-gray-900 text-base flex items-center gap-2">
                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Documents Awaiting Decision
                    </h3>
                    <a href="{{ route('documents.review.index') }}" class="text-xs font-bold text-comsoc-600 hover:text-comsoc-800 transition">
                        Full Queue →
                    </a>
                </div>

                <div class="space-y-3">
                    @forelse($pendingDocuments as $doc)
                        <div class="p-3.5 rounded-xl border border-purple-100 bg-purple-50/30 flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <h4 class="font-bold text-gray-900 text-sm truncate">{{ $doc->title }}</h4>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    {{ $doc->getCategoryLabel() }} · Uploaded by {{ $doc->uploader->name }}
                                </p>
                            </div>
                            <a href="{{ route('documents.review.show', $doc) }}"
                               class="px-3.5 py-1.5 bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold rounded-lg transition shrink-0">
                                Review →
                            </a>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 italic text-center py-4">No documents currently awaiting decision.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- ===== UPCOMING APPROVED CALENDAR ===== --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-gray-900 text-base flex items-center gap-2">
                    <svg class="w-4 h-4 text-comsoc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Approved Academic Organization Schedule
                </h3>
                <a href="{{ route('events.index') }}" class="text-xs font-bold text-comsoc-600 hover:text-comsoc-800 transition">
                    View Public Calendar →
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                @forelse($upcomingSchedule as $sch)
                    <div class="p-3.5 border border-gray-100 bg-gray-50/60 rounded-xl">
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full {{ $sch->getStatusColor() }}">
                            {{ $sch->getStatusLabel() }}
                        </span>
                        <h4 class="font-bold text-gray-900 text-sm mt-2 truncate">{{ $sch->title }}</h4>
                        <p class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ $sch->event_date->format('M d, Y') }}
                        </p>
                        <p class="text-xs text-gray-400 flex items-center gap-1 mt-0.5">
                            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {{ $sch->venue }}
                        </p>
                    </div>
                @empty
                    <p class="col-span-4 text-xs text-gray-400 italic text-center py-4">No upcoming events scheduled.</p>
                @endforelse
            </div>
        </div>

    </div>
</x-dashboard-layout>
