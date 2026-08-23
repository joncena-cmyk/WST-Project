@php
    $user = Auth::user();

    $pendingApprovalsCount = ($user->isAdviser() || $user->isAdmin())
        ? \App\Models\Event::whereIn('status', [\App\Models\Event::STATUS_SUBMITTED, \App\Models\Event::STATUS_UNDER_REVIEW])->count()
        : 0;

    $mainNav = [
        ['label'=>'Dashboard',    'route'=>'dashboard',          'icon'=>'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
        ['label'=>'Community',    'route'=>'community.index',    'icon'=>'M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z'],
        ['label'=>'Events',       'route'=>'events.index',       'icon'=>'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
    ];

    if ($user->isAdviser() || $user->isAdmin()) {
        $mainNav[] = [
            'label' => 'Event Approvals',
            'route' => 'events.approval.index',
            'icon'  => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
            'badge' => $pendingApprovalsCount,
        ];
    }

    $mainNav[] = ['label'=>'Membership',   'route'=>'membership.index',   'icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'];
    $mainNav[] = ['label'=>'Tasks',        'route'=>'tasks.my-tasks',     'icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'];
    $mainNav[] = ['label'=>'Certificates', 'route'=>'certificates.index', 'icon'=>'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z'];

    $comingSoon = [
        ['label'=>'Documents','icon'=>'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        ['label'=>'Reports',  'icon'=>'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
    ];

    $managementNav = [];
    if ($user->isOfficerOrAbove()) {
        $managementNav[] = ['label'=>'Moderation',   'route'=>'moderation.index',       'icon'=>'M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9'];
        $managementNav[] = ['label'=>'Memberships',  'route'=>'admin.memberships.index','icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'];
    }

    $adminNav = [];
    if ($user->isAdmin()) {
        $adminNav[] = ['label'=>'Users','route'=>'admin.users.index','icon'=>'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'];
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

    {{-- ===== USER STRIP — compact, no redundancy ===== --}}
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
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </a>
        </div>
    </div>

    {{-- ===== NAVIGATION ===== --}}
    <nav class="flex-1 px-3 pt-2 pb-2 space-y-0.5 overflow-y-auto min-h-0">

        {{-- Main nav --}}
        @foreach ($mainNav as $item)
            @php $isActive = request()->routeIs($item['route']); @endphp
            <a href="{{ route($item['route']) }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all
                      {{ $isActive
                          ? 'bg-comsoc-700/80 text-white shadow-sm nav-active'
                          : 'text-comsoc-300 hover:bg-comsoc-800/60 hover:text-white' }}">
                <svg class="w-4.5 h-4.5 shrink-0" style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

        {{-- Coming soon --}}
        @foreach ($comingSoon as $item)
            <span class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-comsoc-700 cursor-not-allowed">
                <svg style="width:18px;height:18px" class="shrink-0 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                </svg>
                {{ $item['label'] }}
                <span class="ml-auto text-[9px] bg-comsoc-900 text-comsoc-600 px-1.5 py-0.5 rounded font-bold tracking-wide uppercase">Soon</span>
            </span>
        @endforeach

        {{-- Management (officers+) --}}
        @if(count($managementNav))
            <div class="pt-4 pb-1.5">
                <p class="px-3 text-[9px] font-black text-comsoc-600 uppercase tracking-widest">Management</p>
            </div>
            @foreach ($managementNav as $item)
                @php $isActive = request()->routeIs($item['route']); @endphp
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all
                          {{ $isActive
                              ? 'bg-comsoc-700/80 text-white shadow-sm nav-active'
                              : 'text-comsoc-300 hover:bg-comsoc-800/60 hover:text-white' }}">
                    <svg style="width:18px;height:18px" class="shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $isActive ? '2.5' : '2' }}" d="{{ $item['icon'] }}"/>
                    </svg>
                    <span class="{{ $isActive ? 'font-semibold' : '' }}">{{ $item['label'] }}</span>
                </a>
            @endforeach
        @endif

        {{-- Admin --}}
        @if(count($adminNav))
            <div class="pt-4 pb-1.5">
                <p class="px-3 text-[9px] font-black text-comsoc-600 uppercase tracking-widest">Administration</p>
            </div>
            @foreach ($adminNav as $item)
                @php $isActive = request()->routeIs($item['route'].'*'); @endphp
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all
                          {{ $isActive
                              ? 'bg-comsoc-700/80 text-white shadow-sm nav-active'
                              : 'text-comsoc-300 hover:bg-comsoc-800/60 hover:text-white' }}">
                    <svg style="width:18px;height:18px" class="shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $isActive ? '2.5' : '2' }}" d="{{ $item['icon'] }}"/>
                    </svg>
                    <span class="{{ $isActive ? 'font-semibold' : '' }}">{{ $item['label'] }}</span>
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
