<x-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('events.index') }}" class="hover:text-gray-900 transition-colors">Events</a>
            <span>/</span>
            <a href="{{ route('events.show', $event) }}" class="hover:text-gray-900 transition-colors truncate max-w-[200px]">{{ $event->title }}</a>
            <span>/</span>
            <span class="text-gray-900 font-bold">Committees & Tasks</span>
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto space-y-6" x-data="{ createModalOpen: false }">

        {{-- Flash messages --}}
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                 x-transition
                 class="flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-700 text-sm shadow-sm">
                <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        {{-- EVENT HEADER & NAVIGATION TABS --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-5 sm:p-6 bg-gradient-to-r from-comsoc-950 via-comsoc-900 to-comsoc-800 text-white">
                <div class="flex items-start justify-between gap-4 flex-wrap">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider bg-gold-400/20 text-gold-400 border border-gold-400/30">
                                Event Organization Hub
                            </span>
                        </div>
                        <h1 class="text-xl sm:text-2xl font-black">{{ $event->title }}</h1>
                        <p class="text-xs text-comsoc-200 mt-1 flex items-center gap-3 flex-wrap">
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-gold-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ $event->event_date->format('M d, Y') }}
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-gold-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                {{ $event->venue }}
                            </span>
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('events.tasks.index', $event) }}"
                           class="inline-flex items-center gap-1.5 px-4 py-2 bg-white/10 hover:bg-white/20 text-white text-xs font-bold rounded-xl border border-white/20 transition-all backdrop-blur-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                            View All Tasks Board
                        </a>

                        @can('create', App\Models\Committee::class)
                            <button @click="createModalOpen = true"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-gold-400 hover:bg-gold-500 text-comsoc-950 text-xs font-black rounded-xl transition-all shadow-md">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                </svg>
                                New Committee
                            </button>
                        @endcan
                    </div>
                </div>
            </div>

            {{-- SUB-NAV TABS --}}
            <div class="flex items-center gap-1 px-4 border-b border-gray-100 bg-gray-50/50 overflow-x-auto">
                <a href="{{ route('events.show', $event) }}"
                   class="px-4 py-3 text-xs font-semibold text-gray-500 hover:text-gray-800 transition-colors whitespace-nowrap">
                    Event Overview
                </a>
                <a href="{{ route('events.committees.index', $event) }}"
                   class="px-4 py-3 text-xs font-bold text-comsoc-700 border-b-2 border-comsoc-600 transition-colors whitespace-nowrap">
                    Committees ({{ $event->committees->count() }})
                </a>
                <a href="{{ route('events.tasks.index', $event) }}"
                   class="px-4 py-3 text-xs font-semibold text-gray-500 hover:text-gray-800 transition-colors whitespace-nowrap">
                    Task Board ({{ $totalTasks }})
                </a>
                @can('viewParticipants', $event)
                    <a href="{{ route('events.participants', $event) }}"
                       class="px-4 py-3 text-xs font-semibold text-gray-500 hover:text-gray-800 transition-colors whitespace-nowrap">
                        Participants
                    </a>
                @endcan
            </div>
        </div>

        {{-- PROGRESS & STATS STRIP --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
            <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Committees</p>
                <p class="text-2xl font-black text-gray-900 mt-1">{{ $event->committees->count() }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Total Tasks</p>
                <p class="text-2xl font-black text-gray-900 mt-1">{{ $totalTasks }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
                <p class="text-xs font-bold text-emerald-600 uppercase tracking-wide">Tasks Done</p>
                <p class="text-2xl font-black text-emerald-600 mt-1">{{ $completedTasks }} <span class="text-xs text-gray-400 font-medium">/ {{ $totalTasks }}</span></p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
                <p class="text-xs font-bold text-comsoc-600 uppercase tracking-wide">Progress</p>
                <div class="flex items-center gap-2 mt-1">
                    <p class="text-2xl font-black text-comsoc-700">{{ $overallProgress }}%</p>
                    <div class="flex-1 bg-gray-100 rounded-full h-2 overflow-hidden">
                        <div class="bg-gradient-to-r from-comsoc-500 to-emerald-500 h-2 rounded-full transition-all duration-500"
                             style="width: {{ $overallProgress }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- COMMITTEES GRID --}}
        <div>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-bold text-gray-900">Event Committees</h2>
                <span class="text-xs text-gray-500">{{ $event->committees->count() }} {{ Str::plural('Committee', $event->committees->count()) }}</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse ($event->committees as $committee)
                    @php
                        $tasksCount = $committee->tasks->count();
                        $compCount = $committee->tasks->where('status', \App\Models\EventTask::STATUS_COMPLETED)->count();
                        $pct = $tasksCount > 0 ? (int) round(($compCount / $tasksCount) * 100) : 0;
                    @endphp

                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden flex flex-col justify-between">
                        <div class="p-5">
                            {{-- Header --}}
                            <div class="flex items-start justify-between gap-3 mb-2">
                                <h3 class="text-base font-bold text-gray-900 leading-tight">
                                    {{ $committee->name }}
                                </h3>
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-comsoc-50 text-comsoc-700 border border-comsoc-100 shrink-0">
                                    {{ $committee->members->count() }} {{ Str::plural('Member', $committee->members->count()) }}
                                </span>
                            </div>

                            @if($committee->description)
                                <p class="text-xs text-gray-500 line-clamp-2 leading-relaxed mb-4">
                                    {{ $committee->description }}
                                </p>
                            @else
                                <p class="text-xs text-gray-400 italic mb-4">No description provided.</p>
                            @endif

                            {{-- Appointed Head --}}
                            <div class="p-3 bg-gray-50 rounded-xl mb-4">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide mb-1.5">Committee Head</p>
                                @if($committee->head)
                                    <div class="flex items-center gap-2.5">
                                        <img src="{{ $committee->head->getProfilePictureUrl() }}"
                                             class="w-7 h-7 rounded-full object-cover border border-gray-200 shrink-0" alt=""/>
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-gray-900 truncate">{{ $committee->head->name }}</p>
                                            <p class="text-[10px] text-gray-400 truncate">{{ $committee->head->email }}</p>
                                        </div>
                                    </div>
                                @else
                                    <p class="text-xs text-gray-400 italic">No head appointed yet</p>
                                @endif
                            </div>

                            {{-- Task Progress Bar --}}
                            <div class="space-y-1">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-medium text-gray-500">Tasks Completed</span>
                                    <span class="font-bold text-gray-800">{{ $compCount }} / {{ $tasksCount }} ({{ $pct }}%)</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-comsoc-600 h-1.5 rounded-full transition-all duration-300" style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
                        </div>

                        {{-- Footer CTA --}}
                        <div class="px-5 py-3.5 bg-gray-50/70 border-t border-gray-100 flex items-center justify-between">
                            <a href="{{ route('committees.show', $committee) }}"
                               class="inline-flex items-center gap-1.5 text-xs font-bold text-comsoc-700 hover:text-comsoc-900 transition-colors">
                                Open Workspace →
                            </a>

                            @can('delete', $committee)
                                <form method="POST" action="{{ route('committees.destroy', $committee) }}"
                                      onsubmit="return confirm('Are you sure you want to delete this committee? All associated tasks will be removed.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-red-400 hover:text-red-600 font-semibold transition-colors">
                                        Delete
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white rounded-2xl border border-gray-200 p-12 text-center shadow-sm">
                        <div class="w-16 h-16 rounded-2xl bg-comsoc-50 text-comsoc-400 flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <p class="text-gray-800 font-bold text-base">No committees created yet</p>
                        <p class="text-gray-400 text-sm mt-1 max-w-sm mx-auto">Create specialized committees (Logistics, Creatives, Technical, etc.) to delegate and track event tasks.</p>

                        @can('create', App\Models\Committee::class)
                            <button @click="createModalOpen = true"
                                    class="mt-4 inline-flex items-center gap-1.5 px-4 py-2 bg-comsoc-600 hover:bg-comsoc-700 text-white text-xs font-bold rounded-xl transition-all shadow-sm">
                                Create First Committee
                            </button>
                        @endcan
                    </div>
                @endforelse
            </div>
        </div>

        {{-- CREATE COMMITTEE MODAL --}}
        <div x-show="createModalOpen" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4">
            <div @click.outside="createModalOpen = false"
                 class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-gray-900">Create New Committee</h3>
                    <button @click="createModalOpen = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold">✕</button>
                </div>

                <form method="POST" action="{{ route('events.committees.store', $event) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Committee Name *</label>
                        <input type="text" name="name" required placeholder="e.g. Program & Technical, Logistics & Venue"
                               class="w-full text-sm border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500 focus:border-comsoc-500"/>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="3" placeholder="Define the responsibilities and scope of this committee..."
                                  class="w-full text-sm border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500 focus:border-comsoc-500 resize-none"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Appoint Committee Head (Optional)</label>
                        <select name="head_user_id" class="w-full text-sm border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500 focus:border-comsoc-500">
                            <option value="">Select an active member / officer...</option>
                            @foreach ($activeUsers as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->getRoleLabel() }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" @click="createModalOpen = false"
                                class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-xl transition">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-5 py-2 bg-comsoc-600 hover:bg-comsoc-700 text-white text-xs font-bold rounded-xl transition shadow-sm">
                            Create Committee
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-dashboard-layout>
