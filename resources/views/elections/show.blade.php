<x-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <a href="{{ route('elections.index') }}" class="text-xs font-bold text-comsoc-600 hover:text-comsoc-800 transition">
                ← Back to Elections Hub
            </a>
            <div class="flex items-center gap-2">
                @if($election->areResultsPublished())
                    <a href="{{ route('elections.results', $election) }}"
                       class="px-4 py-1.5 bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold rounded-xl transition shadow-xs flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Official Results
                    </a>
                @endif
                @can('update', $election)
                    <a href="{{ route('elections.edit', $election) }}"
                       class="px-3.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition">
                        Edit
                    </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="{ applyModalOpen: false }">

        {{-- ===== ELECTION HEADER CARD ===== --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-xs space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                <div class="space-y-2 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $election->getStatusColor() }}">
                            {{ $election->getStatusLabel() }}
                        </span>
                        <span class="text-xs font-semibold text-gray-500">Academic Year {{ $election->academic_year }}</span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-black text-gray-900 leading-tight">{{ $election->title }}</h1>
                    @if($election->description)
                        <p class="text-xs text-gray-600 leading-relaxed max-w-3xl">{{ $election->description }}</p>
                    @endif
                </div>

                {{-- Officer Status Controls --}}
                @can('update', $election)
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 shrink-0 space-y-2">
                        <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-wider">Electoral Status</label>
                        <form method="POST" action="{{ route('elections.status', $election) }}" class="flex items-center gap-2">
                            @csrf
                            <select name="status" class="text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500">
                                <option value="draft" {{ $election->status === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="filing_open" {{ $election->status === 'filing_open' ? 'selected' : '' }}>Filing Open</option>
                                <option value="voting_open" {{ $election->status === 'voting_open' ? 'selected' : '' }}>Voting Open</option>
                                <option value="closed" {{ $election->status === 'closed' ? 'selected' : '' }}>Voting Closed</option>
                                <option value="results_published" {{ $election->status === 'results_published' ? 'selected' : '' }}>Results Published</option>
                            </select>
                            <button type="submit" class="px-3 py-2 bg-comsoc-700 hover:bg-comsoc-800 text-white text-xs font-bold rounded-xl transition shadow-2xs">
                                Update
                            </button>
                        </form>
                    </div>
                @endcan
            </div>

            {{-- Action Banners: Voting Live / Filing Open --}}
            @if($election->isVotingOpen())
                <div class="p-5 bg-gradient-to-r from-emerald-700 to-comsoc-800 text-white rounded-xl shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-300 animate-pulse"></span>
                            <h3 class="font-bold text-sm">Official Voting Booth is Open</h3>
                        </div>
                        <p class="text-xs text-emerald-100 mt-1">Review candidates below and cast your confidential secret ballot.</p>
                    </div>

                    @if($hasVoted)
                        <span class="px-4 py-2 bg-white/20 text-white text-xs font-bold rounded-xl border border-white/20 flex items-center gap-1.5 self-start sm:self-auto">
                            <svg class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Ballot Recorded
                        </span>
                    @else
                        <a href="{{ route('elections.vote', $election) }}"
                           class="px-5 py-2.5 bg-gold-400 hover:bg-gold-500 text-comsoc-950 text-xs font-black rounded-xl transition shadow-sm flex items-center gap-1.5 self-start sm:self-auto">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Enter Voting Booth
                        </a>
                    @endif
                </div>
            @elseif($election->isFilingOpen())
                <div class="p-5 bg-gradient-to-r from-blue-700 to-comsoc-800 text-white rounded-xl shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-300 animate-pulse"></span>
                            <h3 class="font-bold text-sm">Call for Candidacy is Open</h3>
                        </div>
                        <p class="text-xs text-blue-100 mt-1">Active CCIS student members are eligible to file their candidacy for vacant seats.</p>
                    </div>

                    @if($hasApplied)
                        <span class="px-4 py-2 bg-white/20 text-white text-xs font-bold rounded-xl border border-white/20 self-start sm:self-auto">
                            Application Filed
                        </span>
                    @else
                        <button type="button" @click="applyModalOpen = true"
                                class="px-5 py-2.5 bg-gold-400 hover:bg-gold-500 text-comsoc-950 text-xs font-black rounded-xl transition shadow-sm flex items-center gap-1.5 self-start sm:self-auto">
                            File Candidacy Application
                        </button>
                    @endif
                </div>
            @endif
        </div>

        {{-- ===== POSITIONS & CANDIDATES DIRECTORY ===== --}}
        <div class="space-y-6">
            <h2 class="text-base font-bold text-gray-900">Electoral Positions &amp; Candidates</h2>

            <div class="space-y-6">
                @foreach($election->positions as $pos)
                    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-xs space-y-4">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                            <div>
                                <h3 class="text-base font-bold text-gray-900">{{ $pos->title }}</h3>
                                <p class="text-xs text-gray-400 mt-0.5">Position #{{ $pos->order }} · 1 Winner Elected</p>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700">
                                {{ $pos->approvedCandidates->count() }} Candidates
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @forelse($pos->approvedCandidates as $cand)
                                <div class="p-4 rounded-xl border border-gray-100 bg-gray-50/50 space-y-3">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $cand->user->getProfilePictureUrl() }}"
                                             alt="{{ $cand->user->name }}"
                                             class="w-12 h-12 rounded-full object-cover border-2 border-comsoc-200"/>
                                        <div class="min-w-0">
                                            <h4 class="font-bold text-gray-900 text-sm truncate">{{ $cand->user->name }}</h4>
                                            <div class="flex items-center gap-2 text-xs text-gray-500 mt-0.5">
                                                @if($cand->user->program)
                                                    <span>{{ $cand->user->program->code }}</span>
                                                @endif
                                                @if($cand->party_name)
                                                    <span class="px-2 py-0.2 bg-blue-50 text-blue-700 rounded font-semibold text-[10px]">{{ $cand->party_name }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    @if($cand->platform)
                                        <div class="p-3 bg-white rounded-lg border border-gray-200 text-xs text-gray-700 leading-relaxed">
                                            <p class="font-bold text-[11px] text-gray-400 uppercase tracking-wider mb-1">Platform Statement</p>
                                            <p class="whitespace-pre-line">{{ $cand->platform }}</p>
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div class="col-span-2 py-6 text-center text-gray-400 text-xs italic">
                                    No candidates officially approved for this position yet.
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ===== CANDIDACY APPLICATION MODAL ===== --}}
        <div x-show="applyModalOpen" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4">
            <div @click.outside="applyModalOpen = false"
                 class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-gray-900">File Candidacy Application</h3>
                    <button @click="applyModalOpen = false" class="text-gray-400 hover:text-gray-600 rounded-lg p-1 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('elections.apply', $election) }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Select Position *</label>
                        <select name="position_id" required class="w-full text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500">
                            <option value="">Choose a position...</option>
                            @foreach($election->positions as $pos)
                                <option value="{{ $pos->id }}">{{ $pos->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Political Party / Slate (Optional)</label>
                        <input type="text" name="party_name" placeholder="e.g. Innovators Slate, Independent"
                               class="w-full text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500"/>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Platform Statement &amp; Vision *</label>
                        <textarea name="platform" rows="5" required
                                  placeholder="State your campaign platform, goals, proposed projects, and background..."
                                  class="w-full text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" @click="applyModalOpen = false"
                                class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-xl">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-5 py-2 bg-comsoc-700 hover:bg-comsoc-800 text-white text-xs font-bold rounded-xl shadow-xs">
                            Submit Candidacy
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-dashboard-layout>
