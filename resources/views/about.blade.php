<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>About COMSOC — CCIS · University of Makati</title>
    <meta name="description" content="Learn about the Computer Society (COMSOC) of the College of Computing and Information Sciences, University of Makati.">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .util-bar { background: #0a2e1a; }
        .section-bar { border-left: 4px solid #16a34a; padding-left: .75rem; }
        .card-lift { transition: transform .18s ease, box-shadow .18s ease; }
        .card-lift:hover { transform: translateY(-3px); box-shadow: 0 12px 32px rgba(0,0,0,0.09); }
        html { scroll-behavior: smooth; }

        /* Scroll reveal */
        .reveal { opacity: 0; transform: translateY(24px); transition: opacity .5s ease, transform .5s ease; }
        .reveal.visible { opacity: 1; transform: translateY(0); }
        .delay-100 { transition-delay: .1s !important; }
        .delay-200 { transition-delay: .2s !important; }
        .delay-300 { transition-delay: .3s !important; }
        .delay-400 { transition-delay: .4s !important; }
    </style>
</head>
<body class="font-sans antialiased bg-white text-gray-900" x-data>

{{-- ===== UTILITY BAR ===== --}}
<div class="util-bar hidden md:block">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-9">
            <span class="text-[11px] text-green-300">Computer Society · CCIS · University of Makati</span>
            <div class="flex items-center gap-4">
                @auth
                    <a href="{{ url('/dashboard') }}" class="text-[11px] text-green-300 hover:text-white transition-colors">Dashboard →</a>
                @else
                    <a href="{{ route('login') }}" class="text-[11px] text-green-300 hover:text-white transition-colors">Sign in</a>
                    <span class="text-green-700">|</span>
                    <a href="{{ route('register') }}" class="text-[11px] text-green-300 hover:text-white transition-colors">Register</a>
                @endauth
            </div>
        </div>
    </div>
</div>

{{-- ===== NAVBAR ===== --}}
<nav x-data="{ scrolled: false, mobileOpen: false }"
     x-on:scroll.window="scrolled = window.scrollY > 30"
     :class="scrolled ? 'shadow-md' : 'shadow-sm'"
     class="sticky top-0 z-50 bg-white border-b border-gray-200 transition-shadow duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <a href="{{ url('/') }}" class="flex items-center gap-3 shrink-0">
                <img src="{{ asset('images/comsoc_logo.png') }}" alt="COMSOC Logo" class="w-10 h-10 object-contain"/>
                <div class="leading-none">
                    <span class="block font-black text-comsoc-900 text-lg">COMSOC Hub</span>
                    <span class="block text-[10px] text-gray-400 font-medium">CCIS · Univ. of Makati</span>
                </div>
            </a>

            <div class="hidden lg:flex items-center gap-1">
                @foreach([
                    'Home'    => url('/'),
                    'About'   => route('about'),
                    'News'    => url('/#features'),
                    'Events'  => url('/#events-preview'),
                    'Officers'=> url('/#officers'),
                ] as $label => $href)
                    <a href="{{ $href }}"
                       class="px-4 py-2 text-sm font-medium rounded-lg transition-colors
                              {{ request()->routeIs('about') && $label === 'About'
                                  ? 'bg-comsoc-700 text-white'
                                  : 'text-gray-600 hover:text-comsoc-700 hover:bg-comsoc-50' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <div class="flex items-center gap-2">
                @auth
                    <a href="{{ url('/dashboard') }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2 bg-comsoc-700 text-white text-sm font-semibold rounded-lg hover:bg-comsoc-800 transition-colors">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="hidden sm:inline-flex px-4 py-2 text-sm font-semibold text-comsoc-700 border border-comsoc-300 rounded-lg hover:bg-comsoc-50 transition-colors">
                        Sign in
                    </a>
                    <a href="{{ route('register') }}"
                       class="inline-flex items-center px-4 py-2 bg-comsoc-700 text-white text-sm font-semibold rounded-lg hover:bg-comsoc-800 transition-colors">
                        Join us
                    </a>
                @endauth
                <button @click="mobileOpen = !mobileOpen" class="lg:hidden p-2 text-gray-500 hover:bg-gray-100 rounded-lg ml-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>
        <div x-show="mobileOpen" x-cloak class="lg:hidden border-t border-gray-100 py-3 space-y-1">
            @foreach(['Home'=>url('/'),'About'=>route('about'),'News'=>url('/#features'),'Events'=>url('/#events-preview'),'Officers'=>url('/#officers')] as $l => $h)
                <a href="{{ $h }}" @click="mobileOpen=false"
                   class="block px-4 py-2 text-sm font-medium text-gray-700 hover:bg-comsoc-50 hover:text-comsoc-700 rounded-lg">{{ $l }}</a>
            @endforeach
        </div>
    </div>
</nav>

{{-- ===== PAGE HERO ===== --}}
<section class="relative bg-comsoc-950 overflow-hidden" style="min-height: 280px;">
    <div class="absolute inset-0"
         style="background-image: linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px); background-size: 44px 44px;"></div>
    <div class="absolute top-0 right-0 w-96 h-96 rounded-full pointer-events-none"
         style="background: radial-gradient(circle, rgba(22,163,74,0.15) 0%, transparent 65%); transform: translate(25%,-25%)"></div>
    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-gold-600 via-gold-400 to-gold-600"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 flex items-center gap-6">
        <img src="{{ asset('images/comsoc_logo.png') }}" alt="COMSOC Logo"
             class="w-20 h-20 object-contain shrink-0 drop-shadow-xl hidden sm:block"/>
        <div>
            <p class="text-xs font-bold text-comsoc-400 uppercase tracking-widest mb-2">About</p>
            <h1 class="text-3xl sm:text-4xl font-black text-white leading-tight">
                Computer Society<br>
                <span class="text-gold-400">CCIS · University of Makati</span>
            </h1>
            <p class="text-comsoc-300 text-sm mt-2">The sole local student organization of the College of Computing and Information Sciences</p>
        </div>
    </div>
</section>

{{-- ===== BREADCRUMB ===== --}}
<div class="bg-gray-50 border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2.5 flex items-center gap-2 text-xs text-gray-500">
        <a href="{{ url('/') }}" class="hover:text-comsoc-700 transition-colors">Home</a>
        <span class="text-gray-400">/</span>
        <span class="text-gray-800 font-semibold">About COMSOC</span>
    </div>
</div>

{{-- ===== WHO WE ARE ===== --}}
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-14 items-center">
            <div class="reveal">
                <p class="text-xs font-bold text-comsoc-600 uppercase tracking-widest mb-3">Who We Are</p>
                <h2 class="section-bar text-3xl font-black text-gray-900 mb-5">
                    The Computer Society of CCIS
                </h2>
                <p class="text-gray-600 leading-relaxed mb-4">
                    The <strong>Computer Society (COMSOC)</strong> is the official and sole local student organization
                    of the College of Computing and Information Sciences (CCIS) at the
                    <strong>University of Makati</strong>.
                </p>
                <p class="text-gray-600 leading-relaxed mb-4">
                    We exist to unite all students under the CCIS college — bringing together future IT professionals
                    from the programs of Computer Science, Information Technology, Diploma in IT, and Information Systems.
                </p>
                <p class="text-gray-600 leading-relaxed">
                    COMSOC aims to gather CCIS students, build and promote IT-related activities, develop leadership
                    and technical skills, and extend IT-based endeavors to the broader community of the University
                    of Makati.
                </p>
            </div>
            <div class="reveal delay-200">
                <div class="relative rounded-2xl overflow-hidden shadow-xl bg-comsoc-950" style="min-height: 300px;">
                    <img src="{{ asset('images/hero_logo.jpg') }}" alt="CCIS UMak"
                         class="w-full h-full object-cover opacity-80" style="min-height: 300px;"/>
                    <div class="absolute inset-0" style="background: linear-gradient(to top, rgba(5,46,22,0.80) 0%, transparent 50%)"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-6">
                        <p class="text-white font-bold text-sm">College of Computing and Information Sciences</p>
                        <p class="text-comsoc-300 text-xs mt-0.5">University of Makati · Makati City, Philippines</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ===== MISSION & VISION ===== --}}
<section class="py-16 bg-gray-50 border-t border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12 reveal">
            <h2 class="section-bar inline-block text-2xl font-black text-gray-900">Mission & Vision</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="reveal card-lift bg-white border border-gray-200 rounded-2xl p-8 shadow-sm">
                <div class="flex items-center justify-center w-14 h-14 rounded-2xl bg-comsoc-700 mb-5">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-black text-gray-900 mb-3">Vision</h3>
                <p class="text-gray-600 leading-relaxed">
                    To be a premier student organization that empowers CCIS students to become
                    technically proficient, socially responsible, and community-oriented IT professionals
                    who contribute meaningfully to the digital advancement of the University of Makati
                    and the wider society.
                </p>
            </div>
            <div class="reveal delay-200 card-lift bg-comsoc-800 border border-comsoc-700 rounded-2xl p-8 shadow-sm">
                <div class="flex items-center justify-center w-14 h-14 rounded-2xl bg-comsoc-600 mb-5">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-black text-white mb-3">Mission</h3>
                <p class="text-comsoc-200 leading-relaxed">
                    To gather and unite all students of CCIS; to build, organize, and promote
                    IT-related activities that develop academic excellence, leadership, and camaraderie;
                    and to extend IT-based endeavors and services to the greater university community
                    and beyond.
                </p>
            </div>
        </div>
    </div>
</section>

{{-- ===== CORE OBJECTIVES ===== --}}
<section class="py-16 bg-white border-t border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12 reveal">
            <p class="text-xs font-bold text-comsoc-600 uppercase tracking-widest mb-3">What We Stand For</p>
            <h2 class="section-bar inline-block text-2xl font-black text-gray-900">Core Objectives</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach([
                ['bg-comsoc-600', 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'Unity & Inclusivity', 'Gather and unite all students under the CCIS college regardless of program, year level, or background.'],
                ['bg-blue-600',   'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z', 'IT Excellence', 'Promote and develop technical skills, knowledge, and innovation in computing and information technology.'],
                ['bg-purple-600', 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'Events & Activities', 'Organize seminars, workshops, competitions, and social events that enrich the academic and campus life of CCIS students.'],
                ['bg-orange-600', 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 00-1-1h-2a1 1 0 00-1 1v5m4 0H9', 'Leadership Development', 'Cultivate future leaders among CCIS students through organizational experience, responsibilities, and mentorship.'],
                ['bg-teal-600',   'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'Community Outreach', 'Extend IT knowledge and services beyond the college — serving the university and broader community through technology-driven initiatives.'],
                ['bg-pink-600',   'M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z', 'Communication', 'Foster open communication between students, faculty, and the organization through transparent channels and active engagement.'],
            ] as $idx => [$bg, $icon, $title, $desc])
                <div class="reveal delay-{{ min($idx * 100, 400) }} card-lift bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                    <div class="flex items-center justify-center w-12 h-12 rounded-xl {{ $bg }} mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 text-base mb-2">{{ $title }}</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ===== COLLEGE PROGRAMS ===== --}}
<section class="py-16 bg-comsoc-950 border-t border-comsoc-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12 reveal">
            <p class="text-xs font-bold text-comsoc-400 uppercase tracking-widest mb-3">Our College</p>
            <h2 class="text-2xl font-black text-white">Programs Under CCIS</h2>
            <p class="text-comsoc-300 text-sm mt-2 max-w-xl mx-auto">
                COMSOC represents students enrolled in these four academic programs under the
                College of Computing and Information Sciences.
            </p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach([
                ['BSCS', 'Bachelor of Science in Computer Science', 'Focuses on computational theory, algorithms, software engineering, and the mathematical foundations of computing.', 'bg-comsoc-700 border-comsoc-600'],
                ['BSIT', 'Bachelor of Science in Information Technology', 'Emphasizes practical IT skills including systems administration, network management, and application development.', 'bg-blue-800 border-blue-700'],
                ['DIT',  'Diploma in Information Technology', 'A focused program providing foundational IT competencies for students entering the technology workforce.', 'bg-purple-800 border-purple-700'],
                ['BSIS', 'Bachelor of Science in Information Systems', 'Bridges business and technology — preparing graduates for roles in systems analysis, design, and enterprise solutions.', 'bg-teal-800 border-teal-700'],
            ] as $idx => [$code, $name, $desc, $style])
                <div class="reveal delay-{{ $idx * 100 }} card-lift border rounded-2xl p-6 {{ $style }}">
                    <p class="text-3xl font-black text-white mb-2">{{ $code }}</p>
                    <p class="text-sm font-bold text-white/90 mb-3 leading-snug">{{ $name }}</p>
                    <p class="text-xs text-white/65 leading-relaxed">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ===== COMSOC HUB PLATFORM ===== --}}
<section class="py-16 bg-gray-50 border-t border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="reveal">
                <p class="text-xs font-bold text-comsoc-600 uppercase tracking-widest mb-3">Our Digital Platform</p>
                <h2 class="section-bar text-2xl font-black text-gray-900 mb-4">About COMSOC Hub</h2>
                <p class="text-gray-600 leading-relaxed mb-4">
                    <strong>COMSOC Hub</strong> is the official centralized digital platform of the Computer Society,
                    developed to modernize and streamline the organization's operations. It replaces the
                    scattered use of Google Forms, spreadsheets, physical attendance sheets, and social
                    media announcements with a single unified system.
                </p>
                <p class="text-gray-600 leading-relaxed mb-6">
                    Built on Laravel with a focus on security, real-time data, and role-based access control,
                    COMSOC Hub brings all organizational functions under one roof.
                </p>
                <ul class="space-y-2.5">
                    @foreach([
                        'Membership management & academic-year tracking',
                        'Event planning, approval workflow & registration',
                        'QR-based attendance recording',
                        'Digital certificate issuance',
                        'Private community feed & announcements',
                        'Committee & task management',
                        'Document management & approval workflows',
                    ] as $feature)
                        <li class="flex items-start gap-2.5 text-sm text-gray-700">
                            <svg class="w-4 h-4 text-comsoc-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ $feature }}
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="reveal delay-200">
                <div class="bg-comsoc-900 rounded-2xl p-8 border border-comsoc-700">
                    <div class="flex items-center gap-3 mb-6">
                        <img src="{{ asset('images/comsoc_logo.png') }}" alt="COMSOC Logo" class="w-12 h-12 object-contain"/>
                        <div>
                            <p class="font-black text-white text-lg">COMSOC Hub</p>
                            <p class="text-comsoc-400 text-xs">v1.0 · AY 2025–2026</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-3 mb-6">
                        @foreach([['7','Roles'],['10+','Modules'],['4','Programs']] as [$v,$l])
                            <div class="bg-comsoc-800 rounded-xl p-3 text-center border border-comsoc-700">
                                <p class="text-2xl font-black text-white">{{ $v }}</p>
                                <p class="text-[10px] text-comsoc-400 font-medium mt-0.5 uppercase tracking-wide">{{ $l }}</p>
                            </div>
                        @endforeach
                    </div>
                    @auth
                        <a href="{{ url('/dashboard') }}"
                           class="block w-full text-center py-3 bg-comsoc-600 text-white font-bold text-sm rounded-xl hover:bg-comsoc-500 transition-colors">
                            Go to Dashboard →
                        </a>
                    @else
                        <div class="space-y-2.5">
                            <a href="{{ route('register') }}"
                               class="block w-full text-center py-3 bg-comsoc-600 text-white font-bold text-sm rounded-xl hover:bg-comsoc-500 transition-colors">
                                Create an Account
                            </a>
                            <a href="{{ route('login') }}"
                               class="block w-full text-center py-3 border border-comsoc-600 text-comsoc-300 font-semibold text-sm rounded-xl hover:bg-comsoc-800 transition-colors">
                                Sign in
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ===== CTA ===== --}}
<section class="py-14 bg-comsoc-800">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 text-center">
        <h2 class="text-2xl font-black text-white mb-3">Ready to be part of COMSOC?</h2>
        <p class="text-comsoc-200 text-sm mb-7">
            Join COMSOC Hub — the platform built for every student of CCIS.
        </p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            @auth
                <a href="{{ url('/dashboard') }}"
                   class="inline-flex items-center justify-center px-7 py-3 bg-white text-comsoc-900 font-bold rounded-xl hover:bg-comsoc-50 transition-colors text-sm shadow-lg">
                    Go to Dashboard
                </a>
            @else
                <a href="{{ route('register') }}"
                   class="inline-flex items-center justify-center px-7 py-3 bg-white text-comsoc-900 font-bold rounded-xl hover:bg-comsoc-50 transition-colors text-sm shadow-lg">
                    Create an account
                </a>
                <a href="{{ url('/') }}"
                   class="inline-flex items-center justify-center px-7 py-3 border-2 border-white/30 text-white font-bold rounded-xl hover:bg-white/10 transition-colors text-sm">
                    ← Back to home
                </a>
            @endauth
        </div>
    </div>
</section>

{{-- ===== FOOTER ===== --}}
<footer class="bg-comsoc-950 border-t border-comsoc-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex flex-col sm:flex-row items-center justify-between gap-3">
        <div class="flex items-center gap-2.5">
            <img src="{{ asset('images/comsoc_logo.png') }}" alt="" class="w-7 h-7 object-contain opacity-80"/>
            <span class="text-sm font-bold text-white">COMSOC Hub</span>
            <span class="text-comsoc-600 text-xs">· CCIS · University of Makati</span>
        </div>
        <p class="text-xs text-comsoc-600">&copy; {{ date('Y') }} Computer Society · CCIS · University of Makati</p>
    </div>
</footer>

{{-- Scroll reveal script --}}
<script>
(function() {
    const els = document.querySelectorAll('.reveal');
    const observer = new IntersectionObserver(entries => {
        entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); observer.unobserve(e.target); } });
    }, { threshold: 0.10 });
    els.forEach(el => observer.observe(el));
    setTimeout(() => els.forEach(el => { if (el.getBoundingClientRect().top < window.innerHeight) el.classList.add('visible'); }), 60);
})();
</script>

</body>
</html>
