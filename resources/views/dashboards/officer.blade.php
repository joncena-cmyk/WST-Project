<x-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-base font-bold text-gray-900">Officer Operations Dashboard</h1>
            <a href="{{ route('analytics.index') }}" class="px-3 py-1.5 bg-comsoc-50 hover:bg-comsoc-100 text-comsoc-700 text-xs font-bold rounded-lg border border-comsoc-200 transition flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Analytics &amp; Reports →
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">

        {{-- ===== HERO WELCOME STRIP ===== --}}
        <div class="relative rounded-2xl overflow-hidden shadow-md" style="min-height:140px;">
            <div class="absolute inset-0">
                <img src="{{ asset('images/hero_logo.jpg') }}" alt="" class="w-full h-full object-cover"/>
            </div>
            <div class="absolute inset-0" style="background:linear-gradient(135deg,rgba(5,46,22,0.95) 0%,rgba(5,46,22,0.80) 100%)"></div>
            <div class="absolute top-0 left-0 right-0 h-0.5 bg-gradient-to-r from-gold-600 via-gold-400 to-gold-600"></div>

            <div class="relative z-10 flex items-center justify-between p-6 text-white">
                <div class="flex items-center gap-4">
                    <img src="{{ $user->getProfilePictureUrl() }}"
                         alt="{{ $user->name }}"
                         class="w-14 h-14 rounded-full object-cover border-2 border-gold-400 shadow-md"/>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-gold-400 text-comsoc-950 uppercase tracking-wide">
                                {{ $user->getRoleLabel() }}
                            </span>
                            <span class="text-xs text-comsoc-300">Operations Control</span>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-black mt-1">{{ $user->name }}</h2>
                    </div>
                </div>

                <div class="hidden sm:flex items-center gap-2">
                    <a href="{{ route('events.create') }}"
                       class="px-4 py-2 bg-gold-400 hover:bg-gold-500 text-comsoc-950 text-xs font-black rounded-xl transition shadow-xs flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Create Event
                    </a>
                    <a href="{{ route('documents.create') }}"
                       class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white text-xs font-bold rounded-xl transition border border-white/20 flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        Upload Document
                    </a>
                </div>
            </div>
        </div>

        {{-- ===== OPERATIONAL METRIC CARDS ===== --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
            <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-xs">
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Active Members</p>
                <p class="text-2xl font-black text-comsoc-700 mt-1">{{ $totalActiveMembers }}</p>
            </div>

            <div class="bg-white border border-blue-100 rounded-2xl p-4 shadow-xs bg-blue-50/40">
                <p class="text-[11px] font-bold text-blue-600 uppercase tracking-wider">Live &amp; Upcoming Events</p>
                <p class="text-2xl font-black text-blue-800 mt-1">{{ $activeEventsCount }}</p>
            </div>

            <div class="bg-white border border-amber-100 rounded-2xl p-4 shadow-xs bg-amber-50/40">
                <p class="text-[11px] font-bold text-amber-600 uppercase tracking-wider">Task Velocity</p>
                <div class="flex items-baseline gap-2 mt-1">
                    <p class="text-2xl font-black text-amber-800">{{ $completedTasks }}/{{ $totalTasksCount }}</p>
                    <span class="text-xs font-bold text-amber-600">({{ $taskVelocity }}%)</span>
                </div>
            </div>

            <div class="bg-white border border-purple-100 rounded-2xl p-4 shadow-xs bg-purple-50/40">
                <p class="text-[11px] font-bold text-purple-600 uppercase tracking-wider">Pending Documents</p>
                <p class="text-2xl font-black text-purple-800 mt-1">{{ $pendingDocs->count() }}</p>
            </div>
        </div>

        {{-- ===== ROLE-SPECIFIC FOCUS DESK ===== --}}
        @if($user->isVpExternal())
            <div class="bg-gradient-to-r from-blue-900 to-indigo-950 border border-blue-800 text-white rounded-2xl p-5 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded text-[10px] font-black bg-blue-400 text-blue-950 uppercase">VP External Desk</span>
                            <span class="text-xs text-blue-200">Partnerships &amp; Industry Linkages</span>
                        </div>
                        <h3 class="text-lg font-black text-white">External Relations Overview</h3>
                        <p class="text-xs text-blue-200">
                            <strong>{{ $roleStats['activePartnerships'] ?? 0 }}</strong> active partnerships · <strong>{{ $roleStats['partnerOrgsCount'] ?? 0 }}</strong> partner organizations · <strong>{{ $roleStats['pendingPartnerships'] ?? 0 }}</strong> proposals awaiting review.
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 shrink-0">
                        <a href="{{ route('partnerships.create') }}" class="px-3.5 py-2 bg-blue-400 hover:bg-blue-300 text-blue-950 font-bold text-xs rounded-xl transition shadow-xs">
                            + Propose Partnership
                        </a>
                        <a href="{{ route('partner-organizations.create') }}" class="px-3.5 py-2 bg-white/10 hover:bg-white/20 text-white font-bold text-xs rounded-xl border border-white/20 transition">
                            + Register Partner Org
                        </a>
                        <a href="{{ route('partnerships.index') }}" class="px-3.5 py-2 bg-white/10 hover:bg-white/20 text-white font-bold text-xs rounded-xl border border-white/20 transition">
                            View Partnerships Hub →
                        </a>
                    </div>
                </div>
            </div>
        @elseif($user->isTreasurer())
            <div class="bg-gradient-to-r from-emerald-900 to-teal-950 border border-emerald-800 text-white rounded-2xl p-5 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded text-[10px] font-black bg-emerald-400 text-emerald-950 uppercase">Treasurer Desk</span>
                            <span class="text-xs text-emerald-200">Financial &amp; Membership Operations</span>
                        </div>
                        <h3 class="text-lg font-black text-white">Membership Dues &amp; Ledger</h3>
                        <p class="text-xs text-emerald-200">
                            <strong>{{ $roleStats['activeMemberships'] ?? 0 }}</strong> active verified members · <strong>{{ $roleStats['pendingMemberships'] ?? 0 }}</strong> membership applications pending payment confirmation.
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 shrink-0">
                        <a href="{{ route('admin.memberships.index') }}" class="px-3.5 py-2 bg-emerald-400 hover:bg-emerald-300 text-emerald-950 font-bold text-xs rounded-xl transition shadow-xs">
                            Verify Memberships →
                        </a>
                        <a href="{{ route('analytics.export.memberships') }}" class="px-3.5 py-2 bg-white/10 hover:bg-white/20 text-white font-bold text-xs rounded-xl border border-white/20 transition">
                            Export Membership Report ↓
                        </a>
                    </div>
                </div>
            </div>
        @elseif($user->isAuditor())
            <div class="bg-gradient-to-r from-purple-900 to-indigo-950 border border-purple-800 text-white rounded-2xl p-5 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded text-[10px] font-black bg-purple-400 text-purple-950 uppercase">Auditor Desk</span>
                            <span class="text-xs text-purple-200">Compliance &amp; System Integrity</span>
                        </div>
                        <h3 class="text-lg font-black text-white">Audit Trail &amp; Verification Console</h3>
                        <p class="text-xs text-purple-200">
                            <strong>{{ $roleStats['totalAuditLogs'] ?? 0 }}</strong> recorded system transactions and state transitions tracked in the audit trail.
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 shrink-0">
                        <a href="{{ route('admin.audit-logs.index') }}" class="px-3.5 py-2 bg-purple-400 hover:bg-purple-300 text-purple-950 font-bold text-xs rounded-xl transition shadow-xs">
                            Inspect Audit Logs →
                        </a>
                        <a href="{{ route('admin.memberships.index') }}" class="px-3.5 py-2 bg-white/10 hover:bg-white/20 text-white font-bold text-xs rounded-xl border border-white/20 transition">
                            Audit Memberships Roll
                        </a>
                    </div>
                </div>
            </div>
        @elseif($user->isSecretary())
            <div class="bg-gradient-to-r from-amber-900 to-yellow-950 border border-amber-800 text-white rounded-2xl p-5 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded text-[10px] font-black bg-amber-400 text-amber-950 uppercase">Secretary Desk</span>
                            <span class="text-xs text-amber-200">Documentation &amp; Certificate Registry</span>
                        </div>
                        <h3 class="text-lg font-black text-white">Records &amp; Resolutions Registry</h3>
                        <p class="text-xs text-amber-200">
                            <strong>{{ $roleStats['totalDocuments'] ?? 0 }}</strong> documents archived · <strong>{{ $roleStats['issuedCertificates'] ?? 0 }}</strong> official event certificates issued to date.
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 shrink-0">
                        <a href="{{ route('documents.create') }}" class="px-3.5 py-2 bg-amber-400 hover:bg-amber-300 text-amber-950 font-bold text-xs rounded-xl transition shadow-xs">
                            + Upload Meeting Minutes / Doc
                        </a>
                        <a href="{{ route('documents.index') }}" class="px-3.5 py-2 bg-white/10 hover:bg-white/20 text-white font-bold text-xs rounded-xl border border-white/20 transition">
                            Documents Hub →
                        </a>
                    </div>
                </div>
            </div>
        @elseif($user->isPio())
            <div class="bg-gradient-to-r from-rose-900 to-pink-950 border border-rose-800 text-white rounded-2xl p-5 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded text-[10px] font-black bg-rose-400 text-rose-950 uppercase">PIO Desk</span>
                            <span class="text-xs text-rose-200">Public Relations &amp; Community Dispatch</span>
                        </div>
                        <h3 class="text-lg font-black text-white">Communications &amp; Moderation Center</h3>
                        <p class="text-xs text-rose-200">
                            <strong>{{ $roleStats['officialPosts'] ?? 0 }}</strong> official announcements broadcasted · <strong>{{ $roleStats['totalPosts'] ?? 0 }}</strong> community discussions published.
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 shrink-0">
                        <a href="{{ route('community.index') }}" class="px-3.5 py-2 bg-rose-400 hover:bg-rose-300 text-rose-950 font-bold text-xs rounded-xl transition shadow-xs">
                            + Post Official Announcement
                        </a>
                        <a href="{{ route('moderation.index') }}" class="px-3.5 py-2 bg-white/10 hover:bg-white/20 text-white font-bold text-xs rounded-xl border border-white/20 transition">
                            Open Moderation Console →
                        </a>
                    </div>
                </div>
            </div>
        @elseif($user->isProgramRep())
            <div class="bg-gradient-to-r from-cyan-900 to-sky-950 border border-cyan-800 text-white rounded-2xl p-5 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded text-[10px] font-black bg-cyan-400 text-cyan-950 uppercase">Program Representative Desk</span>
                            <span class="text-xs text-cyan-200">{{ $roleStats['programName'] ?? 'Department' }} Cohort</span>
                        </div>
                        <h3 class="text-lg font-black text-white">{{ $roleStats['programName'] ?? 'Department' }} Student Representation</h3>
                        <p class="text-xs text-cyan-200">
                            <strong>{{ $roleStats['cohortMembers'] ?? 0 }}</strong> active registered members in your department cohort.
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 shrink-0">
                        <a href="{{ route('events.index') }}" class="px-3.5 py-2 bg-cyan-400 hover:bg-cyan-300 text-cyan-950 font-bold text-xs rounded-xl transition shadow-xs">
                            Browse Events →
                        </a>
                        <a href="{{ route('tasks.my-tasks') }}" class="px-3.5 py-2 bg-white/10 hover:bg-white/20 text-white font-bold text-xs rounded-xl border border-white/20 transition">
                            My Assigned Tasks →
                        </a>
                    </div>
                </div>
            </div>
        @endif

        {{-- ===== TWO-COLUMN: Active Events & Committee Tasks ===== --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            {{-- Organization Events Operations --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-gray-900 text-base flex items-center gap-2">
                        <svg class="w-4 h-4 text-comsoc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Organization Events
                    </h3>
                    <a href="{{ route('events.manage') }}" class="text-xs font-bold text-comsoc-600 hover:text-comsoc-800 transition">
                        Manage All →
                    </a>
                </div>

                <div class="space-y-3">
                    @forelse($activeEvents as $evt)
                        <div class="p-3.5 rounded-xl border border-gray-100 bg-gray-50/50 flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <h4 class="font-bold text-gray-900 text-sm truncate">{{ $evt->title }}</h4>
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full {{ $evt->getStatusColor() }} shrink-0">
                                        {{ $evt->getStatusLabel() }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $evt->event_date->format('M d, Y') }} · {{ $evt->registeredCount() }} Registered
                                </p>
                            </div>
                            <a href="{{ route('events.show', $evt) }}"
                               class="px-3 py-1.5 bg-comsoc-600 hover:bg-comsoc-700 text-white text-xs font-bold rounded-lg transition shrink-0">
                                Operations →
                            </a>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 italic text-center py-4">No events currently recorded.</p>
                    @endforelse
                </div>
            </div>

            {{-- My Assigned Tasks --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-gray-900 text-base flex items-center gap-2">
                        <svg class="w-4 h-4 text-comsoc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        My Assigned Tasks
                    </h3>
                    <a href="{{ route('tasks.my-tasks') }}" class="text-xs font-bold text-comsoc-600 hover:text-comsoc-800 transition">
                        Task Board →
                    </a>
                </div>

                <div class="space-y-3">
                    @forelse($myTasks as $tsk)
                        <div class="p-3.5 rounded-xl border border-gray-100 bg-gray-50/50 flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <h4 class="font-bold text-gray-900 text-sm truncate">{{ $tsk->title }}</h4>
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full {{ $tsk->getPriorityColor() }} shrink-0">
                                        {{ ucfirst($tsk->priority) }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    {{ $tsk->event ? $tsk->event->title : 'General' }} · Due: {{ $tsk->due_date ? $tsk->due_date->format('M d') : 'None' }}
                                </p>
                            </div>
                            <span class="text-xs font-bold text-gray-700 uppercase tracking-wider shrink-0">
                                {{ str_replace('_', ' ', $tsk->status) }}
                            </span>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 italic text-center py-4">No pending tasks assigned to you right now.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- ===== DOCUMENT PIPELINE & RECENT COMMUNITY ===== --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            {{-- Document Pipeline --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-gray-900 text-base flex items-center gap-2">
                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Document Approvals Pipeline
                    </h3>
                    <a href="{{ route('documents.index') }}" class="text-xs font-bold text-comsoc-600 hover:text-comsoc-800 transition">
                        Documents Hub →
                    </a>
                </div>

                <div class="space-y-3">
                    @forelse($pendingDocs as $doc)
                        <div class="p-3.5 rounded-xl border border-amber-100 bg-amber-50/30 flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <h4 class="font-bold text-gray-900 text-sm truncate">{{ $doc->title }}</h4>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    {{ $doc->getCategoryLabel() }} · Uploaded by {{ $doc->uploader->name }}
                                </p>
                            </div>
                            <a href="{{ route('documents.show', $doc) }}"
                               class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-lg transition shrink-0">
                                View Doc
                            </a>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 italic text-center py-4">No documents currently pending review.</p>
                    @endforelse
                </div>
            </div>

            {{-- Recent Community Feed --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-gray-900 text-base flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        Recent Community Activity
                    </h3>
                    <a href="{{ route('community.index') }}" class="text-xs font-bold text-comsoc-600 hover:text-comsoc-800 transition">
                        Community Feed →
                    </a>
                </div>

                <div class="space-y-3">
                    @forelse($recentPosts as $post)
                        <div class="p-3.5 rounded-xl border border-gray-100 bg-gray-50/50 flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p class="font-bold text-gray-900 text-sm truncate">{{ Str::limit($post->body, 60) }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    By {{ $post->author ? $post->author->name : 'Member' }} · {{ $post->created_at->diffForHumans() }}
                                </p>
                            </div>
                            <span class="text-xs text-gray-500 font-semibold shrink-0 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                </svg>
                                {{ $post->likesCount() }}
                            </span>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 italic text-center py-4">No community posts yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</x-dashboard-layout>
