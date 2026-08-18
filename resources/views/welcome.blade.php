<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>COMSOC Hub — Computer Society · CCIS · University of Makati</title>
    <meta name="description" content="COMSOC Hub is the centralized platform for the Computer Society of CCIS, University of Makati.">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* ---------- grid / orb backgrounds ---------- */
        .hero-grid {
            background-image:
                linear-gradient(rgba(255,255,255,0.025) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.025) 1px, transparent 1px);
            background-size: 44px 44px;
        }
        /* ---------- gradient headline ---------- */
        .text-gradient {
            background: linear-gradient(135deg, #fde047 0%, #facc15 45%, #fb923c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        /* ---------- stat dividers ---------- */
        .stat-border::after {
            content:''; position:absolute; right:0; top:20%; height:60%; width:1px;
            background:rgba(255,255,255,0.13);
        }
        .stat-border:last-child::after { display:none; }

        /* ---------- scroll-reveal ---------- */
        .reveal { opacity:0; transform:translateY(28px); transition:opacity .55s ease, transform .55s ease; }
        .reveal.visible { opacity:1; transform:translateY(0); }
        .reveal-left  { opacity:0; transform:translateX(-28px); transition:opacity .55s ease, transform .55s ease; }
        .reveal-left.visible  { opacity:1; transform:translateX(0); }
        .reveal-right { opacity:0; transform:translateX(28px);  transition:opacity .55s ease, transform .55s ease; }
        .reveal-right.visible { opacity:1; transform:translateX(0); }
        .reveal-scale { opacity:0; transform:scale(0.92); transition:opacity .5s ease, transform .5s ease; }
        .reveal-scale.visible { opacity:1; transform:scale(1); }

        /* ---------- stagger helpers ---------- */
        .delay-100 { transition-delay: .10s !important; }
        .delay-200 { transition-delay: .20s !important; }
        .delay-300 { transition-delay: .30s !important; }
        .delay-400 { transition-delay: .40s !important; }
        .delay-500 { transition-delay: .50s !important; }
        .delay-600 { transition-delay: .60s !important; }

        /* ---------- floating particles ---------- */
        @keyframes float-up {
            0%   { transform:translateY(0)   translateX(0)   scale(1);   opacity:.6; }
            50%  { transform:translateY(-60px) translateX(10px) scale(1.1); opacity:.9; }
            100% { transform:translateY(-120px) translateX(-5px) scale(.9); opacity:0; }
        }
        .particle {
            position:absolute; border-radius:50%;
            background:rgba(74,222,128,0.5);
            pointer-events:none; animation:float-up linear infinite;
        }

        /* ---------- card hover lift ---------- */
        .lift { transition:transform .22s ease, box-shadow .22s ease; }
        .lift:hover { transform:translateY(-5px); box-shadow:0 24px 48px rgba(0,0,0,0.09); }

        /* ---------- officer card photo ring ---------- */
        .officer-ring {
            background: conic-gradient(#16a34a, #facc15, #16a34a);
            padding: 3px; border-radius: 9999px;
        }
        .officer-ring-inner {
            background: #1e3a2f; border-radius: 9999px; overflow:hidden;
        }

        /* ---------- shimmer on placeholder ---------- */
        @keyframes shimmer {
            0%   { background-position:-400px 0; }
            100% { background-position:400px 0; }
        }
        .photo-placeholder {
            background: linear-gradient(90deg, #1a3328 25%, #224434 50%, #1a3328 75%);
            background-size: 400px 100%;
            animation: shimmer 2.2s infinite;
        }

        /* ---------- smooth scroll ---------- */
        html { scroll-behavior: smooth; }
    </style>
</head>
<body class="font-sans antialiased bg-white text-gray-900 overflow-x-hidden" x-data>

{{-- ========================================================
     NAVBAR
     ======================================================== --}}
<nav x-data="{ scrolled: false, mobileOpen: false }"
     x-on:scroll.window="scrolled = window.scrollY > 24"
     :class="scrolled ? 'bg-white shadow-md' : 'bg-transparent'"
     class="fixed top-0 left-0 right-0 z-50 transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- Brand --}}
            <a href="/" class="flex items-center gap-2.5 group">
                <div class="flex items-center justify-center w-9 h-9 rounded-xl bg-gradient-to-br from-comsoc-600 to-comsoc-800 shadow-lg">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="leading-none">
                    <span class="block font-bold text-lg"
                          :class="scrolled ? 'text-gray-900' : 'text-white'">COMSOC Hub</span>
                    <span class="block text-[10px] font-medium tracking-wide"
                          :class="scrolled ? 'text-gray-400' : 'text-comsoc-300'">CCIS · Univ. of Makati</span>
                </div>
            </a>

            {{-- Desktop nav --}}
            <div class="hidden md:flex items-center gap-7">
                @foreach(['Features'=>'#features','How It Works'=>'#how-it-works','Officers'=>'#officers','Roles'=>'#roles'] as $label => $href)
                    <a href="{{ $href }}"
                       class="text-sm font-medium transition-colors"
                       :class="scrolled ? 'text-gray-600 hover:text-gray-900' : 'text-comsoc-200 hover:text-white'">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            {{-- Auth buttons --}}
            <div class="flex items-center gap-2">
                @auth
                    <a href="{{ url('/dashboard') }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2 bg-comsoc-600 text-white text-sm font-semibold rounded-lg hover:bg-comsoc-700 transition-colors shadow">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="text-sm font-semibold px-3 py-1.5 rounded-lg transition-colors"
                       :class="scrolled ? 'text-gray-600 hover:text-gray-900' : 'text-white hover:bg-white/10'">
                        Sign in
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                           class="inline-flex items-center gap-1 px-4 py-2 bg-comsoc-600 text-white text-sm font-semibold rounded-lg hover:bg-comsoc-700 transition-colors shadow">
                            Get started
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </div>
</nav>

{{-- ========================================================
     HERO
     ======================================================== --}}
<section class="relative min-h-screen flex items-center overflow-hidden bg-gradient-to-br from-comsoc-950 via-comsoc-900 to-comsoc-800">

    {{-- Grid --}}
    <div class="absolute inset-0 hero-grid pointer-events-none"></div>

    {{-- Orbs --}}
    <div class="absolute top-0 right-0 w-[700px] h-[700px] rounded-full pointer-events-none"
         style="background:radial-gradient(circle,rgba(22,163,74,.18) 0%,transparent 68%);transform:translate(25%,-25%)"></div>
    <div class="absolute bottom-0 left-0 w-[600px] h-[600px] rounded-full pointer-events-none"
         style="background:radial-gradient(circle,rgba(234,179,8,.10) 0%,transparent 68%);transform:translate(-25%,25%)"></div>

    {{-- Floating particles --}}
    @for ($i = 0; $i < 14; $i++)
        @php
            $size   = rand(4, 10);
            $left   = rand(5, 95);
            $delay  = rand(0, 8000);
            $dur    = rand(6000, 14000);
            $bottom = rand(0, 30);
        @endphp
        <div class="particle" style="
            width:{{ $size }}px; height:{{ $size }}px;
            left:{{ $left }}%; bottom:{{ $bottom }}%;
            animation-duration:{{ $dur }}ms;
            animation-delay:{{ $delay }}ms;
            opacity:0;
        "></div>
    @endfor

    <div class="relative w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-32">
        <div class="max-w-4xl mx-auto text-center">

            {{-- Badge --}}
            <div class="reveal inline-flex items-center gap-2 px-4 py-1.5 rounded-full border border-comsoc-600/40 bg-comsoc-900/60 backdrop-blur-sm mb-8">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-green-400"></span>
                </span>
                <span class="text-xs font-medium text-comsoc-200 tracking-wide">
                    Computer Society · CCIS · University of Makati
                </span>
            </div>

            {{-- Headline --}}
            <h1 class="reveal delay-100 text-5xl sm:text-6xl lg:text-7xl font-black text-white leading-[1.05] tracking-tight mb-6">
                Your organization,<br>
                <span class="text-gradient">one platform.</span>
            </h1>

            {{-- Sub --}}
            <p class="reveal delay-200 text-lg sm:text-xl text-comsoc-200/90 max-w-2xl mx-auto mb-10 leading-relaxed">
                COMSOC Hub brings membership, events, community, attendance, and documents
                together — so your org can focus on what actually matters.
            </p>

            {{-- Buttons --}}
            <div class="reveal delay-300 flex flex-col sm:flex-row gap-3 justify-center mb-14">
                @auth
                    <a href="{{ url('/dashboard') }}"
                       class="group inline-flex items-center justify-center gap-2 px-7 py-3.5 bg-white text-comsoc-900 font-bold rounded-xl hover:bg-comsoc-50 transition-all shadow-2xl shadow-black/30">
                        Go to Dashboard
                        <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                @else
                    <a href="{{ route('register') }}"
                       class="group inline-flex items-center justify-center gap-2 px-7 py-3.5 bg-white text-comsoc-900 font-bold rounded-xl hover:bg-comsoc-50 transition-all shadow-2xl shadow-black/30">
                        Create an account
                        <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center justify-center gap-2 px-7 py-3.5 border-2 border-white/20 text-white font-bold rounded-xl hover:bg-white/10 hover:border-white/35 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        Sign in
                    </a>
                @endauth
            </div>

            {{-- Stats strip --}}
            <div class="reveal delay-400 grid grid-cols-3 gap-px bg-white/10 rounded-2xl overflow-hidden border border-white/10 max-w-xl mx-auto">
                @foreach([['7','User Roles'],['10+','Modules'],['4','Programs']] as [$v,$l])
                    <div class="relative bg-white/5 backdrop-blur-sm px-6 py-5 stat-border text-center">
                        <p class="text-2xl font-black text-white">{{ $v }}</p>
                        <p class="text-[11px] text-comsoc-300 font-medium mt-0.5 uppercase tracking-wide">{{ $l }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Wave --}}
    <div class="absolute bottom-0 left-0 right-0 pointer-events-none">
        <svg viewBox="0 0 1440 90" preserveAspectRatio="none" class="w-full h-16 sm:h-24 block">
            <path d="M0 90L80 75C160 60 320 30 480 22C640 15 800 30 960 42C1120 55 1280 65 1360 70L1440 75V90H0Z" fill="#f9fafb"/>
        </svg>
    </div>
</section>

{{-- ========================================================
     FEATURES
     ======================================================== --}}
<section id="features" class="py-24 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16 reveal">
            <span class="inline-flex items-center px-3 py-1 rounded-full bg-comsoc-100 text-comsoc-700 text-xs font-bold tracking-widest uppercase mb-4">
                Platform Features
            </span>
            <h2 class="text-3xl sm:text-4xl font-black text-gray-900 tracking-tight">Everything COMSOC needs</h2>
            <p class="mt-4 text-gray-500 text-lg max-w-xl mx-auto">From event planning to certificate issuance — one platform handles it all.</p>
        </div>

        @php
        $features = [
            ['01','M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z','Membership Management','Academic-year membership tracking with status management, renewal history, and program association.','bg-blue-50 text-blue-600'],
            ['02','M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z','Event Management','Create, plan, and publish events with a full adviser approval workflow and real-time status tracking.','bg-comsoc-50 text-comsoc-600'],
            ['03','M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z','Event Registration','Students register directly — no Google Forms. Capacity limits and deadline enforcement built in.','bg-purple-50 text-purple-600'],
            ['04','M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z','QR Attendance','Officers scan QR passes at the door. Attendance is recorded instantly with duplicate prevention.','bg-orange-50 text-orange-600'],
            ['05','M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z','Digital Certificates','Eligible attendees automatically receive certificates they can view and download.','bg-yellow-50 text-yellow-600'],
            ['06','M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z','COMSOC Community','A private org feed for announcements, discussions, and event updates — not a social media clone.','bg-pink-50 text-pink-600'],
        ];
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($features as $i => [$num, $icon, $title, $desc, $color])
                <div class="lift reveal bg-white rounded-2xl border border-gray-100 p-7 shadow-sm group delay-{{ min($i * 100, 500) }}">
                    <div class="flex items-start justify-between mb-5">
                        <div class="flex items-center justify-center w-12 h-12 rounded-xl {{ $color }}">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $icon }}"/>
                            </svg>
                        </div>
                        <span class="text-4xl font-black text-gray-100 group-hover:text-gray-200 transition-colors select-none">{{ $num }}</span>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 mb-2">{{ $title }}</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ========================================================
     HOW IT WORKS
     ======================================================== --}}
