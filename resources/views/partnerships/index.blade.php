<x-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-base font-bold text-gray-900">Partnerships &amp; External Collaborations</h1>
                <p class="text-xs text-gray-500">Official academic, industry, and student organization partnerships</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('partner-organizations.index') }}"
                   class="px-3.5 py-1.5 bg-white hover:bg-gray-50 text-gray-700 text-xs font-bold rounded-xl border border-gray-300 transition shadow-2xs flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Partner Directory
                </a>
                @can('create', App\Models\Partnership::class)
                    <a href="{{ route('partnerships.create') }}"
                       class="px-4 py-1.5 bg-comsoc-700 hover:bg-comsoc-800 text-white text-xs font-bold rounded-xl transition shadow-xs flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Initiate Partnership
                    </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">

        {{-- ===== KPI STATS CARDS ===== --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-2xs flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Active Partnerships</p>
                    <p class="text-2xl font-black text-emerald-700 mt-1">{{ $activeCount }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-2xs flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Under Review / Proposed</p>
                    <p class="text-2xl font-black text-amber-700 mt-1">{{ $underReviewCount }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-2xs flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Partner Organizations</p>
                    <p class="text-2xl font-black text-blue-700 mt-1">{{ $partnerOrgsCount }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- ===== FILTER BAR ===== --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-xs">
            <form method="GET" action="{{ route('partnerships.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Status</label>
                    <select name="status" class="w-full text-xs border border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500">
                        <option value="">All Statuses</option>
                        <option value="proposed" {{ request('status') === 'proposed' ? 'selected' : '' }}>Proposed</option>
                        <option value="under_review" {{ request('status') === 'under_review' ? 'selected' : '' }}>Under Review</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Partnership</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                        <option value="declined" {{ request('status') === 'declined' ? 'selected' : '' }}>Declined</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Partner Organization</label>
                    <select name="partner_id" class="w-full text-xs border border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500">
                        <option value="">All Partners</option>
                        @foreach($partners as $partner)
                            <option value="{{ $partner->id }}" {{ request('partner_id') == $partner->id ? 'selected' : '' }}>
                                {{ $partner->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit"
                            class="px-4 py-2 bg-comsoc-700 hover:bg-comsoc-800 text-white text-xs font-bold rounded-xl transition shadow-xs">
                        Filter Partnerships
                    </button>
                    @if(request()->hasAny(['status', 'partner_id']))
                        <a href="{{ route('partnerships.index') }}"
                           class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-bold rounded-xl transition">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- ===== PARTNERSHIPS LIST ===== --}}
        <div class="space-y-4">
            @forelse($partnerships as $partnership)
                <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-xs hover:border-comsoc-300 transition">
                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                        <div class="space-y-2 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $partnership->getStatusColor() }}">
                                    {{ $partnership->getStatusLabel() }}
                                </span>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $partnership->partner->getTypeBadgeColor() }}">
                                    {{ $partnership->partner->getTypeLabel() }}
                                </span>
                                <span class="text-xs text-gray-400">AY {{ $partnership->academic_year }}</span>
                            </div>

                            <div>
                                <h3 class="text-base font-bold text-gray-900 leading-snug">{{ $partnership->title }}</h3>
                                <p class="text-xs text-comsoc-700 font-semibold mt-0.5">Partner: {{ $partnership->partner->name }}</p>
                            </div>

                            @if($partnership->description)
                                <p class="text-xs text-gray-600 line-clamp-2 max-w-3xl leading-relaxed">{{ $partnership->description }}</p>
                            @endif

                            <div class="flex items-center gap-4 text-xs text-gray-500 pt-1 flex-wrap">
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $partnership->start_date ? $partnership->start_date->format('M d, Y') : 'TBD' }}
                                    @if($partnership->end_date)
                                        – {{ $partnership->end_date->format('M d, Y') }}
                                    @endif
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                    </svg>
                                    {{ $partnership->activities_count }} Technical Activities
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    {{ $partnership->documents_count }} MoA / Documents
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            <a href="{{ route('partnerships.show', $partnership) }}"
                               class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition">
                                View Details →
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-gray-200 p-12 text-center shadow-xs">
                    <p class="text-gray-800 font-bold text-base">No partnerships found.</p>
                    <p class="text-xs text-gray-500 mt-1">Initiate a partnership proposal or explore partner organizations.</p>
                </div>
            @endforelse
        </div>

        @if($partnerships->hasPages())
            <div class="pt-2">
                {{ $partnerships->links() }}
            </div>
        @endif

    </div>
</x-dashboard-layout>
