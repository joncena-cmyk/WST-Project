<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>COMSOC Hub — Computer Society · CCIS · University of Makati</title>
    <meta name="description" content="The centralized platform of the Computer Society of CCIS, University of Makati.">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Utility top bar */
        .util-bar { background: #0a2e1a; }

        /* Hero banner */
        .hero-slide { position: relative; }

        /* Ticker / announcement bar */
        @keyframes ticker {
            0%   { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
        }
        .ticker-inner { animation: ticker 28s linear infinite; white-space: nowrap; }
        .ticker-inner:hover { animation-play-state: paused; }

        /* Card hover */
        .news-card { transition: box-shadow .18s ease, transform .18s ease; }
        .news-card:hover { box-shadow: 0 8px 32px rgba(0,0,0,0.10); transform: translateY(-2px); }

        /* Section heading bar */
        .section-bar { border-left: 4px solid #16a34a; padding-left: .75rem; }

        html { scroll-behavior: smooth; }
    </style>
</head>
<body class="font-sans antialiased bg-white text-gray-900" x-data>

{{-- ============================================================
     UTILITY TOP BAR
     ============================================================ --}}
<div class="util-bar hidden md:block">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-9">
            <div class="flex items-center gap-4 text-[11px] text-green-300">
                <span>Computer Society · CCIS · University of Makati</span>
                <span class="text-green-600">|</span>
                <span>Academic Year 2025–2026</span>
            </div>
            <div class="flex items-center gap-4">
                @if(Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-[11px] text-green-300 hover:text-white transition-colors">
                            Go to Dashboard →
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-[11px] text-green-300 hover:text-white transition-colors">Sign in</a>
                        <span class="text-green-700">|</span>
                        <a href="{{ route('register') }}" class="text-[11px] text-green-300 hover:text-white transition-colors">Register</a>
                    @endauth
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ============================================================
     MAIN NAVBAR
     ============================================================ --}}
<nav x-data="{ scrolled: false, mobileOpen: false }"
     x-on:scroll.window="scrolled = window.scrollY > 40"
     :class="scrolled ? 'shadow-md' : 'shadow-sm'"
     class="sticky top-0 z-50 bg-white border-b border-gray-200 transition-shadow duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- Brand --}}
            <a href="/" class="flex items-center gap-3 shrink-0">
                <img src="{{ asset('images/comsoc_logo.png') }}"
                     alt="COMSOC Logo"
                     class="w-10 h-10 object-contain shrink-0"/>
                <div class="leading-none">
                    <span class="block font-black text-comsoc-900 text-lg tracking-tight">COMSOC Hub</span>
                    <span class="block text-[10px] text-gray-400 font-medium">CCIS · Univ. of Makati</span>
                </div>
            </a>

            {{-- Desktop nav --}}
            <div class="hidden lg:flex items-center gap-1">
                @foreach([
                    'About'    => route('about'),
                    'News'     => '#features',
                    'Events'   => '#events-preview',
                    'Officers' => '#officers',
                    'How It Works' => '#news',
                ] as $label => $href)
                    <a href="{{ $href }}"
                       class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-comsoc-700 hover:bg-comsoc-50 rounded-lg transition-colors">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            {{-- Auth CTA --}}
            <div class="flex items-center gap-2">
                @auth
                    <a href="{{ url('/dashboard') }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2 bg-comsoc-700 text-white text-sm font-semibold rounded-lg hover:bg-comsoc-800 transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="hidden sm:inline-flex px-4 py-2 text-sm font-semibold text-comsoc-700 border border-comsoc-300 rounded-lg hover:bg-comsoc-50 transition-colors">
                        Sign in
                    </a>
                    @if(Route::has('register'))
                        <a href="{{ route('register') }}"
                           class="inline-flex items-center px-4 py-2 bg-comsoc-700 text-white text-sm font-semibold rounded-lg hover:bg-comsoc-800 transition-colors shadow-sm">
                            Join COMSOC
                        </a>
                    @endif
                @endauth

                {{-- Mobile menu toggle --}}
                <button @click="mobileOpen = !mobileOpen"
                        class="lg:hidden p-2 text-gray-500 hover:bg-gray-100 rounded-lg ml-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile menu --}}
        <div x-show="mobileOpen" x-cloak class="lg:hidden border-t border-gray-100 py-3 space-y-1">
            @foreach(['About'=>route('about'),'News'=>'#features','Events'=>'#events-preview','Officers'=>'#officers','How It Works'=>'#news'] as $l => $h)
                <a href="{{ $h }}" @click="mobileOpen=false"
                   class="block px-4 py-2 text-sm font-medium text-gray-700 hover:bg-comsoc-50 hover:text-comsoc-700 rounded-lg">{{ $l }}</a>
            @endforeach
        </div>
    </div>
</nav>

{{-- ============================================================
     ANNOUNCEMENT TICKER
     ============================================================ --}}
<div class="bg-comsoc-700 overflow-hidden">
    <div class="flex items-center h-9">
        <div class="shrink-0 flex items-center gap-2 px-4 bg-gold-500 h-full z-10">
            <svg class="w-3.5 h-3.5 text-comsoc-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
            </svg>
            <span class="text-[11px] font-black text-comsoc-900 uppercase tracking-widest">Announcements</span>
        </div>
        <div class="flex-1 overflow-hidden relative">
            <p class="ticker-inner text-xs text-white/90 py-2">
                &nbsp;&nbsp;&nbsp;&nbsp;📢 Welcome to COMSOC Hub — your centralized platform for the Computer Society of CCIS, University of Makati.
                &nbsp;&nbsp;&nbsp;•&nbsp;&nbsp;&nbsp;
                🎓 COMSOC membership registration for AY 2025–2026 is now open. Contact a COMSOC officer to enroll.
                &nbsp;&nbsp;&nbsp;•&nbsp;&nbsp;&nbsp;
                📅 Upcoming events will be posted once approved by the organization adviser.
                &nbsp;&nbsp;&nbsp;•&nbsp;&nbsp;&nbsp;
                💻 COMSOC Hub is being built module by module — more features coming soon!
            </p>
        </div>
    </div>
</div>

{{-- ============================================================
     HERO — Full-width photo banner
     ============================================================ --}}
<section x-data="{ playing: true }" class="relative overflow-hidden" style="height: 580px; min-height: 420px;">

    {{-- Background photo --}}
    <div class="absolute inset-0">
        <img src="{{ asset('images/hero_logo.jpg') }}"
             alt="COMSOC Hero"
             class="w-full h-full object-cover object-center"/>
    </div>

    {{-- Multi-layer overlay: dark gradient from bottom, green tint --}}
    <div class="absolute inset-0"
         style="background: linear-gradient(
             to top,
             rgba(5,46,22,0.96) 0%,
             rgba(5,46,22,0.72) 38%,
             rgba(5,46,22,0.38) 65%,
             rgba(0,0,0,0.28) 100%
         )"></div>
    <div class="absolute inset-0" style="background: rgba(22,101,52,0.18); mix-blend-mode: multiply;"></div>

    {{-- Gold accent line at top --}}
    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-gold-600 via-gold-400 to-gold-600 z-10"></div>

    {{-- Pause / play button (top-right like UP) --}}
    <button @click="playing = !playing"
            class="absolute top-4 right-4 z-20 w-9 h-9 flex items-center justify-center bg-black/40 hover:bg-black/60 border border-white/20 rounded text-white transition-colors"
            :aria-label="playing ? 'Pause' : 'Play'">
        <svg x-show="playing" class="w-4 h-4" fill="currentColor" viewBox="0 0 16 16">
            <path d="M4 2h3v12H4zm5 0h3v12H9z"/>
        </svg>
        <svg x-show="!playing" x-cloak class="w-4 h-4" fill="currentColor" viewBox="0 0 16 16">
            <path d="M4 2l10 6-10 6V2z"/>
        </svg>
    </button>

    {{-- Content — center aligned --}}
    <div class="relative z-10 flex flex-col items-center justify-center h-full px-4 sm:px-8 text-center">

        {{-- COMSOC logo watermark above headline --}}
        <img src="{{ asset('images/comsoc_logo.png') }}"
             alt="COMSOC Logo"
             class="w-16 h-16 object-contain mb-4 opacity-95 drop-shadow-xl"/>

        {{-- Main headline --}}
        <h1 class="text-2xl sm:text-4xl lg:text-5xl font-black text-white uppercase tracking-widest leading-tight mb-3"
            style="text-shadow: 0 2px 24px rgba(0,0,0,0.55);">
            COMSOC HUB
        </h1>

        {{-- Subline --}}
        <p class="text-xs sm:text-sm text-white/85 max-w-xl mb-7 leading-relaxed"
           style="text-shadow: 0 1px 10px rgba(0,0,0,0.65);">
            The centralized platform of the Computer Society of CCIS, University of Makati.
        </p>

        {{-- CTA — Learn About COMSOC --}}
        <div class="flex justify-center">
            <a href="{{ route('about') }}"
               class="inline-flex items-center gap-2 px-7 py-3 border-2 border-white/70 text-white font-bold text-sm uppercase tracking-wider rounded hover:bg-white hover:text-comsoc-900 transition-all shadow-lg">
                Learn About COMSOC
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
        </div>
    </div>

    {{-- Bottom wave --}}
    <div class="absolute bottom-0 left-0 right-0 pointer-events-none z-10">
        <svg viewBox="0 0 1440 48" preserveAspectRatio="none" class="w-full h-10 sm:h-12 block">
            <path d="M0 48L480 20C720 8 960 8 1440 20V48H0Z" fill="#f9fafb"/>
        </svg>
    </div>
</section>

{{-- ============================================================
     QUICK STATS STRIP
     ============================================================ --}}
<section class="bg-gray-50 border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 sm:grid-cols-4 divide-x divide-gray-200">
            @foreach([
                ['7',    'User Roles',      'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
                ['10+',  'Platform Modules','M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18'],
                ['4',    'Programs',        'M12 14l9-5-9-5-9 5 9 5z'],
                ['2026', 'Academic Year',   'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
            ] as [$val, $label, $icon])
                <div class="flex items-center gap-3 px-6 py-5">
                    <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-comsoc-100 shrink-0">
                        <svg class="w-5 h-5 text-comsoc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-black text-comsoc-800 leading-none">{{ $val }}</p>
                        <p class="text-xs text-gray-500 font-medium mt-0.5">{{ $label }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============================================================
     ABOUT COMSOC (UP-style intro block)
     ============================================================ --}}
<section id="about" class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <p class="text-xs font-bold text-comsoc-600 uppercase tracking-widest mb-3">About COMSOC</p>
                <h2 class="text-3xl sm:text-4xl font-black text-gray-900 leading-tight mb-5">
                    Serving the students of<br>CCIS since day one.
                </h2>
                <p class="text-gray-600 text-base leading-relaxed mb-5">
                    The Computer Society (COMSOC) is the official student organization of the
                    College of Computing and Information Sciences at the University of Makati.
                    We represent students in the programs of Computer Science, Information
                    Technology, Diploma in IT, and Information Systems.
                </p>
                <p class="text-gray-600 text-base leading-relaxed mb-6">
                    COMSOC Hub is our centralized digital platform — designed to reduce
                    manual processes and keep members connected, informed, and engaged.
                </p>
                <div class="flex flex-wrap gap-3">
                    @auth
                        <a href="{{ url('/dashboard') }}"
                           class="inline-flex items-center gap-2 px-5 py-2.5 bg-comsoc-700 text-white text-sm font-semibold rounded-lg hover:bg-comsoc-800 transition-colors">
                            Go to Dashboard
                        </a>
                    @else
                        <a href="{{ route('register') }}"
                           class="inline-flex items-center gap-2 px-5 py-2.5 bg-comsoc-700 text-white text-sm font-semibold rounded-lg hover:bg-comsoc-800 transition-colors">
                            Create an account
                        </a>
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center gap-2 px-5 py-2.5 border border-comsoc-300 text-comsoc-700 text-sm font-semibold rounded-lg hover:bg-comsoc-50 transition-colors">
                            Sign in
                        </a>
                    @endauth
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                @foreach([
                    ['BSCS', 'Bachelor of Science in Computer Science', 'bg-comsoc-50 border-comsoc-200 text-comsoc-700'],
                    ['BSIT', 'Bachelor of Science in Information Technology', 'bg-blue-50 border-blue-200 text-blue-700'],
                    ['DIT',  'Diploma in Information Technology', 'bg-purple-50 border-purple-200 text-purple-700'],
                    ['BSIS', 'Bachelor of Science in Information Systems', 'bg-orange-50 border-orange-200 text-orange-700'],
                ] as [$code, $name, $style])
                    <div class="border rounded-xl p-5 {{ $style }}">
                        <p class="text-2xl font-black mb-1">{{ $code }}</p>
                        <p class="text-xs font-medium leading-snug opacity-80">{{ $name }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- ============================================================
     CCIS & COMSOC NEWS  (dynamic from official posts)
     ============================================================ --}}
<section id="features" class="py-16 bg-white border-t border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        @php
            $newsItems = \App\Models\Post::with(['author', 'images'])
                ->where('type', \App\Models\Post::TYPE_OFFICIAL)
                ->where('is_hidden', false)
                ->latest()
                ->take(6)
                ->get();
        @endphp

        <div class="flex items-center justify-between mb-8">
            <h2 class="section-bar text-2xl font-black text-gray-900">CCIS & COMSOC News</h2>
            @if($newsItems->isNotEmpty())
                @auth
                    <a href="{{ route('community.index') }}" class="text-sm font-semibold text-comsoc-600 hover:text-comsoc-800 transition-colors">
                        View all posts →
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-comsoc-600 hover:text-comsoc-800 transition-colors">
                        Sign in to see more →
                    </a>
                @endauth
            @else
                <a href="#officers" class="text-sm font-semibold text-comsoc-600 hover:text-comsoc-800 transition-colors">
                    Meet the officers →
                </a>
            @endif
        </div>

        @if($newsItems->isEmpty())
            {{-- Empty state — show platform features as fallback --}}
            <div class="mb-6 flex items-center gap-3 p-4 bg-comsoc-50 border border-comsoc-100 rounded-xl">
                <svg class="w-5 h-5 text-comsoc-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm text-comsoc-700">
                    News and announcements posted by COMSOC officers will appear here.
                    @auth
                        <a href="{{ route('community.index') }}" class="font-semibold underline hover:text-comsoc-900">Visit the community feed</a> to see all posts.
                    @else
                        <a href="{{ route('login') }}" class="font-semibold underline hover:text-comsoc-900">Sign in</a> to access the full community feed.
                    @endauth
                </p>
            </div>

            {{-- Platform features as fallback content --}}
            @php
            $features = [
                ['bg-comsoc-700', 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'Membership Management', 'Academic-year membership tracking with status, program, and renewal history.'],
                ['bg-blue-700',   'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'Event Management', 'Create, plan, and publish events with a full adviser approval workflow.'],
                ['bg-purple-700', 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z', 'Event Registration', 'Students register directly. Capacity limits and deadlines enforced automatically.'],
                ['bg-orange-600', 'M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z', 'QR Attendance', 'Officers scan QR passes at the venue. Attendance is recorded instantly.'],
                ['bg-yellow-600', 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z', 'Digital Certificates', 'Eligible attendees receive certificates they can view and download.'],
                ['bg-pink-700',   'M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z', 'COMSOC Community', 'A private org feed for announcements, discussions, and event updates.'],
            ];
            @endphp
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-0 border border-gray-200 rounded-2xl overflow-hidden">
                @foreach($features as $i => [$bg, $icon, $title, $desc])
                    <div class="news-card p-6 bg-white {{ !$loop->last ? 'border-b sm:border-b-0 sm:border-r border-gray-100' : '' }}
                                {{ $loop->index >= 3 ? 'border-t border-gray-100' : '' }}">
                        <div class="flex items-center justify-center w-11 h-11 rounded-xl {{ $bg }} mb-4">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
                            </svg>
                        </div>
                        <h3 class="font-bold text-gray-900 text-base mb-1.5">{{ $title }}</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">{{ $desc }}</p>
                    </div>
                @endforeach
            </div>

        @else
            {{-- NEWS GRID — UP style --}}
            @php $featured = $newsItems->first(); $rest = $newsItems->skip(1); @endphp

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Featured / lead story (left, spans 2 cols) --}}
                <div class="lg:col-span-2">
                    <div class="news-card bg-white border border-gray-200 rounded-2xl overflow-hidden h-full flex flex-col">
                        {{-- Image or green placeholder --}}
                        @if($featured->images->isNotEmpty())
                            <img src="{{ asset('storage/' . $featured->images->first()->path) }}"
                                 alt="News"
                                 class="w-full h-56 object-cover"/>
                        @else
                            <div class="w-full h-56 bg-gradient-to-br from-comsoc-700 to-comsoc-900 flex items-center justify-center relative overflow-hidden">
                                <div class="absolute inset-0" style="background-image:linear-gradient(rgba(255,255,255,0.03) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.03) 1px,transparent 1px);background-size:32px 32px"></div>
                                <svg class="w-16 h-16 text-comsoc-500 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                        @endif
                        <div class="flex-1 p-6">
                            {{-- Badge --}}
                            <div class="flex items-center gap-2 mb-3">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-comsoc-700 text-white text-[10px] font-bold uppercase tracking-widest">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                    COMSOC Official
                                </span>
                                <span class="text-xs text-gray-400">{{ $featured->created_at->format('M d, Y') }}</span>
                            </div>
                            {{-- Title (first line of body) --}}
                            <h3 class="text-xl font-black text-gray-900 mb-2 leading-snug line-clamp-2">
                                {{ Str::limit($featured->body, 80) }}
                            </h3>
                            <p class="text-sm text-gray-500 leading-relaxed line-clamp-3 mb-4">
                                {{ Str::limit($featured->body, 200) }}
                            </p>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <img src="{{ $featured->author->getProfilePictureUrl() }}"
                                         class="w-7 h-7 rounded-full object-cover border border-gray-200"/>
                                    <span class="text-xs font-semibold text-gray-600">{{ $featured->author->name }}</span>
                                    <span class="text-xs text-gray-400">· {{ $featured->author->getRoleLabel() }}</span>
                                </div>
                                @auth
                                    <a href="{{ route('community.index') }}"
                                       class="text-xs font-semibold text-comsoc-600 hover:text-comsoc-800 transition-colors">
                                        Read more →
                                    </a>
                                @else
                                    <a href="{{ route('login') }}"
                                       class="text-xs font-semibold text-comsoc-600 hover:text-comsoc-800 transition-colors">
                                        Sign in to read →
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sidebar news list --}}
                <div class="flex flex-col gap-4">
                    @foreach($rest->take(3) as $post)
                        <div class="news-card bg-white border border-gray-200 rounded-xl overflow-hidden flex gap-0">
                            {{-- Small image or colored strip --}}
                            @if($post->images->isNotEmpty())
                                <img src="{{ asset('storage/' . $post->images->first()->path) }}"
                                     class="w-24 object-cover shrink-0"/>
                            @else
                                <div class="w-2 shrink-0 bg-comsoc-600"></div>
                            @endif
                            <div class="flex-1 p-4">
                                <div class="flex items-center gap-1.5 mb-1.5">
                                    <span class="text-[10px] font-bold text-comsoc-600 uppercase tracking-wide">Official</span>
                                    <span class="text-gray-300">·</span>
                                    <span class="text-[10px] text-gray-400">{{ $post->created_at->format('M d') }}</span>
                                </div>
                                <p class="text-sm font-bold text-gray-900 line-clamp-2 leading-snug mb-1.5">
                                    {{ Str::limit($post->body, 90) }}
                                </p>
                                <p class="text-xs text-gray-400">{{ $post->author->name }}</p>
                            </div>
                        </div>
                    @endforeach

                    {{-- See more link --}}
                    @auth
                        <a href="{{ route('community.index') }}"
                           class="flex items-center justify-center gap-2 py-3 border-2 border-comsoc-200 rounded-xl text-sm font-semibold text-comsoc-700 hover:bg-comsoc-50 transition-colors">
                            View all news
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="flex items-center justify-center gap-2 py-3 border-2 border-comsoc-200 rounded-xl text-sm font-semibold text-comsoc-700 hover:bg-comsoc-50 transition-colors">
                            Sign in for full access
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    @endauth
                </div>
            </div>
        @endif
    </div>
