<x-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-base font-bold text-gray-900">Edit Election</h1>
            <a href="{{ route('elections.show', $election) }}" class="text-xs font-bold text-comsoc-600 hover:text-comsoc-800 transition">
                ← Back to Election
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <form method="POST" action="{{ route('elections.update', $election) }}" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Election Overview Card --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-xs space-y-4">
                <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide border-b border-gray-100 pb-3">
                    Election Information
                </h2>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Election Title *</label>
                        <input type="text" name="title" value="{{ old('title', $election->title) }}" required
                               class="w-full text-sm border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500"/>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Academic Year *</label>
                            <input type="text" name="academic_year" value="{{ old('academic_year', $election->academic_year) }}" required
                                   class="w-full text-sm border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500"/>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Description &amp; Guidelines</label>
                        <textarea name="description" rows="3"
                                  class="w-full text-sm border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500">{{ old('description', $election->description) }}</textarea>
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
                        <input type="datetime-local" name="filing_start_at"
                               value="{{ old('filing_start_at', $election->filing_start_at ? $election->filing_start_at->format('Y-m-d\TH:i') : '') }}"
                               class="w-full text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500"/>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Candidacy Filing Deadline</label>
                        <input type="datetime-local" name="filing_end_at"
                               value="{{ old('filing_end_at', $election->filing_end_at ? $election->filing_end_at->format('Y-m-d\TH:i') : '') }}"
                               class="w-full text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500"/>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Voting Booth Opens</label>
                        <input type="datetime-local" name="voting_start_at"
                               value="{{ old('voting_start_at', $election->voting_start_at ? $election->voting_start_at->format('Y-m-d\TH:i') : '') }}"
                               class="w-full text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500"/>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Voting Booth Closes</label>
                        <input type="datetime-local" name="voting_end_at"
                               value="{{ old('voting_end_at', $election->voting_end_at ? $election->voting_end_at->format('Y-m-d\TH:i') : '') }}"
                               class="w-full text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500"/>
                    </div>
                </div>
            </div>

            {{-- Submit Controls --}}
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('elections.show', $election) }}"
                   class="px-5 py-2.5 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-xl transition">
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-2.5 bg-comsoc-700 hover:bg-comsoc-800 text-white text-xs font-bold rounded-xl transition shadow-xs">
                    Save Changes
                </button>
            </div>
        </form>

        {{-- Danger Zone: Delete Election --}}
        @can('delete', $election)
            <div class="bg-red-50 border border-red-200 rounded-2xl p-6 shadow-2xs space-y-3">
                <h3 class="text-sm font-bold text-red-900">Danger Zone</h3>
                <p class="text-xs text-red-700">Deleting this election will remove all positions, candidates, and recorded votes permanently.</p>
                <form method="POST" action="{{ route('elections.destroy', $election) }}"
                      onsubmit="return confirm('Are you absolutely sure you want to delete this election? This cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-xl transition">
                        Delete Election
                    </button>
                </form>
            </div>
        @endcan
    </div>
</x-dashboard-layout>