<section id="how-it-works" class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16 reveal">
            <span class="inline-flex items-center px-3 py-1 rounded-full bg-comsoc-100 text-comsoc-700 text-xs font-bold tracking-widest uppercase mb-4">
                How It Works
            </span>
            <h2 class="text-3xl sm:text-4xl font-black text-gray-900 tracking-tight">From sign-up to certificate</h2>
            <p class="mt-4 text-gray-500 text-lg max-w-xl mx-auto">The full student journey — entirely within COMSOC Hub.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">
            @foreach([
                ['1','M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z','Register & Join','Create your account, choose your program, and become part of COMSOC.'],
                ['2','M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z','Discover Events','Browse published events, view details, and register directly on the platform.'],
                ['3','M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z','Attend & Check In','Show your QR pass. Officers scan it — attendance recorded instantly.'],
                ['4','M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z','Get Your Certificate','If eligible, your certificate is automatically issued and downloadable.'],
            ] as $idx => [$step,$icon,$title,$desc])
                <div class="relative text-center reveal delay-{{ $idx * 100 }}">
                    @if ($idx < 3)
                        <div class="hidden lg:block absolute top-8 left-[calc(50%+40px)] right-0 h-px z-0"
                             style="background:linear-gradient(90deg,#bbf7d0,transparent)"></div>
                    @endif
                    <div class="relative z-10 flex flex-col items-center">
                        <div class="relative mb-5">
                            <div class="flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-comsoc-600 to-comsoc-800 shadow-lg shadow-comsoc-900/20">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $icon }}"/>
                                </svg>
                            </div>
                            <span class="absolute -top-2 -right-2 w-6 h-6 rounded-full bg-gold-400 text-comsoc-900 text-xs font-black flex items-center justify-center shadow-md">{{ $step }}</span>
                        </div>
                        <h3 class="text-base font-bold text-gray-900 mb-2">{{ $title }}</h3>
                        <p class="text-sm text-gray-500 leading-relaxed max-w-[200px]">{{ $desc }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ========================================================
     OFFICERS SECTION
     ======================================================== --}}
