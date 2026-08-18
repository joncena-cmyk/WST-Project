<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'COMSOC Hub') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex">

            {{-- Left panel — branding (hidden on mobile) --}}
            <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-comsoc-950 via-comsoc-900 to-comsoc-800 flex-col justify-between p-12 relative overflow-hidden">

                {{-- Decorative circles --}}
                <div class="absolute -top-24 -left-24 w-96 h-96 rounded-full bg-comsoc-700/20"></div>
                <div class="absolute -bottom-32 -right-16 w-[28rem] h-[28rem] rounded-full bg-comsoc-600/10"></div>
                <div class="absolute top-1/2 right-0 w-64 h-64 rounded-full bg-comsoc-800/30 translate-x-1/2 -translate-y-1/2"></div>

                {{-- Logo & brand --}}
                <div class="relative z-10 flex items-center gap-3">
                    <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-comsoc-600 shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-white font-bold text-lg leading-none">COMSOC Hub</p>
                        <p class="text-comsoc-300 text-xs mt-0.5">CCIS · University of Makati</p>                    </div>
                </div>

                {{-- Centre content --}}
                <div class="relative z-10 space-y-6">
                    <div class="space-y-3">
                        <h1 class="text-4xl font-bold text-white leading-tight">
                            Your organization,<br>one platform.
                        </h1>
                        <p class="text-comsoc-200 text-base leading-relaxed max-w-sm">
                            COMSOC Hub centralizes membership, events, community, attendance, and documents
                            for the Computer Society of CCIS.
                        </p>
                    </div>

                    {{-- Feature list --}}
                    <ul class="space-y-3">
                        @foreach ([
                            ['icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'text' => 'Membership management'],
                            ['icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'text' => 'Events & attendance'],
                            ['icon' => 'M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z', 'text' => 'Community & announcements'],
                            ['icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'text' => 'Documents & approvals'],
                        ] as $feature)
                            <li class="flex items-center gap-3 text-comsoc-200 text-sm">
                                <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-comsoc-700/60 shrink-0">
                                    <svg class="w-4 h-4 text-comsoc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $feature['icon'] }}"/>
                                    </svg>
                                </span>
                                {{ $feature['text'] }}
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Footer --}}
                <div class="relative z-10 text-comsoc-500 text-xs">
                    &copy; {{ date('Y') }} COMSOC · CCIS · University of Makati
                </div>
            </div>

            {{-- Right panel — form --}}
            <div class="flex-1 flex flex-col justify-center items-center px-6 py-12 bg-gray-50">

                {{-- Mobile brand header (shown only on mobile) --}}
                <div class="lg:hidden flex items-center gap-2 mb-8">
                    <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-comsoc-600">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <span class="font-bold text-comsoc-900 text-lg">COMSOC Hub</span>
                </div>

                <div class="w-full max-w-md">
                    {{ $slot }}
                </div>
            </div>

        </div>
    </body>
</html>