</section>

{{-- ============================================================
     UPCOMING EVENTS  (UP "Events" list style)
     ============================================================ --}}
<section id="events-preview" class="py-16 bg-gray-50 border-t border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <h2 class="section-bar text-2xl font-black text-gray-900">Upcoming Events</h2>
            @auth
                <a href="{{ route('events.index') }}" class="text-sm font-semibold text-comsoc-600 hover:text-comsoc-800 transition-colors">
                    See all events →
                </a>
            @else
                <a href="{{ route('login') }}" class="text-sm font-semibold text-comsoc-600 hover:text-comsoc-800 transition-colors">
                    Sign in to view →
                </a>
            @endauth
        </div>

        @php
            $upcomingEvents = \App\Models\Event::whereIn('status', ['published', 'ongoing'])
                ->where('event_date', '>=', now())
                ->orderBy('event_date')
                ->take(4)
                ->get();
        @endphp

        @if($upcomingEvents->isEmpty())
            <div class="bg-white border border-gray-200 rounded-xl p-10 text-center">
                <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-gray-400 font-medium text-sm">No upcoming events yet.</p>
                <p class="text-gray-300 text-xs mt-1">Check back soon — events will appear here once published.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                @foreach($upcomingEvents as $event)
                    <div class="news-card bg-white border border-gray-200 rounded-xl overflow-hidden flex">
                        {{-- Date badge --}}
                        <div class="w-20 shrink-0 flex flex-col items-center justify-center bg-comsoc-700 text-white py-4">
                            <p class="text-2xl font-black leading-none">{{ $event->event_date->format('d') }}</p>
                            <p class="text-xs font-semibold uppercase tracking-wide mt-0.5">{{ $event->event_date->format('M') }}</p>
                            <p class="text-[10px] text-comsoc-300 mt-0.5">{{ $event->event_date->format('Y') }}</p>
                        </div>
                        {{-- Content --}}
                        <div class="flex-1 p-4">
                            <p class="font-bold text-gray-900 text-sm leading-snug mb-1 line-clamp-2">{{ $event->title }}</p>
                            <p class="text-xs text-gray-500 mb-2">
                                📍 {{ $event->venue }}
                                &nbsp;·&nbsp;
                                🕐 {{ \Carbon\Carbon::parse($event->start_time)->format('g:i A') }}
                            </p>
                            @auth
                                <a href="{{ route('events.show', $event) }}"
                                   class="text-xs font-semibold text-comsoc-600 hover:text-comsoc-800 transition-colors">
                                    View details →
                                </a>
                            @else
                                <a href="{{ route('login') }}"
                                   class="text-xs font-semibold text-comsoc-600 hover:text-comsoc-800 transition-colors">
                                    Sign in to register →
                                </a>
                            @endauth
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