<section id="officers" class="py-24 bg-comsoc-950 relative overflow-hidden">
    <div class="absolute inset-0 hero-grid pointer-events-none opacity-40"></div>
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] pointer-events-none"
         style="background:radial-gradient(ellipse,rgba(22,163,74,0.13) 0%,transparent 70%)"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="text-center mb-16 reveal">
            <span class="inline-flex items-center px-3 py-1 rounded-full bg-comsoc-800 text-comsoc-300 text-xs font-bold tracking-widest uppercase mb-4">
                Academic Year 2025–2026
            </span>
            <h2 class="text-3xl sm:text-4xl font-black text-white tracking-tight">Meet the Officers</h2>
            <p class="mt-4 text-comsoc-300 text-lg max-w-xl mx-auto">
                The elected leaders of the Computer Society — CCIS, University of Makati.
            </p>
        </div>

        @php
        // Executive Officers
        $executives = [
            ['President',                          'Alexandra Reyes',         'CS'],
            ['Vice President for Internal Affairs','Benjamin Cruz',           'IT'],
            ['Vice President for External Affairs','Christine Lim',           'IT'],
            ['Secretary',                          'Daniel Ocampo',           'CS'],
            ['Treasurer',                          'Erica Santos',            'IS'],
            ['Auditor',                            'Francis Dela Torre',      'CS'],
            ['Public Relations Officer',           'Gabrielle Mendoza',       'IT'],
        ];

        // Program Representatives
        $progReps = [
            ['Program Representative',  'Harold Bautista',   'BSCS', 'Computer Science'],
            ['Program Representative',  'Isabelle Tan',      'BSIT', 'Information Technology'],
            ['Program Representative',  'James Navarro',     'DIT',  'Diploma in IT'],
            ['Program Representative',  'Karen Villanueva',  'BSIS', 'Information Systems'],
        ];

        $roleColors = [
            'President'                            => 'from-yellow-600 to-yellow-800',
            'Vice President for Internal Affairs'  => 'from-comsoc-600 to-comsoc-800',
            'Vice President for External Affairs'  => 'from-comsoc-600 to-comsoc-800',
            'Secretary'                            => 'from-blue-600 to-blue-800',
            'Treasurer'                            => 'from-emerald-600 to-emerald-800',
            'Auditor'                              => 'from-red-600 to-red-800',
            'Public Relations Officer'             => 'from-pink-600 to-pink-800',
            'Program Representative'               => 'from-purple-600 to-purple-800',
        ];
        @endphp

        {{-- Executive Officers --}}
        <div class="mb-12">
            <p class="text-comsoc-400 text-xs font-bold tracking-widest uppercase text-center mb-8">Executive Officers</p>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-5">
                @foreach ($executives as $idx => [$position, $name, $program])
                    <div class="reveal delay-{{ min($idx * 100, 600) }} lift bg-comsoc-900/50 border border-comsoc-700/40 rounded-2xl p-5 text-center hover:border-comsoc-500/60 transition-all">
                        {{-- Photo --}}
                        <div class="flex justify-center mb-4">
                            <div class="officer-ring">
                                <div class="officer-ring-inner w-20 h-20">
                                    <div class="photo-placeholder w-20 h-20 flex items-center justify-center">
                                        <svg class="w-9 h-9 text-comsoc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- Info --}}
                        <p class="font-bold text-white text-sm leading-tight">{{ $name }}</p>
                        <span class="inline-flex items-center mt-2 px-2 py-0.5 rounded-full text-[10px] font-bold
                                     bg-gradient-to-r {{ $roleColors[$position] }} text-white">
                            {{ $position === 'Public Relations Officer' ? 'PRO' : explode(' ', $position)[0] }}
                        </span>
                        <p class="text-comsoc-500 text-[11px] mt-2 leading-tight">{{ $position }}</p>
                        <p class="text-comsoc-600 text-[10px] mt-1">{{ $program }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Program Representatives --}}
        <div>
            <p class="text-comsoc-400 text-xs font-bold tracking-widest uppercase text-center mb-8">Program Representatives</p>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-5 max-w-3xl mx-auto">
                @foreach ($progReps as $idx => [$position, $name, $code, $program])
                    <div class="reveal delay-{{ $idx * 100 }} lift bg-comsoc-900/50 border border-comsoc-700/40 rounded-2xl p-5 text-center hover:border-purple-500/60 transition-all">
                        <div class="flex justify-center mb-4">
                            <div class="officer-ring">
                                <div class="officer-ring-inner w-16 h-16">
                                    <div class="photo-placeholder w-16 h-16 flex items-center justify-center">
                                        <svg class="w-7 h-7 text-comsoc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="font-bold text-white text-sm leading-tight">{{ $name }}</p>
                        <span class="inline-flex items-center mt-2 px-2 py-0.5 rounded-full text-[10px] font-bold bg-gradient-to-r from-purple-600 to-purple-800 text-white">
                            {{ $code }}
                        </span>
                        <p class="text-comsoc-500 text-[11px] mt-2 leading-tight">{{ $program }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Photo upload note --}}
        <p class="text-center text-comsoc-600 text-xs mt-10 reveal">
            <svg class="w-3.5 h-3.5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Officer photos will be uploaded via the admin dashboard.
        </p>
    </div>
