<x-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-base font-bold text-gray-900">Official Secret Ballot</h1>
            <a href="{{ route('elections.show', $election) }}" class="text-xs font-bold text-gray-500 hover:text-gray-700 transition">
                Exit Voting Booth
            </a>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-6" x-data="{
        reviewModalOpen: false,
        rulesOpen: false,
        verified: false,
        summary: [],
        prepareSummary() {
            this.summary = [];
            const form = document.getElementById('ballot-form');
            const positionBlocks = form.querySelectorAll('[data-position-title]');

            positionBlocks.forEach(block => {
                const title = block.getAttribute('data-position-title');
                const checked = block.querySelectorAll('input:checked');
                const selectedNames = [];

                checked.forEach(inp => {
                    const label = inp.getAttribute('data-candidate-name');
                    if (label && label !== '') {
                        selectedNames.push(label);
                    }
                });

                this.summary.push({
                    position: title,
                    candidates: selectedNames.length > 0 ? selectedNames : ['Abstained (No candidate selected)']
                });
            });

            this.reviewModalOpen = true;
        }
    }">

        {{-- ===== #12 VOTER ELIGIBILITY VERIFICATION BADGE ===== --}}
        <div class="bg-white border border-emerald-200 rounded-2xl p-5 shadow-xs">
            <div class="flex items-start gap-4">
                <div class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 uppercase tracking-wide">
                            ✅ Voter Eligibility Verified
                        </span>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">Voter Name</p>
                            <p class="font-bold text-gray-900 truncate">{{ Auth::user()->name }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">Student ID</p>
                            <p class="font-semibold text-gray-700">{{ Auth::user()->student_id ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">Status</p>
                            @php
                                $voterStatus = (Auth::user()->isOfficerOrAbove() || Auth::user()->isAdviser() || Auth::user()->isAdmin())
                                    ? ucwords(str_replace('_', ' ', Auth::user()->role))
                                    : 'Active Member';
                            @endphp
                            <p class="font-semibold text-emerald-700">{{ $voterStatus }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">Program</p>
                            <p class="font-semibold text-gray-700">{{ Auth::user()->program?->code ?? '—' }}</p>
                        </div>
                    </div>
                    <p class="text-[11px] text-emerald-700 mt-2 font-medium">
                        You are officially eligible to cast your secret ballot in this election.
                    </p>
                </div>
            </div>
        </div>

        {{-- ===== BALLOT HEADER & CONFIDENTIALITY NOTICE ===== --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-xs space-y-3">
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 uppercase tracking-wide">
                            Live Ballot
                        </span>
                        <span class="text-xs text-gray-400">AY {{ $election->academic_year }}</span>
                    </div>
                    <h2 class="text-xl font-black text-gray-900">{{ $election->title }}</h2>
                </div>

                {{-- ===== #2 VOTING WINDOW COUNTDOWN TIMER ===== --}}
                @if($election->voting_end_at)
                    <div x-data="{
                            deadline: new Date('{{ $election->voting_end_at->toIso8601String() }}').getTime(),
                            timeLeft: '',
                            urgent: false,
                            start() {
                                const tick = () => {
                                    const now = Date.now();
                                    const diff = this.deadline - now;
                                    if (diff <= 0) {
                                        this.timeLeft = 'Voting Closed';
                                        this.urgent = true;
                                        return;
                                    }
                                    const h = Math.floor(diff / 3600000);
                                    const m = Math.floor((diff % 3600000) / 60000);
                                    const s = Math.floor((diff % 60000) / 1000);
                                    this.urgent = diff < 600000; // under 10 min = urgent
                                    this.timeLeft = (h > 0 ? h + 'h ' : '') + m + 'm ' + String(s).padStart(2,'0') + 's';
                                };
                                tick();
                                setInterval(tick, 1000);
                            }
                        }" x-init="start()"
                         class="shrink-0 text-center bg-gray-50 border border-gray-200 rounded-xl px-4 py-3">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Voting closes in</p>
                        <p class="text-lg font-black mt-0.5 tabular-nums"
                           :class="urgent ? 'text-red-600 animate-pulse' : 'text-comsoc-800'"
                           x-text="timeLeft">—</p>
                        <p class="text-[10px] text-gray-400 mt-0.5">{{ $election->voting_end_at->format('M d, Y · h:i A') }}</p>
                    </div>
                @endif
            </div>

            <div class="p-4 bg-emerald-50/50 border border-emerald-200 rounded-xl text-xs text-emerald-900 leading-relaxed flex items-start gap-3">
                <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <div>
                    <p class="font-bold">Secret Ballot &amp; Voter Privacy Protected</p>
                    <p class="text-emerald-800 mt-0.5">Your votes are cryptographically anonymized. Once submitted, your ballot cannot be altered or traced back to your individual student identity. A digital receipt will be generated upon submission.</p>
                </div>
            </div>
        </div>

        {{-- ===== #15 ELECTION RULES & GUIDELINES (Collapsible) ===== --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-xs overflow-hidden">
            <button type="button" @click="rulesOpen = !rulesOpen"
                    class="w-full flex items-center justify-between px-6 py-4 text-left hover:bg-gray-50 transition">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <span class="text-sm font-bold text-gray-800">Election Rules &amp; Guidelines</span>
                    <span class="text-[10px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">Read Before Voting</span>
                </div>
                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="rulesOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div x-show="rulesOpen" x-collapse class="px-6 pb-6 border-t border-gray-100">
                <div class="pt-4 grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs text-gray-700">
                    <div class="space-y-2.5">
                        <div class="flex items-start gap-2">
                            <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-[10px] font-black shrink-0 mt-0.5">1</span>
                            <p><span class="font-bold text-gray-900">Eligibility:</span> Only active COMSOC members and officers with verified accounts may vote.</p>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-[10px] font-black shrink-0 mt-0.5">2</span>
                            <p><span class="font-bold text-gray-900">One Vote Per Person:</span> Each eligible voter may cast exactly one ballot per election.</p>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-[10px] font-black shrink-0 mt-0.5">3</span>
                            <p><span class="font-bold text-gray-900">Selections Per Position:</span> For single-winner seats, choose one candidate or abstain. For multi-winner seats, choose up to the stated maximum.</p>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-[10px] font-black shrink-0 mt-0.5">4</span>
                            <p><span class="font-bold text-gray-900">Abstention:</span> You may abstain on any position. Abstentions are not counted as votes for any candidate.</p>
                        </div>
                    </div>
                    <div class="space-y-2.5">
                        <div class="flex items-start gap-2">
                            <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-[10px] font-black shrink-0 mt-0.5">5</span>
                            <p><span class="font-bold text-gray-900">Secret Ballot:</span> Your individual choices are cryptographically anonymized. No one — including system administrators — can link your identity to specific candidate selections.</p>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-[10px] font-black shrink-0 mt-0.5">6</span>
                            <p><span class="font-bold text-gray-900">Irrevocable Ballot:</span> Once submitted, your ballot is permanently sealed. You cannot change or retract your votes.</p>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-[10px] font-black shrink-0 mt-0.5">7</span>
                            <p><span class="font-bold text-gray-900">Digital Receipt:</span> Upon submission, a cryptographic proof-of-vote receipt is issued. Save your receipt code as evidence of participation.</p>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-[10px] font-black shrink-0 mt-0.5">8</span>
                            <p><span class="font-bold text-gray-900">Results:</span> Official results will be published by the Election Committee after the voting period closes.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== BALLOT FORM ===== --}}
        <form id="ballot-form" method="POST" action="{{ route('elections.vote.cast', $election) }}" class="space-y-6">
            @csrf

            @foreach($election->positions as $pos)
                <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-xs space-y-4" data-position-title="{{ $pos->title }}">
                    <div class="border-b border-gray-100 pb-3">
                        <h3 class="text-base font-bold text-gray-900">{{ $pos->title }}</h3>
                        <p class="text-xs text-gray-400 mt-0.5">
                            @if($pos->max_winners === 1)
                                Select one candidate or abstain
                            @else
                                Select up to {{ $pos->max_winners }} {{ Str::plural('candidate', $pos->max_winners) }} or abstain
                            @endif
                        </p>
                    </div>

                    <div class="space-y-3" @if($pos->max_winners > 1) data-max="{{ $pos->max_winners }}" data-pos="{{ $pos->id }}" @endif>
                        {{-- Candidate Options --}}
                        @forelse($pos->approvedCandidates as $cand)
                            <label class="p-4 rounded-xl border border-gray-200 bg-gray-50/40 hover:bg-white hover:border-comsoc-400 transition cursor-pointer flex items-start gap-4">
                                @if($pos->max_winners === 1)
                                    <input type="radio" name="votes[{{ $pos->id }}]" value="{{ $cand->id }}"
                                           data-candidate-name="{{ $cand->user->name }}"
                                           class="mt-1 text-comsoc-600 focus:ring-comsoc-500"/>
                                @else
                                    <input type="checkbox" name="votes[{{ $pos->id }}][]" value="{{ $cand->id }}"
                                           data-candidate-name="{{ $cand->user->name }}"
                                           class="mt-1 text-comsoc-600 focus:ring-comsoc-500 pos-checkbox-{{ $pos->id }}"/>
                                @endif
                                <div class="flex items-center gap-3 min-w-0 flex-1">
                                    <img src="{{ $cand->user->getProfilePictureUrl() }}"
                                         alt="{{ $cand->user->name }}"
                                         class="w-10 h-10 rounded-full object-cover border"/>
                                    <div class="min-w-0">
                                        <h4 class="font-bold text-gray-900 text-sm truncate">{{ $cand->user->name }}</h4>
                                        <div class="flex items-center gap-2 text-xs text-gray-500">
                                            @if($cand->user->program)
                                                <span>{{ $cand->user->program->code }}</span>
                                            @endif
                                            @if($cand->party_name)
                                                <span class="px-2 py-0.2 bg-blue-50 text-blue-700 rounded font-semibold text-[10px]">{{ $cand->party_name }}</span>
                                            @endif
                                        </div>
                                        @if($cand->platform)
                                            <p class="text-xs text-gray-600 mt-1 line-clamp-2">{{ $cand->platform }}</p>
                                        @endif
                                    </div>
                                </div>
                            </label>
                        @empty
                            <p class="text-xs text-gray-400 italic py-2">No approved candidates for this position.</p>
                        @endforelse

                        {{-- Abstain Option (radio only) --}}
                        @if($pos->max_winners === 1)
                            <label class="p-3.5 rounded-xl border border-dashed border-gray-200 bg-white hover:bg-gray-50 transition cursor-pointer flex items-center gap-3">
                                <input type="radio" name="votes[{{ $pos->id }}]" value="" checked
                                       data-candidate-name=""
                                       class="text-gray-400 focus:ring-gray-400"/>
                                <span class="text-xs font-semibold text-gray-500">Abstain (Do not cast a vote for this position)</span>
                            </label>
                        @endif
                    </div>
                </div>
            @endforeach

            {{-- Review & Submit Action Bar --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-6 bg-white border border-gray-200 rounded-2xl shadow-xs">
                <div>
                    <p class="text-xs font-bold text-gray-700">Ready to finalize your ballot?</p>
                    <p class="text-[11px] text-gray-400 mt-0.5">You will review your full candidate slate before final submission.</p>
                </div>
                <button type="button" @click="prepareSummary()"
                        class="px-8 py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl transition shadow-sm flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Review &amp; Submit Ballot
                </button>
            </div>
        </form>

        {{-- ===== 2-STEP BALLOT CONFIRMATION MODAL ===== --}}
        <div x-show="reviewModalOpen" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
            <div @click.away="reviewModalOpen = false"
                 class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-gray-100 space-y-6 max-h-[90vh] overflow-y-auto">

                <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-black text-gray-900">Review Ballot Selections</h3>
                            <p class="text-xs text-gray-400">Verify your choices before final submission</p>
                        </div>
                    </div>
                    <button type="button" @click="reviewModalOpen = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Selected Slate Breakdown --}}
                <div class="space-y-3">
                    <template x-for="(item, idx) in summary" :key="idx">
                        <div class="p-3.5 rounded-xl border border-gray-100 bg-gray-50/70 text-xs">
                            <span class="font-bold text-gray-500 uppercase text-[10px] tracking-wider block" x-text="item.position"></span>
                            <div class="mt-1 space-y-0.5">
                                <template x-for="(candName, cIdx) in item.candidates" :key="cIdx">
                                    <p :class="candName.includes('Abstained') ? 'text-gray-400 italic font-medium' : 'text-gray-900 font-bold'"
                                       x-text="candName"></p>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Voter Certification Checkbox --}}
                <label class="p-4 bg-emerald-50/70 border border-emerald-200 rounded-xl flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" x-model="verified" class="mt-0.5 text-emerald-600 focus:ring-emerald-500 rounded"/>
                    <span class="text-xs text-emerald-950 font-medium leading-relaxed">
                        I certify that these are my deliberate and final secret ballot choices. I understand that once submitted, this vote cannot be revoked or modified.
                    </span>
                </label>

                {{-- Modal Action Buttons --}}
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @click="reviewModalOpen = false"
                            class="px-4 py-2.5 text-xs font-bold text-gray-600 hover:text-gray-800 transition">
                        Change Selections
                    </button>
                    <button type="button"
                            :disabled="!verified"
                            :class="!verified ? 'opacity-50 cursor-not-allowed bg-gray-400' : 'bg-emerald-600 hover:bg-emerald-700'"
                            @click="document.getElementById('ballot-form').submit()"
                            class="px-6 py-2.5 text-white text-xs font-black rounded-xl transition shadow-xs flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Confirm &amp; Cast Ballot
                    </button>
                </div>

            </div>
        </div>

        <script>
            // Enforce max_winners cap on multi-winner checkbox groups
            document.querySelectorAll('[data-max]').forEach(function (group) {
                var max = parseInt(group.dataset.max, 10);
                var posId = group.dataset.pos;
                var boxes = group.querySelectorAll('.pos-checkbox-' + posId);

                boxes.forEach(function (box) {
                    box.addEventListener('change', function () {
                        var checked = group.querySelectorAll('.pos-checkbox-' + posId + ':checked').length;
                        boxes.forEach(function (b) {
                            if (!b.checked) b.disabled = checked >= max;
                        });
                    });
                });
            });
        </script>

    </div>
</x-dashboard-layout>
