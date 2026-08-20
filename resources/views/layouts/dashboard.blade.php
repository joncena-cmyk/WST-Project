<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'COMSOC Hub') }}@isset($title) — {{ $title }}@endisset</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Topbar gold accent */
        .topbar-accent { background: linear-gradient(90deg, #166534, #14532d); }
        /* Active sidebar item glow */
        .nav-active { box-shadow: inset 3px 0 0 #facc15; }
        /* Page transition */
        .page-content { animation: fadeUp .25s ease both; }
        @keyframes fadeUp {
            from { opacity:0; transform:translateY(8px); }
            to   { opacity:1; transform:translateY(0); }
        }
        /* Scrollbar */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 9999px; }
        ::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">

<div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden">

    {{-- ===== SIDEBAR (desktop) ===== --}}
    <div class="hidden lg:flex shrink-0">
        @include('layouts.partials.sidebar')
    </div>

    {{-- ===== MOBILE SIDEBAR OVERLAY ===== --}}
    <div x-show="sidebarOpen" x-cloak
         @click="sidebarOpen = false"
         class="fixed inset-0 z-40 bg-comsoc-950/70 backdrop-blur-sm lg:hidden"></div>

    <div x-show="sidebarOpen" x-cloak
         x-transition:enter="transition ease-out duration-250"
         x-transition:enter-start="-translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="-translate-x-full"
         class="fixed inset-y-0 left-0 z-50 flex lg:hidden shadow-2xl">
        @include('layouts.partials.sidebar')
    </div>

    {{-- ===== MAIN CONTENT AREA ===== --}}
    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">

        {{-- Topbar --}}
        <header class="flex items-center justify-between h-14 px-4 sm:px-6 bg-white border-b border-gray-200 shrink-0 shadow-sm">

            {{-- Left: mobile toggle + page title --}}
            <div class="flex items-center gap-3 min-w-0">
                <button @click="sidebarOpen = !sidebarOpen"
                        class="lg:hidden flex items-center justify-center w-9 h-9 rounded-lg text-gray-500 hover:text-comsoc-700 hover:bg-comsoc-50 transition-colors shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                @isset($header)
                    <div class="min-w-0 flex items-center gap-2">
                        <span class="w-1 h-5 rounded-full bg-comsoc-600 shrink-0"></span>
                        <div class="min-w-0">{{ $header }}</div>
                    </div>
                @else
                    <div class="flex items-center gap-2">
                        <span class="w-1 h-5 rounded-full bg-comsoc-600 shrink-0"></span>
                        <h1 class="text-base font-bold text-gray-900 truncate">Dashboard</h1>
                    </div>
                @endisset
            </div>

            {{-- Right: date + user mini --}}
            <div class="flex items-center gap-3 shrink-0">
                <span class="hidden sm:flex items-center gap-1.5 text-xs text-gray-400 font-medium">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    {{ now()->format('D, M d Y') }}
                </span>

                {{-- User avatar dropdown --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open"
                            class="flex items-center gap-2 p-1 rounded-lg hover:bg-gray-100 transition-colors">
                        <img src="{{ Auth::user()->getProfilePictureUrl() }}"
                             alt="{{ Auth::user()->name }}"
                             class="w-8 h-8 rounded-full object-cover border-2 border-comsoc-200"/>
                        <span class="hidden sm:block text-xs font-semibold text-gray-700 max-w-[100px] truncate">
                            {{ Auth::user()->name }}
                        </span>
                        <svg class="w-3.5 h-3.5 text-gray-400 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="open" x-cloak @click.outside="open = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="absolute right-0 mt-2 w-52 bg-white border border-gray-200 rounded-xl shadow-lg z-30 overflow-hidden">

                        {{-- User info header --}}
                        <div class="px-4 py-3 bg-comsoc-50 border-b border-comsoc-100">
                            <p class="text-xs font-bold text-comsoc-800 truncate">{{ Auth::user()->name }}</p>
                            <p class="text-[11px] text-comsoc-500 mt-0.5">{{ Auth::user()->getRoleLabel() }}</p>
                        </div>

                        <div class="py-1">
                            <a href="{{ route('profile.edit') }}"
                               class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Profile Settings
                            </a>
                            <a href="{{ route('dashboard') }}"
                               class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                                Dashboard
                            </a>
                        </div>

                        <div class="border-t border-gray-100 py-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        {{-- Page content --}}
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 page-content">
            {{ $slot }}
        </main>
    </div>
</div>

</body>
</html>
