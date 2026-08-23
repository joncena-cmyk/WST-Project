<x-dashboard-layout>
    <x-slot name="header">
        <h1 class="text-base font-bold text-gray-900">Community</h1>
    </x-slot>

    {{-- Mobile-first three-column layout: left sidebar (hidden on mobile), feed (full width mobile → centered desktop), right panel (hidden on mobile) --}}
    <div class="max-w-screen-xl mx-auto px-0 sm:px-2">
        <div class="flex gap-6 items-start">

            {{-- ===== LEFT SIDEBAR (lg+ only) ===== --}}
            <div class="hidden lg:block w-64 xl:w-72 shrink-0 sticky top-4">
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-100 bg-gradient-to-r from-comsoc-900 to-comsoc-800 flex items-center gap-3">
                        <img src="{{ Auth::user()->getProfilePictureUrl() }}"
                             class="w-10 h-10 rounded-full object-cover border-2 border-gold-400" alt=""/>
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-white truncate">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gold-400 font-medium truncate">{{ Auth::user()->getRoleLabel() }}</p>
                        </div>
                    </div>
                    <nav class="p-3 space-y-0.5">
                        @foreach([
                            ['label' => 'Community Feed', 'icon' => 'M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z', 'active' => true],
                            ['label' => 'Events', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'active' => false],
                            ['label' => 'Membership', 'icon' => 'M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0', 'active' => false],
                        ] as $item)
                            <a href="{{ $item['active'] ? '#' : ($item['label'] === 'Events' ? route('events.index') : route('membership.index')) }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all {{ $item['active'] ? 'bg-comsoc-50 text-comsoc-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                                <span class="w-9 h-9 rounded-xl {{ $item['active'] ? 'bg-comsoc-600 text-white' : 'bg-gray-100 text-gray-500' }} flex items-center justify-center shrink-0 transition-all">
                                    <svg class="w-4.5 h-4.5" style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                                    </svg>
                                </span>
                                {{ $item['label'] }}
                            </a>
                        @endforeach
                    </nav>

                    {{-- Online Members count --}}
                    <div class="px-4 py-3 border-t border-gray-100 bg-gray-50/50">
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wide mb-1">Academic Year</p>
                        <p class="text-sm font-bold text-comsoc-700">2026 – 2027</p>
                    </div>
                </div>
            </div>

            {{-- ===== MAIN FEED ===== --}}
            <div class="flex-1 min-w-0 max-w-[680px] mx-auto w-full space-y-4 pb-6">

                {{-- Flash messages --}}
                @if (session('success'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3500)"
                         x-transition:leave="transition ease-in duration-300"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-2"
                         class="flex items-center gap-3 p-3.5 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-700 text-sm shadow-sm">
                        <div class="w-7 h-7 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                @endif

                {{-- Create post box --}}
                @can('create', App\Models\Post::class)
                    @include('community.partials.create-post-form')
                @endcan

                {{-- Stories / Category pills row (mobile-friendly scrollable) --}}
                <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-hide -mx-1 px-1">
                    @foreach([
                        ['label' => 'All Posts', 'active' => true, 'icon' => '🏠'],
                        ['label' => 'Announcements', 'active' => false, 'icon' => '📢'],
                        ['label' => 'Events', 'active' => false, 'icon' => '🎉'],
                        ['label' => 'Achievements', 'active' => false, 'icon' => '🏆'],
                    ] as $cat)
                        <button class="flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-semibold whitespace-nowrap shrink-0 transition-all
                            {{ $cat['active']
                                ? 'bg-comsoc-700 text-white shadow-sm'
                                : 'bg-white border border-gray-200 text-gray-600 hover:border-comsoc-300 hover:text-comsoc-700' }}">
                            <span>{{ $cat['icon'] }}</span>
                            {{ $cat['label'] }}
                        </button>
                    @endforeach
                </div>

                {{-- Feed --}}
                @forelse ($posts as $post)
                    @include('community.partials.post-card', compact('post'))
                @empty
                    <div class="bg-white border border-gray-200 rounded-2xl p-12 text-center shadow-sm">
                        <div class="w-16 h-16 rounded-2xl bg-comsoc-50 text-comsoc-400 flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
                            </svg>
                        </div>
                        <p class="text-gray-700 font-bold text-base">Nothing here yet</p>
                        <p class="text-gray-400 text-sm mt-1">Be the first to share something with COMSOC!</p>
                    </div>
                @endforelse

                {{-- Pagination --}}
                <div class="pb-4">
                    {{ $posts->links() }}
                </div>
            </div>

            {{-- ===== RIGHT PANEL (xl+ only) ===== --}}
            <div class="hidden xl:block w-64 xl:w-72 shrink-0 sticky top-4 space-y-4">

                {{-- COMSOC Info Card --}}
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="h-16 bg-gradient-to-br from-comsoc-800 to-comsoc-950 relative">
                        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#facc15 1px, transparent 1px); background-size: 12px 12px;"></div>
                    </div>
                    <div class="px-4 -mt-6 pb-4">
                        <div class="w-12 h-12 rounded-xl bg-white border-2 border-comsoc-600 overflow-hidden shadow-md mb-2">
                            <img src="{{ asset('images/comsoc_logo.png') }}" alt="COMSOC" class="w-full h-full object-contain p-1"/>
                        </div>
                        <h3 class="text-sm font-black text-gray-900">COMSOC Hub</h3>
                        <p class="text-xs text-gray-500 mt-0.5">CCIS · University of Makati</p>
                        <p class="text-[11px] text-gray-500 mt-2 leading-relaxed">
                            The official digital platform of the Computer Society of CCIS, University of Makati.
                        </p>
                    </div>
                </div>

                {{-- Quick Links --}}
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4">
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">Quick Access</h3>
                    <div class="space-y-1.5">
                        @foreach([
                            ['Events', route('events.index'), 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                            ['My Membership', route('membership.index'), 'M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1'],
                            ['Certificates', route('certificates.index'), 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622'],
                        ] as [$label, $url, $icon])
                            <a href="{{ $url }}" class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-gray-50 transition text-gray-600 hover:text-comsoc-700 group">
                                <span class="w-8 h-8 rounded-lg bg-gray-100 group-hover:bg-comsoc-100 text-gray-500 group-hover:text-comsoc-700 flex items-center justify-center shrink-0 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
                                    </svg>
                                </span>
                                <span class="text-sm font-semibold">{{ $label }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

            </div>

        </div>
    </div>

</x-dashboard-layout>