</section>

{{-- ========================================================
     ROLES
     ======================================================== --}}
<section id="roles" class="py-24 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16 reveal">
            <span class="inline-flex items-center px-3 py-1 rounded-full bg-comsoc-100 text-comsoc-700 text-xs font-bold tracking-widest uppercase mb-4">
                Role-Based Access
            </span>
            <h2 class="text-3xl sm:text-4xl font-black text-gray-900 tracking-tight">Built for everyone in COMSOC</h2>
            <p class="mt-4 text-gray-500 text-lg max-w-xl mx-auto">Every user sees exactly what they need — no more, no less.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach([
                ['Student',       'M12 14l9-5-9-5-9 5 9 5z',                                                                                                                                                                                                              ['Browse & register for events','View attendance history','Download certificates','Community feed'],                   'bg-blue-50 text-blue-700 border-blue-100'],
                ['Member',        'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',                           ['All student features','Membership dashboard','Community posting','Member-only content'],                             'bg-comsoc-50 text-comsoc-700 border-comsoc-100'],
                ['Officer',       'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',                                       ['Create & manage events','Committee management','Document submission','Task tracking'],                               'bg-purple-50 text-purple-700 border-purple-100'],
                ['President',     'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z',  ['Organization overview','Officer management','Event monitoring','Full reports'],                                     'bg-yellow-50 text-yellow-700 border-yellow-100'],
                ['Adviser',       'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',                                                                                      ['Review event proposals','Approve/reject events','Document approvals','Approval history'],                          'bg-teal-50 text-teal-700 border-teal-100'],
                ['Prog. Rep',     'M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z',                                                                                                                                        ['Program communications','Student outreach','Assigned tasks','Community announcements'],                             'bg-pink-50 text-pink-700 border-pink-100'],
                ['Admin',         'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z', ['User & role management','System configuration','Audit log access','Full platform control'],                       'bg-red-50 text-red-700 border-red-100'],
            ] as $ri => [$role, $icon, $perms, $badge])
                <div class="lift reveal delay-{{ min($ri * 100, 600) }} bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="flex items-center justify-center w-10 h-10 rounded-xl {{ $badge }} border">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
                            </svg>
                        </div>
                        <span class="font-bold text-gray-900 text-sm">{{ $role }}</span>
                    </div>
                    <ul class="space-y-2">
                        @foreach($perms as $perm)
                            <li class="flex items-center gap-2 text-xs text-gray-500">
                                <svg class="w-3 h-3 text-comsoc-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                </svg>
                                {{ $perm }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ========================================================
     CTA
     ======================================================== --}}