{{-- ============================================================
     OFFICERS  (UP "Voices" style grid)
     ============================================================ --}}
<section id="officers" class="py-16 bg-white border-t border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex items-center justify-between mb-2">
            <h2 class="section-bar text-2xl font-black text-gray-900">Meet the Officers</h2>
            <span class="text-xs text-gray-400 font-medium">Academic Year 2025–2026</span>
        </div>
        <p class="text-sm text-gray-500 mb-8 pl-4">The elected leaders of the Computer Society — CCIS, University of Makati.</p>

        @php
        $executives = [
            ['President',                          'Alexandra Reyes',       'CS',  'bg-yellow-100 text-yellow-800'],
            ['VP for Internal Affairs',             'Benjamin Cruz',         'IT',  'bg-comsoc-100 text-comsoc-800'],
            ['VP for External Affairs',             'Christine Lim',         'IT',  'bg-comsoc-100 text-comsoc-800'],
            ['Secretary',                           'Daniel Ocampo',         'CS',  'bg-blue-100 text-blue-800'],
            ['Treasurer',                           'Erica Santos',          'IS',  'bg-purple-100 text-purple-800'],
            ['Auditor',                             'Francis Dela Torre',    'CS',  'bg-red-100 text-red-800'],
            ['Public Relations Officer',            'Gabrielle Mendoza',     'IT',  'bg-pink-100 text-pink-800'],
        ];
        $progReps = [
            ['CS Rep',    'Harold Bautista',   'BSCS'],
            ['IT Rep',    'Isabelle Tan',       'BSIT'],
            ['Diploma Rep','James Navarro',     'DIT'],
            ['IS Rep',    'Karen Villanueva',   'BSIS'],
        ];
        @endphp

        {{-- Executive officers --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-7 gap-4 mb-10">
            @foreach($executives as [$position, $name, $program, $badge])
                <div class="news-card bg-white border border-gray-200 rounded-xl p-4 text-center">
                    {{-- Photo placeholder --}}
                    <div class="relative mx-auto mb-3 w-16 h-16 rounded-full overflow-hidden border-2 border-comsoc-200 bg-gradient-to-br from-comsoc-100 to-comsoc-200 flex items-center justify-center">
                        <svg class="w-8 h-8 text-comsoc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <p class="font-bold text-gray-900 text-xs leading-tight mb-1.5">{{ $name }}</p>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $badge }}">
                        {{ strlen($position) > 18 ? substr($position, 0, 3) . '...' : $position }}
                    </span>
                    <p class="text-[10px] text-gray-400 mt-1.5">{{ $position }}</p>
                </div>
            @endforeach
        </div>

        {{-- Program Representatives --}}
        <div class="border-t border-gray-100 pt-8">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Program Representatives</p>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                @foreach($progReps as [$role, $name, $code])
                    <div class="news-card bg-comsoc-50 border border-comsoc-100 rounded-xl p-4 flex items-center gap-3">
                        <div class="flex items-center justify-center w-10 h-10 rounded-full bg-comsoc-200 shrink-0">
                            <svg class="w-5 h-5 text-comsoc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-comsoc-800 text-xs truncate">{{ $name }}</p>
                            <p class="text-[10px] text-comsoc-600 font-semibold">{{ $code }} — {{ $role }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <p class="mt-6 text-xs text-gray-400 flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Officer photos will be uploaded via the admin dashboard.
        </p>
    </div>
</section>

{{-- ============================================================
     NEWS / HOW IT WORKS  (UP "News" style)
     ============================================================ --}}
<section id="news" class="py-16 bg-gray-50 border-t border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <h2 class="section-bar text-2xl font-black text-gray-900">How It Works</h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach([
                ['1', 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z', 'Register & Join', 'Create your account, choose your program, and become part of COMSOC.'],
                ['2', 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'Discover Events', 'Browse published events, view details, and register directly on the platform.'],
                ['3', 'M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z', 'Attend & Check In', 'Show your QR pass. Officers scan it — attendance recorded instantly.'],
                ['4', 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'Get Your Certificate', 'If eligible, your certificate is automatically issued and downloadable.'],
            ] as [$step, $icon, $title, $desc])
                <div class="news-card bg-white border border-gray-200 rounded-xl p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-comsoc-700 shrink-0">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
                            </svg>
                        </div>
                        <span class="text-4xl font-black text-gray-100 select-none leading-none">{{ $step }}</span>
                    </div>
                    <h3 class="font-bold text-gray-900 text-base mb-1.5">{{ $title }}</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============================================================
     CTA BANNER
     ============================================================ --}}
