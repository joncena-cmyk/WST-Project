<x-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-base font-bold text-gray-900">Create New Election</h1>
            <a href="{{ route('elections.index') }}" class="text-xs font-bold text-comsoc-600 hover:text-comsoc-800 transition">
                ← Back to Elections
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto" x-data="{
        positions: [
            { title: 'President', max_winners: 1 },
            { title: 'Vice President for Internal Affairs', max_winners: 1 },
            { title: 'Vice President for External Affairs', max_winners: 1 },
            { title: 'Secretary General', max_winners: 1 },
            { title: 'Treasurer', max_winners: 1 },
            { title: 'Auditor', max_winners: 1 },
            { title: 'Public Information Officer', max_winners: 1 }
        ],
        addPosition() {
            this.positions.push({ title: '', max_winners: 1 });
        },
        removePosition(index) {
            if (this.positions.length > 1) {
                this.positions.splice(index, 1);
            }
        }
    }">
        <form method="POST" action="{{ route('elections.store') }}" class="space-y-6">
            @csrf

            {{-- Election Overview Card --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-xs space-y-4">
                <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide border-b border-gray-100 pb-3">
                    Election Information
                </h2>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Election Title *</label>
                        <input type="text" name="title" value="{{ old('title') }}" required
                               placeholder="e.g. COMSOC Executive Board General Elections 2026-2027"
                               class="w-full text-sm border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500"/>
                        @error('title')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Academic Year *</label>
                            <input type="text" name="academic_year" value="{{ old('academic_year', '2025-2026') }}" required
                                   placeholder="2025-2026"
                                   class="w-full text-sm border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500"/>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Description &amp; Guidelines</label>
                        <textarea name="description" rows="3"
                                  placeholder="Provide general election guidelines, eligibility rules, and schedule notes..."
                                  class="w-full text-sm border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Timeline Card --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-xs space-y-4">
                <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide border-b border-gray-100 pb-3">
                    Schedule &amp; Timelines
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Candidacy Filing Start</label>
                        <input type="datetime-local" name="filing_start_at" value="{{ old('filing_start_at') }}"
                               class="w-full text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500"/>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Candidacy Filing Deadline</label>
                        <input type="datetime-local" name="filing_end_at" value="{{ old('filing_end_at') }}"
                               class="w-full text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500"/>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Voting Booth Opens</label>
                        <input type="datetime-local" name="voting_start_at" value="{{ old('voting_start_at') }}"
                               class="w-full text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500"/>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Voting Booth Closes</label>
                        <input type="datetime-local" name="voting_end_at" value="{{ old('voting_end_at') }}"
                               class="w-full text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500"/>
                    </div>
                </div>
            </div>

            {{-- Electoral Positions Card --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-xs space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <div>
                        <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Electoral Positions</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Define executive and batch representative seats on the ballot</p>
                    </div>
                    <button type="button" @click="addPosition()"
                            class="px-3.5 py-1.5 bg-comsoc-50 hover:bg-comsoc-100 text-comsoc-700 text-xs font-bold rounded-xl border border-comsoc-200 transition flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Position
                    </button>
                </div>

                <div class="space-y-2.5">
                    <template x-for="(pos, idx) in positions" :key="idx">
                        <div class="flex items-center gap-2">
                            <span class="w-7 h-7 rounded-lg bg-gray-100 text-gray-500 text-xs font-bold flex items-center justify-center shrink-0" x-text="idx + 1"></span>
                            <input type="text" :name="'positions[' + idx + '][title]'" x-model="pos.title" required
                                   placeholder="Position Title (e.g. Vice President)"
                                   class="flex-1 text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500"/>
                            <div class="flex items-center gap-1 shrink-0">
                                <label class="text-[10px] font-bold text-gray-400 whitespace-nowrap">Max Winners</label>
                                <input type="number" :name="'positions[' + idx + '][max_winners]'" x-model.number="pos.max_winners"
                                       min="1" max="50"
                                       class="w-14 text-xs text-center border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500"/>
                            </div>
                            <button type="button" @click="removePosition(idx)"
                                    class="p-2 text-gray-300 hover:text-red-500 rounded-lg transition"
                                    :disabled="positions.length <= 1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Submit Controls --}}
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('elections.index') }}"
                   class="px-5 py-2.5 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-xl transition">
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-2.5 bg-comsoc-700 hover:bg-comsoc-800 text-white text-xs font-bold rounded-xl transition shadow-xs">
                    Create Election
                </button>
            </div>
        </form>
    </div>
</x-dashboard-layout>
