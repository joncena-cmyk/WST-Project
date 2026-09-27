@php
    $user = Auth::user();

    // ─── Shared icon paths ─────────────────────────────────────────────────────
    $icons = [
        'dashboard'    => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
        'community'    => 'M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z',
        'events'       => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
        'membership'   => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
        'certificates' => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z',
        'elections'    => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        'tasks'        => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
        'documents'    => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'partnerships' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
        'analytics'    => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
        'moderation'   => 'M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9',
        'memberships'  => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
        'audit'        => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
        'users'        => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
        'approvals'    => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
        'cohort'       => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
    ];

    // ─── Badge counts (computed only when needed) ──────────────────────────────
    $pendingApprovalsCount = 0;
    $pendingDocsCount      = 0;
    $pendingMembersCount   = 0;

    if ($user->isAdviser() || $user->isAdmin() || $user->isPresident()) {
        $pendingApprovalsCount = \App\Models\Event::whereIn('status', [
            \App\Models\Event::STATUS_SUBMITTED,
            \App\Models\Event::STATUS_UNDER_REVIEW,
        ])->count();
    }

    if ($user->isAdviser() || $user->isAdmin() || $user->isPresident() || $user->isSecretary()) {
        $pendingDocsCount = \App\Models\Document::whereIn('status', [
            \App\Models\Document::STATUS_SUBMITTED,
            \App\Models\Document::STATUS_UNDER_REVIEW,
        ])->count();
    }

    if ($user->isTreasurer() || $user->isAdmin() || $user->isPresident()) {
        $pendingMembersCount = \App\Models\Membership::where('status', \App\Models\Membership::STATUS_PENDING)->count();
    }

    // ─── Build nav sections ────────────────────────────────────────────────────
    // Every role always sees these three core items
    $coreNav = [
        ['label' => 'Dashboard', 'route' => 'dashboard',      'icon' => $icons['dashboard']],
        ['label' => 'Community', 'route' => 'community.index', 'icon' => $icons['community']],
        ['label' => 'Events',    'route' => 'events.index',    'icon' => $icons['events']],
    ];

    // ─── Desk nav (role-specific work modules) ─────────────────────────────────
    $deskNav = [];

    // ── Students & non-officer members ──────────────────────────────────────────
    if (in_array($user->role, [\App\Models\User::ROLE_STUDENT, \App\Models\User::ROLE_MEMBER])) {
        $deskNav[] = ['label' => 'My Membership',  'route' => 'membership.index',   'icon' => $icons['membership']];
        $deskNav[] = ['label' => 'My Certificates', 'route' => 'certificates.index', 'icon' => $icons['certificates']];
        $deskNav[] = ['label' => 'Elections',       'route' => 'elections.index',    'icon' => $icons['elections']];
    }

    // ── Program Representatives ─────────────────────────────────────────────────
    if ($user->isProgramRep()) {
        $deskNav[] = ['label' => 'My Tasks',        'route' => 'tasks.my-tasks',     'icon' => $icons['tasks']];
        $deskNav[] = ['label' => 'My Certificates', 'route' => 'certificates.index', 'icon' => $icons['certificates']];
        $deskNav[] = ['label' => 'Elections',       'route' => 'elections.index',    'icon' => $icons['elections']];
        $deskNav[] = ['label' => 'My Membership',   'route' => 'membership.index',   'icon' => $icons['membership']];
    }

    // ── President ───────────────────────────────────────────────────────────────
    if ($user->isPresident()) {
        $deskNav[] = ['label' => 'Tasks',        'route' => 'tasks.my-tasks',          'icon' => $icons['tasks']];
        $deskNav[] = ['label' => 'Documents',    'route' => 'documents.index',          'icon' => $icons['documents'],    'badge' => $pendingDocsCount];
        $deskNav[] = ['label' => 'Certificates', 'route' => 'certificates.index',       'icon' => $icons['certificates']];
        $deskNav[] = ['label' => 'Partnerships', 'route' => 'partnerships.index',       'icon' => $icons['partnerships']];
        $deskNav[] = ['label' => 'Elections',    'route' => 'elections.index',          'icon' => $icons['elections']];
        $deskNav[] = ['label' => 'Analytics',    'route' => 'analytics.index',          'icon' => $icons['analytics']];
        $deskNav[] = ['label' => 'Event Approvals', 'route' => 'events.approval.index', 'icon' => $icons['approvals'],   'badge' => $pendingApprovalsCount];
    }

    // ── VP Internal ─────────────────────────────────────────────────────────────
    if ($user->isVpInternal()) {
        $deskNav[] = ['label' => 'Tasks',        'route' => 'tasks.my-tasks',   'icon' => $icons['tasks']];
        $deskNav[] = ['label' => 'Documents',    'route' => 'documents.index',  'icon' => $icons['documents']];
        $deskNav[] = ['label' => 'Elections',    'route' => 'elections.index',  'icon' => $icons['elections']];
        $deskNav[] = ['label' => 'Analytics',    'route' => 'analytics.index',  'icon' => $icons['analytics']];
    }

    // ── VP External ─────────────────────────────────────────────────────────────
    if ($user->isVpExternal()) {
        $deskNav[] = ['label' => 'Tasks',        'route' => 'tasks.my-tasks',         'icon' => $icons['tasks']];
        $deskNav[] = ['label' => 'Partnerships', 'route' => 'partnerships.index',     'icon' => $icons['partnerships']];
        $deskNav[] = ['label' => 'Documents',    'route' => 'documents.index',        'icon' => $icons['documents']];
        $deskNav[] = ['label' => 'Elections',    'route' => 'elections.index',        'icon' => $icons['elections']];
        $deskNav[] = ['label' => 'Analytics',    'route' => 'analytics.index',        'icon' => $icons['analytics']];
    }

    // ── Secretary ───────────────────────────────────────────────────────────────
    if ($user->isSecretary()) {
        $deskNav[] = ['label' => 'Tasks',           'route' => 'tasks.my-tasks',    'icon' => $icons['tasks']];
        $deskNav[] = ['label' => 'Documents',       'route' => 'documents.index',   'icon' => $icons['documents'],    'badge' => $pendingDocsCount];
        $deskNav[] = ['label' => 'Certificates',    'route' => 'certificates.index','icon' => $icons['certificates']];
        $deskNav[] = ['label' => 'Elections',       'route' => 'elections.index',   'icon' => $icons['elections']];
        $deskNav[] = ['label' => 'Analytics',       'route' => 'analytics.index',   'icon' => $icons['analytics']];
    }

    // ── Treasurer ───────────────────────────────────────────────────────────────
    if ($user->isTreasurer()) {
        $deskNav[] = ['label' => 'Tasks',       'route' => 'tasks.my-tasks',          'icon' => $icons['tasks']];
        $deskNav[] = ['label' => 'Memberships', 'route' => 'admin.memberships.index', 'icon' => $icons['memberships'], 'badge' => $pendingMembersCount];
        $deskNav[] = ['label' => 'Analytics',   'route' => 'analytics.index',         'icon' => $icons['analytics']];
    }

    // ── Auditor ─────────────────────────────────────────────────────────────────
    if ($user->isAuditor()) {
        $deskNav[] = ['label' => 'Tasks',       'route' => 'tasks.my-tasks',  'icon' => $icons['tasks']];
        $deskNav[] = ['label' => 'Analytics',   'route' => 'analytics.index', 'icon' => $icons['analytics']];
    }

    // ── PIO ─────────────────────────────────────────────────────────────────────
    if ($user->isPio()) {
        $deskNav[] = ['label' => 'Tasks',       'route' => 'tasks.my-tasks',    'icon' => $icons['tasks']];
        $deskNav[] = ['label' => 'Moderation',  'route' => 'moderation.index',  'icon' => $icons['moderation']];
    }

    // ── Generic Officer (fallback) ────────────────────────────────────────────
    if ($user->hasRole(\App\Models\User::ROLE_OFFICER)) {
        $deskNav[] = ['label' => 'Tasks',        'route' => 'tasks.my-tasks',   'icon' => $icons['tasks']];
        $deskNav[] = ['label' => 'Documents',    'route' => 'documents.index',  'icon' => $icons['documents']];
        $deskNav[] = ['label' => 'Certificates', 'route' => 'certificates.index','icon' => $icons['certificates']];
        $deskNav[] = ['label' => 'Partnerships', 'route' => 'partnerships.index','icon' => $icons['partnerships']];
        $deskNav[] = ['label' => 'Elections',    'route' => 'elections.index',   'icon' => $icons['elections']];
        $deskNav[] = ['label' => 'Analytics',    'route' => 'analytics.index',   'icon' => $icons['analytics']];
    }

    // ── Adviser ─────────────────────────────────────────────────────────────────
    if ($user->isAdviser()) {
        $deskNav[] = ['label' => 'Event Approvals', 'route' => 'events.approval.index', 'icon' => $icons['approvals'],  'badge' => $pendingApprovalsCount];
        $deskNav[] = ['label' => 'Documents',       'route' => 'documents.index',       'icon' => $icons['documents'],  'badge' => $pendingDocsCount];
        $deskNav[] = ['label' => 'Partnerships',    'route' => 'partnerships.index',    'icon' => $icons['partnerships']];
        $deskNav[] = ['label' => 'Analytics',       'route' => 'analytics.index',       'icon' => $icons['analytics']];
    }

    // ─── Governance / Compliance nav ────────────────────────────────────────────
    // Items that appear in a separate "Governance" section below the desk
    $governanceNav = [];

    if ($user->isAdmin() || $user->isAuditor() || $user->isPresident() || $user->isAdviser()) {
        $governanceNav[] = ['label' => 'Audit Logs', 'route' => 'admin.audit-logs.index', 'icon' => $icons['audit']];
    }
    if ($user->isPio() || $user->isPresident() || $user->isAdmin()) {
        $governanceNav[] = ['label' => 'Moderation', 'route' => 'moderation.index', 'icon' => $icons['moderation']];
    }
    if ($user->isTreasurer() || $user->isPresident() || $user->isAdmin()) {
        // Only show if not already in deskNav (Treasurer has it there)
        if (!$user->isTreasurer()) {
            $governanceNav[] = ['label' => 'Memberships', 'route' => 'admin.memberships.index', 'icon' => $icons['memberships'], 'badge' => $pendingMembersCount];
        }
    }

    // ─── Admin-only section ────────────────────────────────────────────────────
    $adminNav = [];
    if ($user->isAdmin()) {
        $adminNav[] = ['label' => 'Manage Users',  'route' => 'admin.users.index',       'icon' => $icons['users']];
        $adminNav[] = ['label' => 'Memberships',   'route' => 'admin.memberships.index', 'icon' => $icons['memberships'], 'badge' => $pendingMembersCount];
        $adminNav[] = ['label' => 'Moderation',    'route' => 'moderation.index',        'icon' => $icons['moderation']];
        $adminNav[] = ['label' => 'Audit Logs',    'route' => 'admin.audit-logs.index',  'icon' => $icons['audit']];
        $adminNav[] = ['label' => 'Analytics',     'route' => 'analytics.index',         'icon' => $icons['analytics']];
    }
