<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'COMSOC Hub') }}@isset($title) — {{ $title }}@endisset</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50">
        <div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden">

            {{-- Sidebar (desktop) --}}
            <div class="hidden lg:flex shrink-0">
                @include('layouts.partials.sidebar')
            </div>

            {{-- Mobile sidebar overlay --}}
            <div x-show="sidebarOpen" x-cloak
                 class="fixed inset-0 z-40 bg-gray-900/50 lg:hidden"
                 @click="sidebarOpen = false"></div>
            <div x-show="sidebarOpen" x-cloak
                 x-transition:enter="transition ease-in-out duration-300"
                 x-transition:enter-start="-translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition ease-in-out duration-300"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="-translate-x-full"
                 class="fixed inset-y-0 left-0 z-50 flex lg:hidden">
                @include('layouts.partials.sidebar')
            </div>

            {{-- Main content --}}
            <div class="flex flex-col flex-1 min-w-0 overflow-hidden">

                {{-- Top bar --}}
                <header class="flex items-center justify-between h-16 px-4 sm:px-6 bg-white border-b border-gray-200 shrink-0">
                    <div class="flex items-center gap-3 min-w-0">
                        <button
                            @click="sidebarOpen = !sidebarOpen"
                            class="lg:hidden p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>

                        @isset($header)
                            <div class="min-w-0">{{ $header }}</div>
                        @else
                            <h1 class="text-lg font-semibold text-gray-800 truncate">Dashboard</h1>
                        @endisset
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        <span class="hidden sm:block text-sm text-gray-500">{{ now()->format('M d, Y') }}</span>
                    </div>
                </header>

                {{-- Page content --}}
                <main class="flex-1 overflow-y-auto p-4 sm:p-6">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
