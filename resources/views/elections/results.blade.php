<x-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-base font-bold text-gray-900">Official Election Results</h1>
                <p class="text-xs text-gray-500">Certified vote counts and elected officer roster</p>
            </div>
            <a href="{{ route('elections.show', $election) }}" class="text-xs font-bold text-comsoc-600 hover:text-comsoc-800 transition">
                ← Back to Election
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">

        {{-- ===== RESULTS HERO HEADER ===== --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-xs space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-purple-100 text-purple-800 uppercase tracking-wide">
                            Official Certified Tally
                        </span>
                        <span class="text-xs text-gray-400">AY {{ $election->academic_year }}</span>
                    </div>
                    <h2 class="text-xl font-black text-gray-900 mt-1">{{ $election->title }}</h2>
                </div>

                <div class="flex items-center gap-3 bg-gray-50 px-4 py-3 rounded-xl border border-gray-200 shrink-0">
                    <div class="text-right">
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Total Ballots Cast</p>
                        <p class="text-xl font-black text-comsoc-800 leading-none mt-0.5">{{ $totalBallotsCast }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-comsoc-100 text-comsoc-700 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== POSITION RESULTS TALLIES ===== --}}
        <div class="space-y-6">
            @foreach($election->positions as $pos)
                @php
                    $posTotalVotes = $pos->votes()->count();
                    $sortedCandidates = $pos->approvedCandidates->sortByDesc(fn($c) => $c->votes->count());
                    $highestVotes = $sortedCandidates->first()?->votes->count() ?? 0;
                @endphp

                <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-xs space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <div>
                            <h3 class="text-base font-bold text-gray-900">{{ $pos->title }}</h3>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $posTotalVotes }} total votes recorded for this seat</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        @forelse($sortedCandidates as $idx => $cand)
                            @php
                                $votesCount = $cand->votes->count();
                                $percentage = $posTotalVotes > 0 ? round(($votesCount / $posTotalVotes) * 100, 1) : 0;
                                $isWinner = ($highestVotes > 0 && $votesCount === $highestVotes);
                            @endphp

                            <div class="p-4 rounded-xl border {{ $isWinner ? 'border-gold-300 bg-gold-50/20 shadow-2xs' : 'border-gray-100 bg-gray-50/40' }} space-y-2">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <img src="{{ $cand->user->getProfilePictureUrl() }}"
                                             alt="{{ $cand->user->name }}"
                                             class="w-10 h-10 rounded-full object-cover border {{ $isWinner ? 'border-gold-400' : '' }}"/>
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2">
                                                <h4 class="font-bold text-gray-900 text-sm truncate">{{ $cand->user->name }}</h4>
                                                @if($isWinner)
                                                    <span class="px-2 py-0.2 rounded-full text-[10px] font-black bg-gold-400 text-comsoc-950 uppercase tracking-wide">
                                                        Elected
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                                @if($cand->user->program)
                                                    <span>{{ $cand->user->program->code }}</span>
                                                @endif
                                                @if($cand->party_name)
                                                    <span class="text-gray-400">· {{ $cand->party_name }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-right shrink-0">
                                        <p class="text-base font-black text-gray-900">{{ $votesCount }} <span class="text-xs text-gray-400 font-normal">votes</span></p>
                                        <p class="text-xs font-bold text-comsoc-700">{{ $percentage }}%</p>
                                    </div>
                                </div>

                                {{-- Visual Progress Bar --}}
                                <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full {{ $isWinner ? 'bg-gradient-to-r from-gold-500 to-amber-400' : 'bg-comsoc-600' }} rounded-full transition-all"
                                         style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-gray-400 italic py-2">No candidates ran for this seat.</p>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</x-dashboard-layout>