@endphp

<aside class="flex flex-col w-64 h-full bg-comsoc-950 text-white select-none">

    {{-- ===== BRAND ===== --}}
    <div class="flex items-center gap-3 px-5 py-4 border-b border-comsoc-800/60 shrink-0">
        <img src="{{ asset('images/comsoc_logo.png') }}"
             alt="COMSOC"
             class="w-9 h-9 object-contain shrink-0"/>
        <div class="min-w-0">
            <p class="text-sm font-black text-white tracking-tight leading-tight truncate">COMSOC Hub</p>
            <p class="text-[10px] text-gold-500 font-semibold truncate">CCIS · Univ. of Makati</p>
        </div>
    </div>

    {{-- ===== USER STRIP ===== --}}
    <div class="px-4 py-3 border-b border-comsoc-800/50 shrink-0">
        <div class="flex items-center gap-2.5">
            <img src="{{ $user->getProfilePictureUrl() }}"
                 alt="{{ $user->name }}"
                 class="w-8 h-8 rounded-full object-cover border border-comsoc-700 shrink-0"/>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-bold text-white truncate">{{ $user->name }}</p>
                <p class="text-[10px] text-gold-500 truncate">{{ $user->getRoleLabel() }}</p>
            </div>
            <a href="{{ route('profile.edit') }}"
               class="shrink-0 p-1 text-comsoc-500 hover:text-white transition-colors"
               title="Profile Settings">
                <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </a>
        </div>
    </div>

    {{-- ===== NAVIGATION ===== --}}
    <nav class="flex-1 px-3 pt-2 pb-2 space-y-0.5 overflow-y-auto min-h-0">

        {{-- Core nav (all roles) --}}
        @foreach ($coreNav as $item)
            @php $isActive = request()->routeIs($item['route']); @endphp
            <a href="{{ route($item['route']) }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all
                      {{ $isActive ? 'bg-comsoc-700/80 text-white shadow-sm' : 'text-comsoc-300 hover:bg-comsoc-800/60 hover:text-white' }}">
                <svg style="width:18px;height:18px" class="shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $isActive ? '2.5' : '2' }}" d="{{ $item['icon'] }}"/>
                </svg>
                <span class="{{ $isActive ? 'font-semibold' : '' }}">{{ $item['label'] }}</span>
                @if($isActive)
                    <span class="ml-auto w-1.5 h-1.5 rounded-full bg-gold-400 shrink-0"></span>
                @endif
            </a>
        @endforeach

        {{-- Desk nav (role-specific) --}}
        @if(count($deskNav))
            <div class="pt-4 pb-1.5">
                <p class="px-3 text-[9px] font-black text-comsoc-600 uppercase tracking-widest">
                    @if($user->isAdviser())
                        Governance
                    @elseif($user->isOfficer())
                        Officer Desk
                    @else
                        My Account
                    @endif
                </p>
            </div>
            @foreach ($deskNav as $item)
                @php $isActive = request()->routeIs($item['route']); @endphp
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all
                          {{ $isActive ? 'bg-comsoc-700/80 text-white shadow-sm' : 'text-comsoc-300 hover:bg-comsoc-800/60 hover:text-white' }}">
                    <svg style="width:18px;height:18px" class="shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $isActive ? '2.5' : '2' }}" d="{{ $item['icon'] }}"/>
                    </svg>
                    <span class="{{ $isActive ? 'font-semibold' : '' }}">{{ $item['label'] }}</span>
                    @if(isset($item['badge']) && $item['badge'] > 0)
                        <span class="ml-auto px-2 py-0.5 text-xs font-black bg-amber-400 text-comsoc-950 rounded-full shrink-0 shadow-xs">
                            {{ $item['badge'] }}
                        </span>
                    @elseif($isActive)
                        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-gold-400 shrink-0"></span>
                    @endif
                </a>
            @endforeach
        @endif

        {{-- Governance / Compliance (Auditor, President, Adviser) --}}
        @if(count($governanceNav) && !$user->isAdmin())
            <div class="pt-4 pb-1.5">
                <p class="px-3 text-[9px] font-black text-comsoc-600 uppercase tracking-widest">Compliance</p>
            </div>
            @foreach ($governanceNav as $item)
                @php $isActive = request()->routeIs($item['route'].'*'); @endphp
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all
                          {{ $isActive ? 'bg-comsoc-700/80 text-white shadow-sm' : 'text-comsoc-300 hover:bg-comsoc-800/60 hover:text-white' }}">
                    <svg style="width:18px;height:18px" class="shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $isActive ? '2.5' : '2' }}" d="{{ $item['icon'] }}"/>
                    </svg>
                    <span class="{{ $isActive ? 'font-semibold' : '' }}">{{ $item['label'] }}</span>
                    @if(isset($item['badge']) && $item['badge'] > 0)
                        <span class="ml-auto px-2 py-0.5 text-xs font-black bg-amber-400 text-comsoc-950 rounded-full shrink-0 shadow-xs">
                            {{ $item['badge'] }}
                        </span>
                    @elseif($isActive)
                        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-gold-400 shrink-0"></span>
                    @endif
                </a>
            @endforeach
        @endif

        {{-- Admin full section --}}
        @if(count($adminNav))
            <div class="pt-4 pb-1.5">
                <p class="px-3 text-[9px] font-black text-comsoc-600 uppercase tracking-widest">Administration</p>
            </div>
            @foreach ($adminNav as $item)
                @php $isActive = request()->routeIs($item['route'].'*'); @endphp
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all
                          {{ $isActive ? 'bg-comsoc-700/80 text-white shadow-sm' : 'text-comsoc-300 hover:bg-comsoc-800/60 hover:text-white' }}">
                    <svg style="width:18px;height:18px" class="shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $isActive ? '2.5' : '2' }}" d="{{ $item['icon'] }}"/>
                    </svg>
                    <span class="{{ $isActive ? 'font-semibold' : '' }}">{{ $item['label'] }}</span>
                    @if(isset($item['badge']) && $item['badge'] > 0)
                        <span class="ml-auto px-2 py-0.5 text-xs font-black bg-amber-400 text-comsoc-950 rounded-full shrink-0 shadow-xs">
                            {{ $item['badge'] }}
                        </span>
                    @elseif($isActive)
                        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-gold-400 shrink-0"></span>
                    @endif
                </a>
            @endforeach
        @endif

    </nav>

    {{-- ===== SIGN OUT ===== --}}
    <div class="px-3 py-3 border-t border-comsoc-800/60 shrink-0">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-xs text-comsoc-500 hover:bg-red-900/25 hover:text-red-300 transition-all">
                <svg style="width:16px;height:16px" class="shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Sign Out
            </button>
        </form>
    </div>
</aside>
