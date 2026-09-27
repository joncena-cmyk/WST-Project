<x-dashboard-layout>
    <x-slot name="header">
        <h1 class="text-base font-bold text-gray-900">Community</h1>
    </x-slot>

    {{-- Clean Streamlined Community Hub Layout --}}
    <div class="max-w-6xl mx-auto px-0 sm:px-2">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            {{-- ===== MAIN FEED COLUMN ===== --}}
            <div class="lg:col-span-8 space-y-4 pb-8 min-w-0">

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

                {{-- Category filter pills --}}
                <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-hide -mx-1 px-1">
                    <a href="{{ route('community.index') }}"
                       class="px-4 py-2 rounded-full text-xs font-bold whitespace-nowrap shrink-0 transition-all flex items-center gap-1.5 {{ ($filter ?? 'all') === 'all' ? 'bg-comsoc-700 text-white shadow-xs' : 'bg-white border border-gray-200 text-gray-600 hover:border-comsoc-300 hover:text-comsoc-700' }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        <span>All Posts</span>
                    </a>

                    <a href="{{ route('community.index', ['filter' => 'official']) }}"
                       class="px-4 py-2 rounded-full text-xs font-bold whitespace-nowrap shrink-0 transition-all flex items-center gap-1.5 {{ ($filter ?? 'all') === 'official' ? 'bg-comsoc-700 text-white shadow-xs' : 'bg-white border border-gray-200 text-gray-600 hover:border-comsoc-300 hover:text-comsoc-700' }}">
                        <svg class="w-3.5 h-3.5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <span>Official Announcements</span>
                    </a>

                    <a href="{{ route('community.index', ['filter' => 'community']) }}"
                       class="px-4 py-2 rounded-full text-xs font-bold whitespace-nowrap shrink-0 transition-all flex items-center gap-1.5 {{ ($filter ?? 'all') === 'community' ? 'bg-comsoc-700 text-white shadow-xs' : 'bg-white border border-gray-200 text-gray-600 hover:border-comsoc-300 hover:text-comsoc-700' }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
                        </svg>
                        <span>Student Discussions</span>
                    </a>
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
                        <p class="text-gray-700 font-bold text-base">
                            @if(($filter ?? 'all') === 'official')
                                No official announcements found
                            @elseif(($filter ?? 'all') === 'community')
                                No student discussions yet
                            @else
                                Nothing here yet
                            @endif
                        </p>
                        <p class="text-gray-400 text-sm mt-1">
                            @if(($filter ?? 'all') !== 'all')
                                <a href="{{ route('community.index') }}" class="text-comsoc-600 font-semibold hover:underline">Clear filter</a> to view all posts.
                            @else
                                Be the first to share something with COMSOC!
                            @endif
                        </p>
                    </div>
                @endforelse

                {{-- Pagination --}}
                @if($posts->hasPages())
                    <div class="pt-2 pb-4">
                        {{ $posts->links() }}
                    </div>
                @endif
            </div>

            {{-- ===== RIGHT SIDEBAR (lg+ only) ===== --}}
            <div class="hidden lg:block lg:col-span-4 sticky top-4 space-y-4">

                {{-- COMSOC Info Card --}}
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="p-5 bg-gradient-to-br from-comsoc-950 via-comsoc-900 to-comsoc-800 text-white relative">
                        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-gold-600 via-gold-400 to-gold-600"></div>
                        <div class="flex items-center gap-3.5 mb-2.5">
                            <div class="w-12 h-12 rounded-xl bg-white/10 backdrop-blur-sm border border-gold-400/40 p-1 flex items-center justify-center shrink-0 shadow-inner">
                                <img src="{{ asset('images/comsoc_logo.png') }}" alt="COMSOC" class="w-full h-full object-contain"/>
                            </div>
                            <div>
                                <h3 class="text-base font-black text-white leading-tight">COMSOC Hub</h3>
                                <p class="text-[11px] text-gold-400 font-semibold">CCIS · University of Makati</p>
                            </div>
                        </div>
                        <p class="text-xs text-comsoc-100/90 leading-relaxed">
                            The official digital community platform of the Computer Society of CCIS. Connect, discuss, and stay updated.
                        </p>
                    </div>
                    <div class="px-5 py-3.5 bg-gray-50/60 border-t border-gray-100 flex items-center justify-between text-xs">
                        <span class="text-gray-500 font-medium">Academic Year</span>
                        <span class="font-bold text-comsoc-800 bg-comsoc-100/70 px-2.5 py-0.5 rounded-full border border-comsoc-200 text-[11px]">2026 – 2027</span>
                    </div>
                </div>

                {{-- Upcoming Event Spotlight Widget (if active) --}}
                @if(isset($spotlightEvent) && $spotlightEvent)
                    <div class="bg-white rounded-2xl border border-comsoc-200/80 shadow-sm p-4 relative overflow-hidden">
                        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-comsoc-600 to-emerald-500"></div>
                        <div class="flex items-center justify-between mb-2.5">
                            <span class="text-[11px] font-bold text-comsoc-700 uppercase tracking-wider flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                Upcoming Event
                            </span>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-gold-400 text-comsoc-950 uppercase tracking-wide">
                                {{ ucfirst($spotlightEvent->category ?? 'Event') }}
                            </span>
                        </div>
                        <h4 class="text-sm font-bold text-gray-900 line-clamp-1 mb-1">{{ $spotlightEvent->title }}</h4>
                        <p class="text-xs text-gray-500 flex items-center gap-1.5 mb-3">
                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>{{ $spotlightEvent->event_date?->format('M d, Y · g:i A') }}</span>
                        </p>
                        <a href="{{ route('events.show', $spotlightEvent) }}"
                           class="w-full inline-flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl bg-comsoc-50 hover:bg-comsoc-100 text-comsoc-700 text-xs font-bold transition-colors">
                            <span>View Event Details</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                @endif

                {{-- Community Standards Card --}}
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4">
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-comsoc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        Community Guidelines
                    </h3>
                    <ul class="space-y-2.5 text-xs text-gray-600">
                        <li class="flex items-start gap-2">
                            <span class="text-emerald-500 font-bold text-sm leading-none shrink-0">•</span>
                            <span>Be respectful and supportive to all students and faculty.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-emerald-500 font-bold text-sm leading-none shrink-0">•</span>
                            <span>Share relevant CCIS topics, technology insights, and academic tips.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-emerald-500 font-bold text-sm leading-none shrink-0">•</span>
                            <span>No harassment, hate speech, spam, or unauthorized promotions.</span>
                        </li>
                    </ul>
                </div>

            </div>

        </div>
    </div>

</x-dashboard-layout>
