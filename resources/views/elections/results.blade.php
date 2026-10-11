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
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black {{ $election->status === 'results_published' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }} uppercase tracking-wide flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full {{ $election->status === 'results_published' ? 'bg-purple-600' : 'bg-blue-600 animate-ping' }}"></span>
                            {{ $election->status === 'results_published' ? 'Official Certified Tally' : ($election->status === 'voting_open' ? 'Live Real-Time Tally' : 'Tally Pending Proclamation') }}
                        </span>
                        <span class="text-xs text-gray-400">AY {{ $election->academic_year }}</span>
                    </div>
                    <h2 class="text-xl font-black text-gray-900 mt-1">{{ $election->title }}</h2>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    {{-- Ballots Cast --}}
                    <div class="flex items-center gap-3 bg-gray-50 px-4 py-3 rounded-xl border border-gray-200">
                        <div class="text-right">
                            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Ballots Cast</p>
                            <p class="text-xl font-black text-comsoc-800 leading-none mt-0.5">{{ $totalBallotsCast }}</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-comsoc-100 text-comsoc-700 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>

                    {{-- Voter Turnout --}}
                    @if($eligibleVoterCount > 0)
                        @php
                            $effectiveEligible = max($eligibleVoterCount, $totalBallotsCast);
                            $turnoutPct = round(($totalBallotsCast / $effectiveEligible) * 100, 1);
                        @endphp
                        <div class="flex items-center gap-3 bg-gray-50 px-4 py-3 rounded-xl border border-gray-200">
                            <div class="text-right">
                                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Voter Turnout</p>
                                <p class="text-xl font-black leading-none mt-0.5 {{ $turnoutPct >= 50 ? 'text-emerald-700' : 'text-amber-600' }}">
                                    {{ $turnoutPct }}%
                                </p>
                                <p class="text-[10px] text-gray-400 mt-0.5">{{ $totalBallotsCast }} / {{ $effectiveEligible }}</p>
                            </div>
                            <div class="w-10 h-10 rounded-xl {{ $turnoutPct >= 50 ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }} flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Turnout Progress Bar --}}
            @if(isset($turnoutPct))
                <div class="space-y-1 pt-2 border-t border-gray-100">
                    <div class="flex items-center justify-between text-[11px] font-bold">
                        <span class="text-gray-500">Official Electorate Participation</span>
                        <span class="{{ $turnoutPct >= 50 ? 'text-emerald-700' : 'text-amber-600' }}">{{ $turnoutPct }}% Quorum Reached</span>
                    </div>
                    <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full {{ $turnoutPct >= 50 ? 'bg-gradient-to-r from-emerald-500 to-teal-400' : 'bg-gradient-to-r from-amber-500 to-yellow-400' }} rounded-full transition-all duration-1000"
                             style="width: {{ $turnoutPct }}%"></div>
                    </div>
                </div>
            @endif
        </div>

        {{-- ===== OFFICIAL PROCLAMATION & OFFICER HANDOVER CONTROL ===== --}}
        @can('update', $election)
            @if($election->status === 'closed')
                <div class="p-6 bg-gradient-to-r from-comsoc-900 via-comsoc-800 to-comsoc-950 text-white rounded-2xl shadow-md border border-gold-400/30 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-gold-400 animate-pulse"></span>
                                <h3 class="font-black text-sm tracking-wide uppercase text-gold-300">Executive Proclamation &amp; Officer Handover</h3>
                            </div>
                            <p class="text-xs text-gray-200 max-w-2xl leading-relaxed">
                                Certify the official tally and automatically upgrade all winning student user accounts to their respective Executive Officer roles with full Officer Desk portal permissions.
                            </p>
                        </div>

                        <form method="POST" action="{{ route('elections.proclaim', $election) }}"
                              onsubmit="return confirm('Are you sure you want to officially proclaim the winners? This will immediately elevate their user accounts to executive officer roles.');"
                              class="shrink-0">
                            @csrf
                            <button type="submit"
                                    class="px-6 py-3 bg-gradient-to-r from-gold-400 to-amber-500 hover:from-gold-500 hover:to-amber-600 text-comsoc-950 text-xs font-black rounded-xl transition shadow-md flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                                </svg>
                                Proclaim Winners &amp; Elevate Roles
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        @endcan

        {{-- ===== POSITION RESULTS TALLIES ===== --}}
        <div class="space-y-6">
            @foreach($election->positions as $pos)
                @php
                    $posTotalVotes    = $pos->approvedCandidates->sum(fn($c) => $c->votes->count());
                    $sortedCandidates = $pos->approvedCandidates->sortByDesc(fn($c) => $c->votes->count())->values();

                    $cutoffCandidate = $sortedCandidates->get($pos->max_winners - 1);
                    $cutoffVotes     = $cutoffCandidate ? $cutoffCandidate->votes->count() : 0;
                    
                    // Check for exact tie at the cutoff boundary
                    $isTiedAtCutoff = false;
                    $tiedCandidateCount = 0;
                    if ($cutoffVotes > 0) {
                        $tiedCandidatesAtCutoff = $sortedCandidates->filter(fn($c) => $c->votes->count() === $cutoffVotes);
                        if ($tiedCandidatesAtCutoff->count() > 1 && $sortedCandidates->search(fn($s) => $s->votes->count() === $cutoffVotes) < $pos->max_winners) {
                            $isTiedAtCutoff = true;
                            $tiedCandidateCount = $tiedCandidatesAtCutoff->count();
                        }
                    }

                    $winnerIds = $cutoffVotes > 0
                        ? $sortedCandidates
                            ->filter(fn($c) => $c->votes->count() >= $cutoffVotes
                                && $sortedCandidates->search(fn($s) => $s->id === $c->id) < $pos->max_winners
                                || $c->votes->count() > $cutoffVotes
                            )
                            ->pluck('id')->all()
                        : [];
                @endphp

                <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-xs space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-gray-100 pb-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-base font-bold text-gray-900">{{ $pos->title }}</h3>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-600">
                                    {{ $pos->max_winners === 1 ? 'Single-Seat' : $pos->max_winners . ' Available Seats' }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $posTotalVotes }} total votes cast for this seat</p>
                        </div>

                        @if($isTiedAtCutoff)
                            <div class="flex items-center gap-1.5 px-3 py-1 bg-amber-50 border border-amber-200 text-amber-800 rounded-xl text-xs font-bold shrink-0">
                                <svg class="w-4 h-4 text-amber-600 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                Exact Tie Scenario Detected
                            </div>
                        @endif
                    </div>

                    {{-- Tie Resolution Banner --}}
                    @if($isTiedAtCutoff)
                        <div class="p-4 bg-amber-50/90 border border-amber-200 rounded-xl flex items-start gap-3 text-xs text-amber-950">
                            <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                            <div class="space-y-1">
                                <p class="font-black text-amber-900">
                                    ⚡ Electoral Deadlock: Exact Tie at Cutoff Boundary ({{ $cutoffVotes }} votes each · {{ $posTotalVotes > 0 ? round(($cutoffVotes / $posTotalVotes) * 100, 1) : 0 }}%)
                                </p>
                                <p class="text-amber-800 text-[11px] leading-relaxed">
                                    {{ $tiedCandidateCount }} candidates garnered identical vote counts for {{ $pos->title }}. 
                                    <strong>Tie Resolution Mechanism:</strong> Under COMSOC Constitution &amp; Electoral Code Art. IV, the Election Committee applies deterministic secondary ordering (Seniority / Candidate ID tie-breaker) or conducts formal committee deliberation prior to executive proclamation.
                                </p>
                            </div>
                        </div>
                    @endif

                    <div class="space-y-4">
                        @forelse($sortedCandidates as $idx => $cand)
                            @php
                                $votesCount = $cand->votes->count();
                                $percentage = $posTotalVotes > 0 ? round(($votesCount / $posTotalVotes) * 100, 1) : 0;
                                $isWinner   = in_array($cand->id, $winnerIds);
                                $isTiedCand = $isTiedAtCutoff && $votesCount === $cutoffVotes;
                            @endphp

                            <div class="p-4 rounded-xl border {{ $isTiedCand ? 'border-amber-300 bg-amber-50/30' : ($isWinner ? 'border-gold-300 bg-gold-50/20 shadow-2xs' : 'border-gray-100 bg-gray-50/40') }} space-y-2">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <img src="{{ $cand->user->getProfilePictureUrl() }}"
                                             alt="{{ $cand->user->name }}"
                                             class="w-10 h-10 rounded-full object-cover border {{ $isTiedCand ? 'border-amber-400' : ($isWinner ? 'border-gold-400' : '') }}"/>
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2">
                                                <h4 class="font-bold text-gray-900 text-sm truncate">{{ $cand->user->name }}</h4>
                                                @if($isTiedCand)
                                                    @if($election->status === 'results_published')
                                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-gold-400 text-comsoc-950 uppercase tracking-wide">
                                                            ⚡ Tie Resolved · Proclaimed
                                                        </span>
                                                    @else
                                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-200 text-amber-900 border border-amber-300 uppercase tracking-wide">
                                                            ⚠️ Exact Tie · Deliberation
                                                        </span>
                                                    @endif
                                                @elseif($isWinner)
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-gold-400 text-comsoc-950 uppercase tracking-wide">
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
                                        <p class="text-xs font-black {{ $isTiedCand ? 'text-amber-700' : 'text-comsoc-700' }}">{{ $percentage }}%</p>
                                    </div>
                                </div>

                                {{-- Animated Progress Bar --}}
                                <div class="w-full h-2.5 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="result-bar h-full {{ $isTiedCand ? 'bg-gradient-to-r from-amber-500 to-yellow-400' : ($isWinner ? 'bg-gradient-to-r from-gold-500 to-amber-400' : 'bg-comsoc-600') }} rounded-full"
                                         style="--target-width: {{ $percentage }}%"></div>
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

{{-- ===== #10 ANIMATED RESULTS — CSS + JS ===== --}}
<style>
    .result-bar {
        width: 0%;
        transition: none;
    }
    .result-bar.animate {
        animation: growBar 1.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    @keyframes growBar {
        from { width: 0%; }
        to   { width: var(--target-width); }
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const bars = document.querySelectorAll('.result-bar');

        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        // Stagger each bar slightly for a reveal effect
                        const idx = Array.from(bars).indexOf(entry.target);
                        setTimeout(function () {
                            entry.target.classList.add('animate');
                        }, idx * 120);
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.3 });

            bars.forEach(function (bar) { observer.observe(bar); });
        } else {
            // Fallback: just set widths directly
            bars.forEach(function (bar) {
                bar.style.width = bar.style.getPropertyValue('--target-width') || '0%';
            });
        }
    });
</script>
