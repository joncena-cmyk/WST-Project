<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'COMSOC Hub') }} — CCIS · University of Makati</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-white text-gray-900">

        {{-- ===== NAVBAR ===== --}}
        <nav class="sticky top-0 z-50 bg-white/95 backdrop-blur border-b border-gray-100">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 flex items-center justify-between h-16">
                <div class="flex items-center gap-2.5">
                    <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-comsoc-600">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <span class="font-bold text-comsoc-900 text-base">COMSOC Hub</span>
                </div>

                @if (Route::has('login'))
                    <div class="flex items-center gap-3">
                        @auth
                            <a href="{{ url('/dashboard') }}"
                               class="inline-flex items-center px-4 py-2 bg-comsoc-600 text-white text-sm font-medium rounded-lg hover:bg-comsoc-700 transition-colors">
                                Go to Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                               class="text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                                Sign in
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                   class="inline-flex items-center px-4 py-2 bg-comsoc-600 text-white text-sm font-medium rounded-lg hover:bg-comsoc-700 transition-colors">
                                    Get started
                                </a>
                            @endif
                        @endauth
                    </div>
                @endif
            </div>
        </nav>

        {{-- ===== HERO ===== --}}
        <section class="relative overflow-hidden bg-gradient-to-br from-comsoc-950 via-comsoc-900 to-comsoc-800">
            {{-- Background decoration --}}
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute -top-40 -right-40 w-[600px] h-[600px] rounded-full bg-comsoc-700/20"></div>
                <div class="absolute -bottom-40 -left-20 w-[500px] h-[500px] rounded-full bg-comsoc-800/30"></div>
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] rounded-full bg-comsoc-600/5"></div>
            </div>

            <div class="relative max-w-6xl mx-auto px-4 sm:px-6 py-24 sm:py-32 text-center">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-comsoc-800/70 border border-gold-500/50 text-gold-300 text-xs font-medium mb-6">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse"></span>
                    CCIS · University of Makati
                </span>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-tight mb-6">
                    The home of<br>
                    <span class="text-gold-400">Computer Society</span>
                </h1>
                <p class="text-comsoc-200 text-lg sm:text-xl max-w-2xl mx-auto mb-10 leading-relaxed">
                    COMSOC Hub is the centralized platform for membership, events, community,
                    attendance tracking, and organizational management.
                </p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    @auth
                        <a href="{{ url('/dashboard') }}"
                           class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-white text-comsoc-800 font-semibold rounded-xl hover:bg-comsoc-50 transition-colors shadow-lg">
                            Go to Dashboard
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </a>
                    @else
                        <a href="{{ route('register') }}"
                           class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-white text-comsoc-800 font-semibold rounded-xl hover:bg-comsoc-50 transition-colors shadow-lg">
                            Create an account
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </a>
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center justify-center px-6 py-3 border border-comsoc-500 text-comsoc-200 font-semibold rounded-xl hover:bg-comsoc-800 transition-colors">
                            Sign in
                        </a>
                    @endauth
                </div>
            </div>
        </section>

        {{-- ===== FEATURES ===== --}}
        <section class="py-20 bg-gray-50">
            <div class="max-w-6xl mx-auto px-4 sm:px-6">
                <div class="text-center mb-14">
                    <h2 class="text-3xl font-bold text-gray-900">Everything COMSOC needs</h2>
                    <p class="mt-3 text-gray-500 max-w-xl mx-auto">
                        From registration to certificates — one platform handles it all.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ([
                        [
                            'icon'  => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
                            'title' => 'Membership',
                            'desc'  => 'Academic-year membership tracking with status management for all COMSOC members.',
                        ],
                        [
                            'icon'  => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                            'title' => 'Events',
                            'desc'  => 'Create, plan, and publish events with an approval workflow through the organization adviser.',
                        ],
                        [
                            'icon'  => 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z',
                            'title' => 'Registration',
                            'desc'  => 'Students register for events directly in the system — no Google Forms needed.',
                        ],
                        [
                            'icon'  => 'M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z',
                            'title' => 'QR Attendance',
                            'desc'  => 'Officers scan student QR passes at the door — attendance is recorded automatically.',
                        ],
                        [
                            'icon'  => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z',
                            'title' => 'Certificates',
                            'desc'  => 'Eligible attendees automatically get certificates they can view and download.',
                        ],
                        [
                            'icon'  => 'M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z',
                            'title' => 'Community',
                            'desc'  => 'A private org feed for announcements, discussions, and event updates — not another social media.',
                        ],
                    ] as $feature)
                        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-center w-11 h-11 rounded-xl bg-comsoc-50 mb-4">
                                <svg class="w-5 h-5 text-comsoc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $feature['icon'] }}"/>
                                </svg>
                            </div>
                            <h3 class="text-base font-semibold text-gray-900 mb-1">{{ $feature['title'] }}</h3>
                            <p class="text-sm text-gray-500 leading-relaxed">{{ $feature['desc'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ===== ROLES SECTION ===== --}}
        <section class="py-20 bg-white">
            <div class="max-w-6xl mx-auto px-4 sm:px-6">
                <div class="text-center mb-14">
                    <h2 class="text-3xl font-bold text-gray-900">Built for everyone in COMSOC</h2>
                    <p class="mt-3 text-gray-500 max-w-xl mx-auto">
                        Role-based access means every user sees exactly what they need.
                    </p>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach ([
                        ['role' => 'Students',    'desc' => 'Browse events, register, track attendance & certificates'],
                        ['role' => 'Members',     'desc' => 'Full member access plus membership features'],
                        ['role' => 'Officers',    'desc' => 'Manage events, committees, documents & tasks'],
                        ['role' => 'President',   'desc' => 'Org-wide oversight and officer management'],
                        ['role' => 'Adviser',     'desc' => 'Review and approve events and documents'],
                        ['role' => 'Prog. Reps',  'desc' => 'Program-specific communication and tasks'],
                        ['role' => 'Admin',       'desc' => 'User management, roles, permissions & audit logs'],
                    ] as $r)
                        <div class="bg-gray-50 rounded-xl border border-gray-200 p-4">
                            <p class="text-sm font-semibold text-comsoc-700">{{ $r['role'] }}</p>
                            <p class="text-xs text-gray-500 mt-1 leading-relaxed">{{ $r['desc'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ===== CTA ===== --}}
        <section class="py-20 bg-gradient-to-br from-comsoc-900 to-comsoc-800">
            <div class="max-w-2xl mx-auto px-4 sm:px-6 text-center">
                <h2 class="text-3xl font-bold text-white mb-4">Ready to get started?</h2>
                <p class="text-comsoc-200 mb-8">
                    Join COMSOC Hub and connect with the Computer Society of CCIS.
                </p>
                @auth
                    <a href="{{ url('/dashboard') }}"
                       class="inline-flex items-center justify-center gap-2 px-8 py-3 bg-white text-comsoc-800 font-semibold rounded-xl hover:bg-comsoc-50 transition-colors shadow-lg">
                        Go to Dashboard
                    </a>
                @else
                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <a href="{{ route('register') }}"
                           class="inline-flex items-center justify-center px-8 py-3 bg-white text-comsoc-800 font-semibold rounded-xl hover:bg-comsoc-50 transition-colors shadow-lg">
                            Create an account
                        </a>
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center justify-center px-8 py-3 border border-comsoc-500 text-comsoc-200 font-semibold rounded-xl hover:bg-comsoc-800 transition-colors">
                            Sign in
                        </a>
                    </div>
                @endauth
            </div>
        </section>

        {{-- ===== FOOTER ===== --}}
        <footer class="py-8 bg-comsoc-950 border-t border-comsoc-900">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <div class="flex items-center justify-center w-6 h-6 rounded bg-comsoc-700">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <span class="text-comsoc-300 text-sm font-medium">COMSOC Hub</span>
                </div>
                <p class="text-comsoc-500 text-xs text-center">
                    &copy; {{ date('Y') }} Computer Society · College of Computing and Information Sciences · University of Makati
                </p>
            </div>
        </footer>

    </body>
</html>
