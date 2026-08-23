<x-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-base font-bold text-gray-900">Elections &amp; Governance Hub</h1>
                <p class="text-xs text-gray-500">Official Computer Society executive and batch elections</p>
            </div>
            <div class="flex items-center gap-2">
                @can('review', App\Models\Candidate::class)
                    <a href="{{ route('candidates.index') }}"
                       class="px-3.5 py-1.5 bg-white hover:bg-gray-50 text-gray-700 text-xs font-bold rounded-xl border border-gray-300 transition shadow-2xs flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                        Candidate Queue
                    </a>
                @endcan
                @can('create', App\Models\Election::class)
                    <a href="{{ route('elections.create') }}"
                       class="px-4 py-1.5 bg-comsoc-700 hover:bg-comsoc-800 text-white text-xs font-bold rounded-xl transition shadow-xs flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Create Election
                    </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">

        {{-- ===== HERO BANNER ===== --}}
        <div class="relative rounded-2xl overflow-hidden shadow-md" style="min-height:130px;">
            <div class="absolute inset-0">
                <img src="{{ asset('images/hero_logo.jpg') }}" alt="" class="w-full h-full object-cover"/>
            </div>
            <div class="absolute inset-0" style="background:linear-gradient(135deg,rgba(5,46,22,0.95) 0%,rgba(15,23,42,0.85) 100%)"></div>
            <div class="absolute top-0 left-0 right-0 h-0.5 bg-gradient-to-r from-gold-600 via-gold-400 to-gold-600"></div>

            <div class="relative z-10 flex items-center justify-between p-6 text-white">
                <div>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-gold-400 text-comsoc-950 uppercase tracking-wide">
                        COMSOC Electoral Commission
                    </span>
                    <h2 class="text-xl sm:text-2xl font-black mt-1">Student Leadership Elections</h2>
                    <p class="text-xs text-comsoc-200 mt-1 max-w-xl">
                        Exercise your democratic student right to vote and choose the next executive leaders of the Computer Society.
                    </p>
                </div>
            </div>
        </div>

        {{-- ===== ELECTIONS LIST ===== --}}
        <div class="space-y-4">
            @forelse($elections as $election)
                <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-xs hover:border-comsoc-300 transition">
                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                        <div class="space-y-1.5 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $election->getStatusColor() }}">
                                    {{ $election->getStatusLabel() }}
                                </span>
                                <span class="text-xs font-semibold text-gray-500">AY {{ $election->academic_year }}</span>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 leading-snug">{{ $election->title }}</h3>
                            @if($election->description)
                                <p class="text-xs text-gray-600 line-clamp-2 max-w-2xl">{{ $election->description }}</p>
                            @endif

                            <div class="flex items-center gap-4 text-xs text-gray-500 pt-2 flex-wrap">
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                    </svg>
                                    {{ $election->positions->count() }} Positions
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    {{ $election->approved_candidates_count }} Approved Candidates
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ $election->voter_participations_count }} Ballots Cast
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            @if($election->isVotingOpen())
                                @if(Auth::check() && $election->hasUserVoted(Auth::user()))
                                    <span class="px-3.5 py-2 bg-gray-100 text-gray-600 text-xs font-bold rounded-xl flex items-center gap-1">
                                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Ballot Submitted
                                    </span>
                                @else
                                    <a href="{{ route('elections.vote', $election) }}"
                                       class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition shadow-xs flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Cast Your Vote
                                    </a>
                                @endif
                            @endif

                            @if($election->areResultsPublished())
                                <a href="{{ route('elections.results', $election) }}"
                                   class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold rounded-xl transition shadow-xs flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                    View Official Results
                                </a>
                            @endif

                            <a href="{{ route('elections.show', $election) }}"
                               class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition">
                                View Details →
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-gray-200 p-12 text-center shadow-xs">
                    <p class="text-gray-800 font-bold text-base">No elections scheduled at the moment.</p>
                    <p class="text-xs text-gray-500 mt-1">Electoral periods and candidacy filings will be announced here.</p>
                </div>
            @endforelse
        </div>

        @if($elections->hasPages())
            <div class="pt-2">
                {{ $elections->links() }}
            </div>
        @endif

    </div>
</x-dashboard-layout>
