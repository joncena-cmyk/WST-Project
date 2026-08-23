<x-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-base font-bold text-gray-900">Official Secret Ballot</h1>
            <a href="{{ route('elections.show', $election) }}" class="text-xs font-bold text-gray-500 hover:text-gray-700 transition">
                Exit Voting Booth
            </a>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-6">

        {{-- ===== BALLOT HEADER & CONFIDENTIALITY NOTICE ===== --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-xs space-y-3">
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 uppercase tracking-wide">
                    Live Ballot
                </span>
                <span class="text-xs text-gray-400">AY {{ $election->academic_year }}</span>
            </div>
            <h2 class="text-xl font-black text-gray-900">{{ $election->title }}</h2>
            <div class="p-4 bg-emerald-50/50 border border-emerald-200 rounded-xl text-xs text-emerald-900 leading-relaxed flex items-start gap-3">
                <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <div>
                    <p class="font-bold">Secret Ballot &amp; Voter Privacy Protected</p>
                    <p class="text-emerald-800 mt-0.5">Your votes are cryptographically anonymized. Once submitted, your ballot cannot be altered or traced back to your individual student identity.</p>
                </div>
            </div>
        </div>

        {{-- ===== BALLOT FORM ===== --}}
        <form method="POST" action="{{ route('elections.vote.cast', $election) }}"
              onsubmit="return confirm('Are you ready to submit your official ballot? Once submitted, it cannot be edited.');"
              class="space-y-6">
            @csrf

            @foreach($election->positions as $pos)
                <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-xs space-y-4">
                    <div class="border-b border-gray-100 pb-3">
                        <h3 class="text-base font-bold text-gray-900">{{ $pos->title }}</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Select up to {{ $pos->max_winners }} candidate or abstain</p>
                    </div>

                    <div class="space-y-3">
                        {{-- Candidate Options --}}
                        @forelse($pos->approvedCandidates as $cand)
                            <label class="p-4 rounded-xl border border-gray-200 bg-gray-50/40 hover:bg-white hover:border-comsoc-400 transition cursor-pointer flex items-start gap-4">
                                <input type="radio" name="votes[{{ $pos->id }}]" value="{{ $cand->id }}"
                                       class="mt-1 text-comsoc-600 focus:ring-comsoc-500"/>
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

                        {{-- Abstain Option --}}
                        <label class="p-3.5 rounded-xl border border-dashed border-gray-200 bg-white hover:bg-gray-50 transition cursor-pointer flex items-center gap-3">
                            <input type="radio" name="votes[{{ $pos->id }}]" value="" checked
                                   class="text-gray-400 focus:ring-gray-400"/>
                            <span class="text-xs font-semibold text-gray-500">Abstain (Do not cast a vote for this position)</span>
                        </label>
                    </div>
                </div>
            @endforeach

            {{-- Submit Official Ballot --}}
            <div class="flex items-center justify-between p-6 bg-white border border-gray-200 rounded-2xl shadow-xs">
                <span class="text-xs text-gray-500">Ensure all selections are final before submitting.</span>
                <button type="submit"
                        class="px-8 py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl transition shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Submit Official Ballot
                </button>
            </div>
        </form>

    </div>
</x-dashboard-layout>
