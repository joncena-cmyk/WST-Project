<x-dashboard-layout>
    <x-slot name="header">
        <h1 class="text-base font-bold text-gray-900">Dashboard</h1>
    </x-slot>

    @php
        $upcomingCount    = \App\Models\Event::whereIn('status',['published','ongoing'])->where('event_date','>=',now())->count();
        $registeredCount  = \App\Models\EventRegistration::where('user_id',Auth::id())->where('status','registered')->count();
        $activeMembership = Auth::user()->activeMembership();
    @endphp

    {{-- ===== HERO WELCOME BANNER ===== --}}
    <div class="relative rounded-2xl overflow-hidden mb-6 shadow-md" style="min-height:160px;">
        {{-- Background --}}
        <div class="absolute inset-0">
            <img src="{{ asset('images/hero_logo.jpg') }}" alt="" class="w-full h-full object-cover"/>
        </div>
        <div class="absolute inset-0" style="background:linear-gradient(135deg,rgba(5,46,22,0.92) 0%,rgba(5,46,22,0.75) 50%,rgba(5,46,22,0.50) 100%)"></div>
        <div class="absolute inset-0" style="background-image:linear-gradient(rgba(255,255,255,0.025) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.025) 1px,transparent 1px);background-size:32px 32px"></div>
        {{-- Gold top stripe --}}
        <div class="absolute top-0 left-0 right-0 h-0.5 bg-gradient-to-r from-gold-600 via-gold-400 to-gold-600"></div>

        <div class="relative z-10 flex items-center gap-5 p-6">
            <div class="relative shrink-0">
                <img src="{{ Auth::user()->getProfilePictureUrl() }}"
                     alt="{{ Auth::user()->name }}"
                     class="w-16 h-16 rounded-full object-cover border-2 border-gold-400 shadow-lg"/>
                <span class="absolute bottom-0 right-0 w-4 h-4 rounded-full bg-green-400 border-2 border-comsoc-900 shadow"></span>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-semibold text-gold-400 uppercase tracking-widest mb-0.5">Welcome back</p>
                <h2 class="text-xl font-black text-white truncate">{{ Auth::user()->name }}</h2>
                <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-comsoc-700/80 border border-comsoc-600/50 text-white uppercase tracking-wide">
                        {{ Auth::user()->getRoleLabel() }}
                    </span>
                    @if(Auth::user()->program)
                        <span class="text-comsoc-300 text-xs font-medium">{{ Auth::user()->program->code }}</span>
                    @endif
                    @if(Auth::user()->student_id)
                        <span class="text-comsoc-400 text-xs">· {{ Auth::user()->student_id }}</span>
                    @endif
                </div>
            </div>
            {{-- COMSOC logo watermark --}}
            <div class="ml-auto shrink-0 hidden sm:block opacity-20">
                <img src="{{ asset('images/comsoc_logo.png') }}" alt="" class="w-16 h-16 object-contain"/>
            </div>
        </div>
    </div>

    {{-- ===== QUICK STATS ===== --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @foreach([
            ['Upcoming Events',   $upcomingCount,    'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',  'bg-comsoc-50 text-comsoc-600', 'border-comsoc-100'],
            ['My Registrations', $registeredCount,  'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'bg-blue-50 text-blue-600', 'border-blue-100'],
            ['Membership',        $activeMembership ? 'Active' : 'None', 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', $activeMembership ? 'bg-green-50 text-green-600' : 'bg-gray-50 text-gray-500', $activeMembership ? 'border-green-100' : 'border-gray-200'],
            ['Certificates',      '—', 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z', 'bg-yellow-50 text-yellow-600', 'border-yellow-100'],
        ] as [$label, $value, $icon, $iconClass, $borderClass])
            <div class="bg-white rounded-xl border {{ $borderClass }} p-4 shadow-sm flex items-center gap-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-xl {{ $iconClass }} shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide truncate">{{ $label }}</p>
                    <p class="text-xl font-black text-gray-900 leading-tight mt-0.5">{{ $value }}</p>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ===== QUICK ACTIONS ===== --}}
    <div class="mb-6">
        <div class="flex items-center gap-2 mb-3">
            <span class="w-1 h-4 rounded-full bg-comsoc-600"></span>
            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide">Quick Access</h3>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            @foreach([
                [route('community.index'), 'Community', 'Posts, announcements, discussions', 'from-comsoc-700 to-comsoc-900', 'M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z'],
                [route('events.index'),    'Events',    'Browse & register for events',       'from-blue-700 to-blue-900',    'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                [route('membership.index'),'Membership','View status and history',             'from-purple-700 to-purple-900','M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
            ] as [$href, $title, $desc, $gradient, $icon])
                <a href="{{ $href }}"
                   class="group relative rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-200">
                    {{-- Gradient background --}}
                    <div class="absolute inset-0 bg-gradient-to-br {{ $gradient }}"></div>
                    <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity"
                         style="background:linear-gradient(rgba(255,255,255,0.05) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.05) 1px,transparent 1px);background-size:20px 20px"></div>
                    <div class="relative z-10 p-5 flex items-center gap-4">
                        <div class="flex items-center justify-center w-11 h-11 rounded-xl bg-white/15 shrink-0">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-white">{{ $title }}</p>
                            <p class="text-xs text-white/70 mt-0.5 truncate">{{ $desc }}</p>
                        </div>
                        <svg class="w-4 h-4 text-white/50 group-hover:text-white group-hover:translate-x-0.5 transition-all shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </a>
            @endforeach
        </div>
    </div>

    {{-- ===== TWO-COLUMN: Upcoming events + Build progress ===== --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- Upcoming events preview --}}
        @php
            $upcomingEvents = \App\Models\Event::whereIn('status',['published','ongoing'])
                ->where('event_date','>=',now())
                ->orderBy('event_date')
                ->take(3)->get();
        @endphp
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <span class="w-1 h-4 rounded-full bg-comsoc-600"></span>
                    <h3 class="text-sm font-bold text-gray-800">Upcoming Events</h3>
                </div>
                <a href="{{ route('events.index') }}" class="text-xs font-semibold text-comsoc-600 hover:text-comsoc-800 transition-colors">View all →</a>
            </div>
            @if($upcomingEvents->isEmpty())
                <div class="px-5 py-8 text-center">
                    <svg class="w-10 h-10 text-gray-200 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-sm text-gray-400 font-medium">No upcoming events</p>
                </div>
            @else
                <div class="divide-y divide-gray-100">
                    @foreach($upcomingEvents as $event)
                        <a href="{{ route('events.show', $event) }}"
                           class="flex items-center gap-4 px-5 py-3.5 hover:bg-gray-50 transition-colors group">
                            <div class="flex flex-col items-center justify-center w-12 h-12 rounded-xl bg-comsoc-700 text-white text-center shrink-0 shadow-sm">
                                <p class="text-lg font-black leading-none">{{ $event->event_date->format('d') }}</p>
                                <p class="text-[9px] font-bold uppercase tracking-wide">{{ $event->event_date->format('M') }}</p>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 truncate group-hover:text-comsoc-700 transition-colors">{{ $event->title }}</p>
                                <p class="text-xs text-gray-400 mt-0.5 truncate">📍 {{ $event->venue }}</p>
                            </div>
                            <span class="inline-flex shrink-0 px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $event->getStatusColor() }}">
                                {{ $event->getStatusLabel() }}
                            </span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Platform build progress --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="flex items-center gap-2 px-5 py-4 border-b border-gray-100">
                <span class="w-1 h-4 rounded-full bg-comsoc-600"></span>
                <h3 class="text-sm font-bold text-gray-800">Platform Progress</h3>
            </div>
            <div class="px-5 py-4 space-y-2.5">
                @foreach([
                    [true,  'Authentication & User Management'],
                    [true,  'Programs, Roles & Membership'],
                    [true,  'User & Membership Management UI'],
                    [true,  'COMSOC Community'],
                    [true,  'Events & Approval Workflow'],
                    [false, 'QR Attendance'],
                    [false, 'Certificates'],
                    [false, 'Committees & Tasks'],
                    [false, 'Documents & Approvals'],
                    [false, 'Reports & Analytics'],
                ] as [$done, $label])
                    <div class="flex items-center gap-2.5 text-sm {{ $done ? 'text-gray-700' : 'text-gray-400' }}">
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full shrink-0 text-[10px] font-bold
                                     {{ $done ? 'bg-comsoc-100 text-comsoc-700' : 'bg-gray-100 text-gray-400' }}">
                            {{ $done ? '✓' : '·' }}
                        </span>
                        {{ $label }}
                    </div>
                @endforeach
            </div>
        </div>
    </div>

</x-dashboard-layout>
