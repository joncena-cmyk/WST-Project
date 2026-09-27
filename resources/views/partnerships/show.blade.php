<x-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <a href="{{ route('partnerships.index') }}" class="text-xs font-bold text-comsoc-600 hover:text-comsoc-800 transition flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Partnerships Hub
            </a>
            <div class="flex items-center gap-2">
                @can('update', $partnership)
                    <a href="{{ route('partnerships.edit', $partnership) }}"
                       class="px-3.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition">
                        Edit Details
                    </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="{ 
        activityModalOpen: false, 
        docModalOpen: false, 
        letterModalOpen: false, 
        spawnModalOpen: false, 
        endorseModalOpen: false,
        emailModalOpen: false
    }">

        {{-- ===== FLUID TRANSACTION PROGRESS STEPPER ===== --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-xs">
            <div class="flex items-center justify-between mb-3 border-b border-gray-100 pb-2">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <h3 class="text-xs font-black text-gray-900 uppercase tracking-wider">Collaboration Transaction Pipeline</h3>
                </div>
                <span class="text-[11px] font-bold text-gray-500">
                    Status: <strong class="text-gray-900">{{ $partnership->getStatusLabel() }}</strong>
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-5 gap-3 pt-1">
                {{-- Step 1: Proposal Logged --}}
                <div class="flex items-center gap-3 p-3 rounded-xl border bg-emerald-50/70 border-emerald-200">
                    <div class="w-7 h-7 rounded-full bg-emerald-600 text-white flex items-center justify-center font-black text-xs shrink-0">✓</div>
                    <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-800">Step 1</p>
                        <p class="text-xs font-bold text-gray-900 truncate">Partner Logged</p>
                    </div>
                </div>

                {{-- Step 2: Letter & Proposal Drafted --}}
                @php
                    $hasLetter = !empty($partnership->letter_content) || $partnership->proposal_document_id;
                    $partnerAccepted = $partnership->isPartnerAccepted();
                    $partnerResponded = !empty($partnership->partner_responded_at);
                @endphp
                <div class="flex items-center gap-3 p-3 rounded-xl border {{ $hasLetter ? 'bg-emerald-50/70 border-emerald-200' : 'bg-amber-50/70 border-amber-200' }}">
                    <div class="w-7 h-7 rounded-full {{ $hasLetter ? 'bg-emerald-600 text-white' : 'bg-amber-500 text-white' }} flex items-center justify-center font-black text-xs shrink-0">
                        {{ $hasLetter ? '✓' : '2' }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-wider {{ $hasLetter ? 'text-emerald-800' : 'text-amber-800' }}">Step 2</p>
                        <p class="text-xs font-bold text-gray-900 truncate">Proposal Letter</p>
                    </div>
                </div>

                {{-- Step 3: Partner Response & Acceptance --}}
                <div class="flex items-center gap-3 p-3 rounded-xl border {{ $partnerAccepted ? 'bg-emerald-50/70 border-emerald-200' : ($partnerResponded ? 'bg-amber-50/70 border-amber-200' : 'bg-indigo-50/70 border-indigo-200') }}">
                    <div class="w-7 h-7 rounded-full {{ $partnerAccepted ? 'bg-emerald-600 text-white' : ($partnerResponded ? 'bg-amber-600 text-white' : 'bg-indigo-600 text-white') }} flex items-center justify-center font-black text-xs shrink-0">
                        {{ $partnerAccepted ? '✓' : '3' }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-wider {{ $partnerAccepted ? 'text-emerald-800' : ($partnerResponded ? 'text-amber-800' : 'text-indigo-800') }}">Step 3</p>
                        <p class="text-xs font-bold text-gray-900 truncate">Partner Accepted</p>
                    </div>
                </div>

                {{-- Step 4: Adviser Endorsement --}}
                @php
                    $isEndorsed = $partnership->isEndorsed();
                    $isUnderReview = $partnership->status === 'under_review';
                @endphp
                <div class="flex items-center gap-3 p-3 rounded-xl border {{ $isEndorsed ? 'bg-emerald-50/70 border-emerald-200' : ($isUnderReview ? 'bg-blue-50/70 border-blue-200' : 'bg-gray-50 border-gray-200') }}">
                    <div class="w-7 h-7 rounded-full {{ $isEndorsed ? 'bg-emerald-600 text-white' : ($isUnderReview ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600') }} flex items-center justify-center font-black text-xs shrink-0">
                        {{ $isEndorsed ? '✓' : '4' }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-wider {{ $isEndorsed ? 'text-emerald-800' : ($isUnderReview ? 'text-blue-800' : 'text-gray-500') }}">Step 4</p>
                        <p class="text-xs font-bold text-gray-900 truncate">Adviser Endorsement</p>
                    </div>
                </div>

                {{-- Step 5: Joint Event Execution --}}
                @php
                    $hasEvent = !is_null($partnership->event_id);
                @endphp
                <div class="flex items-center gap-3 p-3 rounded-xl border {{ $hasEvent ? 'bg-emerald-50/70 border-emerald-200' : ($isEndorsed ? 'bg-indigo-50/70 border-indigo-200' : 'bg-gray-50 border-gray-200') }}">
                    <div class="w-7 h-7 rounded-full {{ $hasEvent ? 'bg-emerald-600 text-white' : ($isEndorsed ? 'bg-indigo-600 text-white animate-pulse' : 'bg-gray-200 text-gray-600') }} flex items-center justify-center font-black text-xs shrink-0">
                        {{ $hasEvent ? '✓' : '5' }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-wider {{ $hasEvent ? 'text-emerald-800' : ($isEndorsed ? 'text-indigo-800' : 'text-gray-500') }}">Step 5</p>
                        <p class="text-xs font-bold text-gray-900 truncate">Joint Event Spawn</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== PARTNER ACCEPTANCE & ONLINE RESPONSE BANNER ===== --}}
        @if($partnership->partner_responded_at)
            @if($partnership->isPartnerAccepted())
                <div class="bg-gradient-to-r from-emerald-900 via-teal-900 to-slate-900 rounded-2xl p-6 text-white shadow-md border border-emerald-500/40 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 flex items-center justify-center font-black text-2xl shrink-0">
                            ✓
                        </div>
                        <div class="space-y-1">
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-200 border border-emerald-400/30">
                                🎉 Partner Organization Formally Accepted
                            </div>
                            <h3 class="text-base font-black text-white">{{ $partnership->partner->name }} Accepted Proposal</h3>
                            <p class="text-xs text-emerald-200/90 leading-relaxed">
                                Signed &amp; confirmed by <strong>{{ $partnership->partner_responder_name }}</strong> ({{ $partnership->partner_responder_title ?? 'Representative' }}) on {{ $partnership->partner_responded_at->format('M d, Y h:i A') }}.
                            </p>
                            @if($partnership->partner_response_remarks)
                                <p class="text-xs text-white/90 bg-black/20 p-2.5 rounded-xl border border-white/10 mt-2">
                                    <em>"{{ $partnership->partner_response_remarks }}"</em>
                                </p>
                            @endif
                        </div>
                    </div>

                    <a href="{{ $partnership->getResponseUrl() }}" target="_blank"
                       class="px-4 py-2 bg-emerald-500 hover:bg-emerald-400 text-gray-950 font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-1.5 shrink-0">
                        View Partner Portal View ↗
                    </a>
                </div>
            @else
                <div class="bg-gradient-to-r from-amber-900 via-orange-900 to-slate-900 rounded-2xl p-6 text-white shadow-md border border-amber-500/40 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-amber-500/20 text-amber-300 border border-amber-400/30 flex items-center justify-center font-black text-2xl shrink-0">
                            📝
                        </div>
                        <div class="space-y-1">
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-200 border border-amber-400/30">
                                📝 Partner Feedback Received
                            </div>
                            <h3 class="text-base font-black text-white">{{ $partnership->getPartnerResponseStatusLabel() }}</h3>
                            <p class="text-xs text-amber-200/90 leading-relaxed">
                                Submitted by <strong>{{ $partnership->partner_responder_name }}</strong> ({{ $partnership->partner_responder_title ?? 'Representative' }}) on {{ $partnership->partner_responded_at->format('M d, Y h:i A') }}.
                            </p>
                            @if($partnership->partner_response_remarks)
                                <p class="text-xs text-white/90 bg-black/20 p-2.5 rounded-xl border border-white/10 mt-2">
                                    <em>"{{ $partnership->partner_response_remarks }}"</em>
                                </p>
                            @endif
                        </div>
                    </div>

                    <button type="button" @click="letterModalOpen = true"
                            class="px-4 py-2 bg-amber-400 hover:bg-amber-300 text-gray-950 font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-1.5 shrink-0">
                        Edit Proposal Letter
                    </button>
                </div>
            @endif
        @else
            <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-2xl p-5 text-white shadow-md border border-indigo-800/50 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-400/30">
                        ⏳ Direct Partner Acceptance Portal Available
                    </div>
                    <h3 class="text-sm font-black text-white">{{ $partnership->partner->name }} Can Accept Proposal Online</h3>
                    <p class="text-xs text-slate-300 leading-relaxed max-w-2xl">
                        A proposal email was dispatched with a 1-click acceptance link. You can test or copy the partner's unique response portal link.
                    </p>
                </div>

                <div class="flex items-center gap-2 shrink-0 flex-wrap">
                    <a href="{{ $partnership->getResponseUrl() }}" target="_blank"
                       class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        Test Partner Portal View ↗
                    </a>
                    <button type="button" @click="emailModalOpen = true"
                            class="px-3.5 py-2 bg-white/10 hover:bg-white/20 text-white font-bold text-xs rounded-xl border border-white/20 transition flex items-center gap-1.5">
                        ✉️ Send Email
                    </button>
                </div>
            </div>
        @endif

        {{-- ===== PARTNERSHIP HEADER CARD ===== --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-xs space-y-4">
            <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-4">
                <div class="space-y-2 min-w-0 flex-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $partnership->getStatusColor() }}">
                            {{ $partnership->getStatusLabel() }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $partnership->getPartnershipTypeBadgeColor() }}">
                            {{ $partnership->getPartnershipTypeLabel() }}
                        </span>
                        <a href="{{ route('partner-organizations.show', $partnership->partner) }}"
                           class="px-2.5 py-0.5 rounded-full text-xs font-bold border hover:underline {{ $partnership->partner->getTypeBadgeColor() }}">
                            {{ $partnership->partner->name }}
                        </a>
                        <span class="text-xs font-semibold text-gray-500">AY {{ $partnership->academic_year }}</span>

                        @if($partnership->sponsorship_amount > 0)
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                💰 ₱ {{ number_format($partnership->sponsorship_amount, 2) }} Support Fund
                            </span>
                        @endif
                    </div>

                    <h1 class="text-xl sm:text-2xl font-black text-gray-900 leading-tight">{{ $partnership->title }}</h1>

                    @if($partnership->description)
                        <p class="text-xs text-gray-600 leading-relaxed max-w-3xl">{{ $partnership->description }}</p>
                    @endif

                    <div class="flex items-center gap-4 text-xs text-gray-500 pt-2 flex-wrap">
                        <span>Timeline: <strong class="text-gray-800">{{ $partnership->start_date ? $partnership->start_date->format('M d, Y') : 'TBD' }} – {{ $partnership->end_date ? $partnership->end_date->format('M d, Y') : 'TBD' }}</strong></span>
                        <span>Proposed By: <strong class="text-gray-800">{{ $partnership->creator->name ?? 'Officer' }}</strong></span>
                        @if($partnership->endorsed_by)
                            <span class="text-emerald-700 font-bold flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Endorsed by {{ $partnership->endorser->name ?? 'Adviser' }} on {{ $partnership->endorsed_at ? $partnership->endorsed_at->format('M d, Y') : '' }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Status Controls (Officers / Advisers) --}}
                @can('update', $partnership)
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 shrink-0 space-y-2">
                        <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-wider">Partnership Status</label>
                        <form method="POST" action="{{ route('partnerships.status', $partnership) }}" class="flex items-center gap-2">
                            @csrf
                            <select name="status" class="text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500">
                                <option value="proposed" {{ $partnership->status === 'proposed' ? 'selected' : '' }}>Proposed</option>
                                <option value="under_review" {{ $partnership->status === 'under_review' ? 'selected' : '' }}>Under Review</option>
                                @can('review', $partnership)
                                    <option value="approved" {{ $partnership->status === 'approved' ? 'selected' : '' }}>Approved</option>
                                @endcan
                                <option value="active" {{ $partnership->status === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="completed" {{ $partnership->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="archived" {{ $partnership->status === 'archived' ? 'selected' : '' }}>Archived</option>
                                @can('review', $partnership)
                                    <option value="declined" {{ $partnership->status === 'declined' ? 'selected' : '' }}>Declined</option>
                                @endcan
                            </select>
                            <button type="submit" class="px-3 py-2 bg-comsoc-700 hover:bg-comsoc-800 text-white text-xs font-bold rounded-xl transition shadow-2xs">
                                Update
                            </button>
                        </form>
                    </div>
                @endcan
            </div>

            {{-- Review Remarks if any --}}
            @if($partnership->review_remarks || $partnership->endorsement_remarks)
                <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-900 leading-relaxed">
                    <p class="font-bold flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-amber-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                        Academic Adviser Endorsement Remarks:
                    </p>
                    <p class="mt-1 text-gray-800">{{ $partnership->endorsement_remarks ?? $partnership->review_remarks }}</p>
                    <p class="text-[10px] text-amber-700 mt-1.5">Reviewed by {{ $partnership->endorser->name ?? $partnership->reviewer->name ?? 'Academic Adviser' }} on {{ ($partnership->endorsed_at ?? $partnership->reviewed_at)?->format('M d, Y h:i A') }}</p>
                </div>
            @endif
        </div>

        {{-- ===== ADVISER ENDORSEMENT BANNER / ACTION ===== --}}
        @if((auth()->user()->isAdviser() || auth()->user()->isAdmin() || auth()->user()->isPresident()) && in_array($partnership->status, ['proposed', 'under_review']))
            <div class="bg-gradient-to-r from-blue-900 via-indigo-900 to-blue-950 rounded-2xl p-6 text-white shadow-lg border border-blue-800/60 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="space-y-1">
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-500/20 text-blue-200 border border-blue-400/30">
                        👨‍🏫 Academic Adviser Action Required
                    </div>
                    <h3 class="text-base font-black text-white">Review &amp; Endorse Collaboration Proposal</h3>
                    <p class="text-xs text-blue-200/90 max-w-2xl leading-relaxed">
                        Verify the partnership objectives, formal letter of intent, and schedule. Endorsing authorizes the officers to spawn the joint COMSOC event.
                    </p>
                </div>
                <button type="button" @click="endorseModalOpen = true"
                        class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-400 text-gray-950 font-black text-xs rounded-xl shadow-md transition flex items-center gap-2 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Endorse Proposal
                </button>
            </div>
        @endif

        {{-- ===== JOINT EVENT SPAWN BANNER ===== --}}
        @if($partnership->canBeSpawnedToEvent())
            <div class="bg-gradient-to-r from-emerald-900 via-teal-900 to-slate-900 rounded-2xl p-6 text-white shadow-lg border border-emerald-700/50 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="space-y-1">
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-500/20 text-emerald-200 border border-emerald-400/30">
                        ✨ Collaboration Endorsed &amp; Ready
                    </div>
                    <h3 class="text-base font-black text-white">Convert to Official Joint COMSOC Event</h3>
                    <p class="text-xs text-emerald-200/90 max-w-2xl leading-relaxed">
                        Spawning creates the public event co-hosted with <strong>{{ $partnership->partner->name }}</strong>, initializes registration, and automatically delegates committee tasks based on the partnership type.
                    </p>
                </div>
                @can('update', $partnership)
                    <button type="button" @click="spawnModalOpen = true"
                            class="px-5 py-2.5 bg-emerald-400 hover:bg-emerald-300 text-gray-950 font-black text-xs rounded-xl shadow-md transition flex items-center gap-2 shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        1-Click Convert to Joint Event
                    </button>
                @endcan
            </div>
        @elseif($partnership->event)
            <div class="bg-white border-2 border-emerald-500/40 rounded-2xl p-6 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-black text-xl shrink-0">
                        🎉
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                Active Official Joint Event
                            </span>
                            <span class="text-xs text-gray-500">{{ $partnership->event->event_date->format('M d, Y') }}</span>
                        </div>
                        <h3 class="text-base font-black text-gray-900 mt-0.5">{{ $partnership->event->title }}</h3>
                        <p class="text-xs text-gray-600 mt-0.5">Venue: {{ $partnership->event->venue }} · Registered Attendees: <strong>{{ $partnership->event->registrations()->count() }}</strong></p>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0 flex-wrap">
                    <a href="{{ route('events.show', $partnership->event) }}"
                       class="px-4 py-2 bg-comsoc-700 hover:bg-comsoc-800 text-white text-xs font-bold rounded-xl transition flex items-center gap-1.5 shadow-2xs">
                        View Event Dashboard →
                    </a>
                    <a href="{{ route('attendance.scanner', $partnership->event) }}"
                       class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition flex items-center gap-1">
                        📷 QR Scanner
                    </a>
                    <a href="{{ route('certificates.manage', $partnership->event) }}"
                       class="px-3.5 py-2 bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-200 text-xs font-bold rounded-xl transition flex items-center gap-1">
                        🎓 Issue Certificates
                    </a>
                </div>
            </div>
        @endif

        {{-- ===== FORMAL LETTER OF INTENT & PROPOSAL CARD ===== --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-xs space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-gray-100 pb-4">
                <div>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-100">
                        Formal Activity Documentation
                    </span>
                    <h2 class="text-base font-black text-gray-900 mt-1">Official Letter of Intent &amp; Activity Proposal</h2>
                    <p class="text-xs text-gray-500">Official document submitted to the Academic Adviser and {{ $partnership->partner->name }}</p>
                </div>

                <div class="flex items-center gap-2 flex-wrap">
                    @can('update', $partnership)
                        <button type="button" @click="emailModalOpen = true"
                                class="px-3.5 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 text-xs font-bold rounded-xl transition flex items-center gap-1.5 shadow-2xs">
                            <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            ✉️ Email Letter to Partner
                        </button>

                        <button type="button" @click="letterModalOpen = true"
                                class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 text-xs font-bold rounded-xl transition flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            Edit Proposal Letter
                        </button>

                        @if($partnership->canBeSubmittedToAdviser())
                            <form method="POST" action="{{ route('partnerships.submit-to-adviser', $partnership) }}">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-xs transition flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                    Submit to Adviser
                                </button>
                            </form>
                        @endif
                    @endcan
                </div>
            </div>

            {{-- Formal Letter Container with Letterhead --}}
            <div class="bg-gray-50 border border-gray-200 rounded-2xl p-6 sm:p-8 space-y-6 font-sans text-xs text-gray-800">
                {{-- Letterhead --}}
                <div class="text-center space-y-1 border-b-2 border-gray-300 pb-5">
                    <p class="text-[11px] font-bold text-gray-500 uppercase tracking-widest">Republic of the Philippines</p>
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-wider">University of Makati</h3>
                    <p class="text-xs font-bold text-gray-700">College of Computing and Information Sciences</p>
                    <p class="text-xs font-black text-comsoc-700">Computer Society (COMSOC)</p>
                    <p class="text-[10px] text-gray-400">J.P. Rizal Ext., West Rembo, Taguig City / Makati City</p>
                </div>

                {{-- Letter Metadata --}}
                <div class="flex justify-between items-start text-xs text-gray-600">
                    <div>
                        <p><strong>Date:</strong> {{ now()->format('F d, Y') }}</p>
                        <p><strong>Academic Year:</strong> {{ $partnership->academic_year }}</p>
                        <p><strong>Transaction Ref:</strong> <span class="font-mono text-gray-800">COL-{{ $partnership->academic_year }}-{{ str_pad($partnership->id, 4, '0', STR_PAD_LEFT) }}</span></p>
                    </div>
                    <div class="text-right">
                        <span class="inline-block px-2.5 py-1 rounded-lg text-[11px] font-bold {{ $partnership->isEndorsed() ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-amber-100 text-amber-800 border border-amber-200' }}">
                            {{ $partnership->isEndorsed() ? '✓ Formally Endorsed' : '⏳ Awaiting Review' }}
                        </span>
                    </div>
                </div>

                {{-- Letter Content --}}
                <div class="whitespace-pre-line leading-relaxed text-gray-700 bg-white p-5 rounded-xl border border-gray-200">
{{ $partnership->getDefaultLetterContent() }}
                </div>

                {{-- Signatures Section --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4 border-t border-gray-200">
                    <div class="space-y-1">
                        <p class="text-[10px] font-bold uppercase text-gray-400">Prepared &amp; Proposed By:</p>
                        <p class="font-black text-gray-900 text-xs">{{ $partnership->creator->name ?? 'Officer' }}</p>
                        <p class="text-[11px] text-gray-500">{{ $partnership->creator?->getRoleLabel() ?? 'Officer' }}, COMSOC</p>
                    </div>

                    <div class="space-y-1 sm:text-right">
                        <p class="text-[10px] font-bold uppercase text-gray-400">Academic Adviser Endorsement:</p>
                        @if($partnership->endorsed_by)
                            <p class="font-black text-emerald-800 text-xs flex items-center sm:justify-end gap-1">
                                <span>✍️ {{ $partnership->endorser->name ?? 'Academic Adviser' }}</span>
                            </p>
                            <p class="text-[11px] text-emerald-700">Verified &amp; Signed on {{ $partnership->endorsed_at ? $partnership->endorsed_at->format('M d, Y') : '' }}</p>
                        @else
                            <p class="italic text-gray-400 text-xs">Pending Formal Adviser Sign-off</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== GRID: ACTIVITIES & DOCUMENTS ===== --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Technical Activities Column --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-xs space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <div>
                        <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Technical Collaborations</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Workshops, bootcamps, and technical operations</p>
                    </div>
                    @can('update', $partnership)
                        <button type="button" @click="activityModalOpen = true"
                                class="px-3 py-1.5 bg-comsoc-50 hover:bg-comsoc-100 text-comsoc-700 text-xs font-bold rounded-xl border border-comsoc-200 transition flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add Activity
                        </button>
                    @endcan
                </div>

                <div class="space-y-3">
                    @forelse($partnership->activities as $activity)
                        <div class="p-4 rounded-xl border border-gray-100 bg-gray-50/50 space-y-2">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                        {{ $activity->getTypeLabel() }}
                                    </span>
                                    <h4 class="font-bold text-gray-900 text-sm mt-1">{{ $activity->title }}</h4>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $activity->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-700' }}">
                                    {{ ucfirst($activity->status) }}
                                </span>
                            </div>

                            @if($activity->description)
                                <p class="text-xs text-gray-600 leading-relaxed">{{ $activity->description }}</p>
                            @endif

                            <div class="flex items-center justify-between text-xs text-gray-500 pt-1">
                                <span>{{ $activity->activity_date ? $activity->activity_date->format('M d, Y') : 'Date TBD' }} · {{ $activity->venue ?? 'Venue TBD' }}</span>
                                @can('update', $partnership)
                                    <div class="flex items-center gap-1.5">
                                        @if($activity->status !== 'completed')
                                            <form method="POST" action="{{ route('partnerships.activities.update-status', $activity) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit" class="text-emerald-600 hover:text-emerald-800 font-bold text-[11px]">Mark Complete</button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('partnerships.activities.destroy', $activity) }}" onsubmit="return confirm('Remove activity?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 text-[11px]">Delete</button>
                                        </form>
                                    </div>
                                @endcan
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 italic py-4 text-center">No technical activities scheduled for this partnership yet.</p>
                    @endforelse
                </div>
            </div>

            {{-- MoA & Partnership Documents Column --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-xs space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <div>
                        <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide">MoA &amp; Supporting Documents</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Formal agreements, permits, and proposal letters</p>
                    </div>
                    @can('update', $partnership)
                        <button type="button" @click="docModalOpen = true"
                                class="px-3 py-1.5 bg-comsoc-50 hover:bg-comsoc-100 text-comsoc-700 text-xs font-bold rounded-xl border border-comsoc-200 transition flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Upload File
                        </button>
                    @endcan
                </div>

                <div class="space-y-3">
                    @forelse($partnership->documents as $doc)
                        <div class="p-3.5 rounded-xl border border-gray-100 bg-gray-50/50 flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-100">
                                    {{ $doc->getTypeLabel() }}
                                </span>
                                <h4 class="font-bold text-gray-900 text-xs truncate mt-1">{{ $doc->title }}</h4>
                                <p class="text-[11px] text-gray-400">{{ $doc->file_name }} · {{ $doc->formatted_file_size }}</p>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                <a href="{{ route('partnerships.documents.download', $doc) }}"
                                   class="px-3 py-1.5 bg-white hover:bg-gray-100 text-gray-700 text-xs font-bold rounded-lg border border-gray-200 transition flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    Download
                                </a>
                                @can('update', $partnership)
                                    <form method="POST" action="{{ route('partnerships.documents.destroy', $doc) }}" onsubmit="return confirm('Delete document?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 rounded transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 italic py-4 text-center">No documents uploaded for this partnership yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- ===== EDIT LETTER OF INTENT MODAL ===== --}}
        <div x-show="letterModalOpen" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4">
            <div @click.outside="letterModalOpen = false"
                 class="bg-white rounded-2xl max-w-2xl w-full p-6 shadow-2xl border border-gray-100 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Customize Official Letter of Intent</h3>
                        <p class="text-xs text-gray-500">Edit the proposal narrative and sponsorship support amount</p>
                    </div>
                    <button @click="letterModalOpen = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('partnerships.save-letter', $partnership) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Sponsorship / Financial Support Amount (₱)</label>
                        <input type="number" step="0.01" name="sponsorship_amount" value="{{ old('sponsorship_amount', $partnership->sponsorship_amount) }}" placeholder="0.00"
                               class="w-full text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500"/>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Proposal Letter Body &amp; Letterhead Content *</label>
                        <textarea name="letter_content" rows="12" required
                                  class="w-full text-xs font-mono border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500">{{ old('letter_content', $partnership->getDefaultLetterContent()) }}</textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" @click="letterModalOpen = false" class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-xl">Cancel</button>
                        <button type="submit" class="px-5 py-2 bg-comsoc-700 hover:bg-comsoc-800 text-white text-xs font-bold rounded-xl shadow-xs">Save Proposal Draft</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ===== ADVISER ENDORSE MODAL ===== --}}
        <div x-show="endorseModalOpen" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4">
            <div @click.outside="endorseModalOpen = false"
                 class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Academic Adviser Formal Endorsement</h3>
                        <p class="text-xs text-gray-500">Sign-off and endorse {{ $partnership->partner->name }} collaboration</p>
                    </div>
                    <button @click="endorseModalOpen = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('partnerships.endorse', $partnership) }}" class="space-y-4">
                    @csrf
                    <div class="p-3 bg-blue-50 border border-blue-200 rounded-xl text-xs text-blue-900">
                        <p class="font-bold">Endorsement Details:</p>
                        <p class="mt-0.5">Endorsing this collaboration approves the Letter of Intent and authorizes the creation of the joint campus activity.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Adviser Review Remarks &amp; Recommendations</label>
                        <textarea name="remarks" rows="4" placeholder="e.g. Endorsed with recommendation to coordinate with the CCIS Department Chair and secure laboratory facilities..."
                                  class="w-full text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" @click="endorseModalOpen = false" class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-xl">Cancel</button>
                        <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-xs flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Confirm Endorsement
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ===== SPAWN JOINT EVENT MODAL ===== --}}
        <div x-show="spawnModalOpen" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4">
            <div @click.outside="spawnModalOpen = false"
                 class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl border border-gray-100 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">🚀 Convert to Joint COMSOC Event</h3>
                        <p class="text-xs text-gray-500">Auto-fills details from partnership with {{ $partnership->partner->name }}</p>
                    </div>
                    <button @click="spawnModalOpen = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('partnerships.spawn-event', $partnership) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Event Title *</label>
                        <input type="text" name="title" required value="{{ old('title', $partnership->title) }}"
                               class="w-full text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500"/>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Event Date *</label>
                            <input type="date" name="event_date" required value="{{ old('event_date', $partnership->start_date ? $partnership->start_date->format('Y-m-d') : now()->addDays(14)->format('Y-m-d')) }}"
                                   class="w-full text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500"/>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Venue / Platform *</label>
                            <input type="text" name="venue" required value="{{ old('venue', 'University of Makati Auditorium / Online') }}"
                                   class="w-full text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500"/>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Start Time *</label>
                            <input type="time" name="start_time" required value="09:00"
                                   class="w-full text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500"/>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">End Time</label>
                            <input type="time" name="end_time" value="17:00"
                                   class="w-full text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500"/>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Participant Limit</label>
                            <input type="number" name="participant_limit" value="150" min="1"
                                   class="w-full text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500"/>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Event Description &amp; Co-Host Details</label>
                        <textarea name="description" rows="3"
                                  class="w-full text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500">{{ old('description', "Joint event co-presented by the Computer Society and {$partnership->partner->name}.\n\n" . ($partnership->description ?? '')) }}</textarea>
                    </div>

                    <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-[11px] text-emerald-900 space-y-1">
                        <p class="font-bold">✨ Automated Committee Initialization:</p>
                        <p>This action will automatically create Logistics, Programs &amp; AV, and Creatives committees with assigned operational tasks.</p>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" @click="spawnModalOpen = false" class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-xl">Cancel</button>
                        <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-xs flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            Spawn Joint Event
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ===== ADD ACTIVITY MODAL ===== --}}
        <div x-show="activityModalOpen" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4">
            <div @click.outside="activityModalOpen = false"
                 class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-gray-900">Add Technical Activity</h3>
                    <button @click="activityModalOpen = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('partnerships.activities.store', $partnership) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Activity Title *</label>
                        <input type="text" name="title" required placeholder="e.g. Cloud DevOps Hands-On Workshop"
                               class="w-full text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500"/>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Activity Type *</label>
                            <select name="activity_type" required class="w-full text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500">
                                <option value="workshop">Technical Workshop</option>
                                <option value="technical_support">Technical Support &amp; AV Ops</option>
                                <option value="speaker_panel">Resource Speakers &amp; Judges</option>
                                <option value="hackathon">Hackathon</option>
                                <option value="seminar">Tech Seminar</option>
                                <option value="competition">Competition</option>
                                <option value="project">Software Project</option>
                                <option value="training">Industry Training</option>
                                <option value="community">Community Outreach</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Target Date</label>
                            <input type="date" name="activity_date"
                                   class="w-full text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500"/>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Venue / Platform</label>
                        <input type="text" name="venue" placeholder="e.g. CCIS Lab 304, Zoom Online"
                               class="w-full text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500"/>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Link to COMSOC Event (Optional)</label>
                        <select name="event_id" class="w-full text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500">
                            <option value="">None (Independent Collaboration)</option>
                            @foreach($events as $ev)
                                <option value="{{ $ev->id }}">{{ $ev->title }} ({{ $ev->event_date->format('M d, Y') }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Description &amp; Objectives</label>
                        <textarea name="description" rows="3" placeholder="Scope, speaker/facilitators, learning outcomes..."
                                  class="w-full text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" @click="activityModalOpen = false" class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-xl">Cancel</button>
                        <button type="submit" class="px-5 py-2 bg-comsoc-700 hover:bg-comsoc-800 text-white text-xs font-bold rounded-xl shadow-xs">Save Activity</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ===== UPLOAD DOCUMENT MODAL ===== --}}
        <div x-show="docModalOpen" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4">
            <div @click.outside="docModalOpen = false"
                 class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-gray-900">Upload Partnership Document</h3>
                    <button @click="docModalOpen = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('partnerships.documents.store', $partnership) }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Document Title *</label>
                        <input type="text" name="title" required placeholder="e.g. Memorandum of Agreement (Signed)"
                               class="w-full text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500"/>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Document Type *</label>
                        <select name="document_type" required class="w-full text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500">
                            <option value="moa_mou">MoA / MoU Agreement</option>
                            <option value="letter_of_intent">Letter of Intent</option>
                            <option value="sponsorship_deck">Sponsorship Package</option>
                            <option value="invitation">Partnership Invitation</option>
                            <option value="completion_report">Activity Completion Report</option>
                            <option value="other">Other Supporting Document</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Attachment File (PDF, Word, Images, Zip - Max 10MB) *</label>
                        <input type="file" name="file" required
                               class="w-full text-xs border border-gray-200 rounded-xl file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-comsoc-50 file:text-comsoc-700 hover:file:bg-comsoc-100"/>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" @click="docModalOpen = false" class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-xl">Cancel</button>
                        <button type="submit" class="px-5 py-2 bg-comsoc-700 hover:bg-comsoc-800 text-white text-xs font-bold rounded-xl shadow-xs">Upload Document</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ===== EMAIL PROPOSAL TO PARTNER MODAL ===== --}}
        <div x-show="emailModalOpen" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4">
            <div @click.outside="emailModalOpen = false"
                 class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center text-sm font-bold">
                            ✉️
                        </div>
                        <div>
                            <h3 class="text-base font-black text-gray-900">Email Letter of Intent &amp; Proposal</h3>
                            <p class="text-xs text-gray-500">Send official branded communication to {{ $partnership->partner->name }}</p>
                        </div>
                    </div>
                    <button @click="emailModalOpen = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('partnerships.send-email', $partnership) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Recipient Organization Email *</label>
                        <input type="email" name="recipient_email" required
                               value="{{ old('recipient_email', $partnership->partner->contact_email) }}"
                               placeholder="e.g. partnerships@company.org"
                               class="w-full text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500"/>
                        <p class="text-[11px] text-gray-500 mt-1">Pre-filled with {{ $partnership->partner->name }}'s registered contact email.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Additional Note / Custom Greetings (Optional)</label>
                        <textarea name="custom_message" rows="3"
                                  placeholder="Add an optional warm greeting, meeting link, or note from COMSOC officers..."
                                  class="w-full text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500"></textarea>
                    </div>

                    <div class="bg-indigo-50/70 border border-indigo-100 rounded-xl p-3 text-xs text-indigo-900 flex items-start gap-2">
                        <svg class="w-4 h-4 text-indigo-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                        <span>The email will contain the complete official <strong>University of Makati &amp; COMSOC</strong> Letterhead, formal Letter of Intent, event objectives, and officer signatures.</span>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" @click="emailModalOpen = false" class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-xl">Cancel</button>
                        <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black rounded-xl shadow-xs transition flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            Dispatch Email to Partner
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-dashboard-layout>