<section class="py-14 bg-comsoc-800">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-2xl sm:text-3xl font-black text-white mb-3">Ready to join COMSOC Hub?</h2>
        <p class="text-comsoc-200 mb-7">Connect with the Computer Society of CCIS — free to join, no payment required.</p>
        @auth
            <a href="{{ url('/dashboard') }}"
               class="inline-flex items-center gap-2 px-7 py-3 bg-white text-comsoc-900 font-bold rounded-xl hover:bg-comsoc-50 transition-colors shadow-lg text-sm">
                Go to Dashboard
            </a>
        @else
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('register') }}"
                   class="inline-flex items-center justify-center gap-2 px-7 py-3 bg-white text-comsoc-900 font-bold rounded-xl hover:bg-comsoc-50 transition-colors shadow-lg text-sm">
                    Create an account
                </a>
                <a href="{{ route('login') }}"
                   class="inline-flex items-center justify-center px-7 py-3 border-2 border-white/25 text-white font-bold rounded-xl hover:bg-white/10 transition-colors text-sm">
                    Sign in
                </a>
            </div>
        @endauth
    </div>
</section>

{{-- ============================================================
     FOOTER  (UP-style multi-column)
     ============================================================ --}}
<footer class="bg-comsoc-950 border-t border-comsoc-900">

    {{-- Main footer columns --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">

            {{-- Brand --}}
            <div class="lg:col-span-1">
                <div class="flex items-center gap-2.5 mb-4">
                    <img src="{{ asset('images/comsoc_logo.png') }}"
                         alt="COMSOC Logo"
                         class="w-9 h-9 object-contain shrink-0"/>
                    <div>
                        <span class="block font-black text-white text-sm">COMSOC Hub</span>
                        <span class="block text-[10px] text-comsoc-400">CCIS · University of Makati</span>
                    </div>
                </div>
                <p class="text-sm text-comsoc-400 leading-relaxed">
                    The centralized digital platform for the Computer Society of CCIS.
                </p>
            </div>

            {{-- Platform --}}
            <div>
                <p class="text-[11px] font-bold text-comsoc-400 uppercase tracking-widest mb-4">Platform</p>
                <ul class="space-y-2.5">
                    @foreach(['About COMSOC'=>'#about','Features'=>'#features','Upcoming Events'=>'#events-preview','Officers'=>'#officers','How It Works'=>'#news'] as $l => $h)
                        <li><a href="{{ $h }}" class="text-sm text-comsoc-400 hover:text-white transition-colors">{{ $l }}</a></li>
                    @endforeach
                </ul>
            </div>

            {{-- Account --}}
            <div>
                <p class="text-[11px] font-bold text-comsoc-400 uppercase tracking-widest mb-4">Account</p>
                <ul class="space-y-2.5">
                    @auth
                        <li><a href="{{ url('/dashboard') }}" class="text-sm text-comsoc-400 hover:text-white transition-colors">Dashboard</a></li>
                        <li><a href="{{ route('community.index') }}" class="text-sm text-comsoc-400 hover:text-white transition-colors">Community</a></li>
                        <li><a href="{{ route('events.index') }}" class="text-sm text-comsoc-400 hover:text-white transition-colors">Events</a></li>
                        <li><a href="{{ route('membership.index') }}" class="text-sm text-comsoc-400 hover:text-white transition-colors">My Membership</a></li>
                        <li><a href="{{ route('profile.edit') }}" class="text-sm text-comsoc-400 hover:text-white transition-colors">Profile</a></li>
                    @else
                        <li><a href="{{ route('login') }}" class="text-sm text-comsoc-400 hover:text-white transition-colors">Sign in</a></li>
                        <li><a href="{{ route('register') }}" class="text-sm text-comsoc-400 hover:text-white transition-colors">Create account</a></li>
                    @endauth
                </ul>
            </div>

            {{-- Programs --}}
            <div>
                <p class="text-[11px] font-bold text-comsoc-400 uppercase tracking-widest mb-4">Programs</p>
                <ul class="space-y-2.5">
                    @foreach(['BSCS — Computer Science','BSIT — Information Technology','DIT — Diploma in IT','BSIS — Information Systems'] as $p)
                        <li><p class="text-sm text-comsoc-400">{{ $p }}</p></li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    {{-- Bottom bar --}}
    <div class="border-t border-comsoc-800/60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-xs text-comsoc-600">
                &copy; {{ date('Y') }} Computer Society · College of Computing and Information Sciences · University of Makati
            </p>
            <p class="text-xs text-comsoc-700">COMSOC Hub v1.0</p>
        </div>
    </div>
</footer>

</body>
</html>