<section class="py-24 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="reveal-scale relative bg-gradient-to-br from-comsoc-800 via-comsoc-900 to-comsoc-950 rounded-3xl overflow-hidden p-10 sm:p-16 text-center shadow-2xl">
            <div class="absolute inset-0 hero-grid opacity-25 pointer-events-none"></div>
            <div class="absolute -top-24 -right-24 w-72 h-72 rounded-full pointer-events-none"
                 style="background:radial-gradient(circle,rgba(234,179,8,.18) 0%,transparent 70%)"></div>
            <div class="absolute -bottom-24 -left-24 w-72 h-72 rounded-full pointer-events-none"
                 style="background:radial-gradient(circle,rgba(22,163,74,.18) 0%,transparent 70%)"></div>
            <div class="relative z-10">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-white/10 backdrop-blur mb-6">
                    <svg class="w-8 h-8 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                              d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h2 class="text-3xl sm:text-4xl font-black text-white mb-4 tracking-tight">Ready to join COMSOC Hub?</h2>
                <p class="text-comsoc-200 text-lg mb-8 max-w-xl mx-auto">
                    Connect with the Computer Society of CCIS and manage everything in one place.
                </p>
                @auth
                    <a href="{{ url('/dashboard') }}"
                       class="inline-flex items-center gap-2 px-8 py-4 bg-white text-comsoc-900 font-bold rounded-xl hover:bg-comsoc-50 transition-all shadow-xl text-base">
                        Go to Dashboard
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                @else
                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <a href="{{ route('register') }}"
                           class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-white text-comsoc-900 font-bold rounded-xl hover:bg-comsoc-50 transition-all shadow-xl text-base">
                            Create a free account
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </a>
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center justify-center px-8 py-4 border-2 border-white/20 text-white font-bold rounded-xl hover:bg-white/10 transition-all text-base">
                            Sign in
                        </a>
                    </div>
                    <p class="mt-5 text-comsoc-500 text-sm">Free to join · No payment required</p>
                @endauth
            </div>
        </div>
    </div>
</section>

{{-- ========================================================
     FOOTER
     ======================================================== --}}
<footer class="bg-gray-950 border-t border-gray-800/60">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10 mb-10">
            <div>
                <div class="flex items-center gap-2.5 mb-4">
                    <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-comsoc-700">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <span class="font-bold text-white text-base">COMSOC Hub</span>
                </div>
                <p class="text-sm text-gray-500 leading-relaxed">
                    The centralized digital platform for the Computer Society of CCIS, University of Makati.
                </p>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-5">Platform</p>
                <ul class="space-y-3">
                    @foreach(['Features'=>'#features','How It Works'=>'#how-it-works','Officers'=>'#officers','Roles'=>'#roles'] as $l=>$h)
                        <li><a href="{{ $h }}" class="text-sm text-gray-500 hover:text-white transition-colors">{{ $l }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-5">Account</p>
                <ul class="space-y-3">
                    @auth
                        <li><a href="{{ url('/dashboard') }}" class="text-sm text-gray-500 hover:text-white transition-colors">Dashboard</a></li>
                        <li><a href="{{ route('profile.edit') }}" class="text-sm text-gray-500 hover:text-white transition-colors">Profile</a></li>
                    @else
                        <li><a href="{{ route('login') }}" class="text-sm text-gray-500 hover:text-white transition-colors">Sign in</a></li>
                        <li><a href="{{ route('register') }}" class="text-sm text-gray-500 hover:text-white transition-colors">Create account</a></li>
                    @endauth
                </ul>
            </div>
        </div>
        <div class="border-t border-gray-800/60 pt-8 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-xs text-gray-600">&copy; {{ date('Y') }} Computer Society · CCIS · University of Makati. All rights reserved.</p>
            <p class="text-xs text-gray-700">COMSOC Hub v1.0</p>
        </div>
    </div>
</footer>

{{-- ========================================================
     SCROLL REVEAL SCRIPT
     ======================================================== --}}
<script>
(function () {
    const selectors = ['.reveal', '.reveal-left', '.reveal-right', '.reveal-scale'];
    const targets = document.querySelectorAll(selectors.join(','));

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

    targets.forEach(el => observer.observe(el));

    // Trigger elements already in view on load
    setTimeout(() => {
        targets.forEach(el => {
            const rect = el.getBoundingClientRect();
            if (rect.top < window.innerHeight) el.classList.add('visible');
        });
    }, 80);
})();
</script>

</body>
</html>
