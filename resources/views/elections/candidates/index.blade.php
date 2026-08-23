<x-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-base font-bold text-gray-900">Candidacy Application Queue</h1>
                <p class="text-xs text-gray-500">Review student candidate filings and eligibility</p>
            </div>
            <a href="{{ route('elections.index') }}" class="text-xs font-bold text-comsoc-600 hover:text-comsoc-800 transition">
                ← Back to Elections
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">

        {{-- ===== FILTER BAR ===== --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-xs">
            <form method="GET" action="{{ route('candidates.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Status</label>
                    <select name="status" class="w-full text-xs border border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending Review</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Election</label>
                    <select name="election_id" class="w-full text-xs border border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500">
                        <option value="">All Elections</option>
                        @foreach($elections as $el)
                            <option value="{{ $el->id }}" {{ request('election_id') == $el->id ? 'selected' : '' }}>
                                {{ $el->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit"
                            class="px-4 py-2 bg-comsoc-700 hover:bg-comsoc-800 text-white text-xs font-bold rounded-xl transition shadow-xs">
                        Filter Applications
                    </button>
                    @if(request()->hasAny(['status', 'election_id']))
                        <a href="{{ route('candidates.index') }}"
                           class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-bold rounded-xl transition">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- ===== APPLICATIONS LIST ===== --}}
        <div class="space-y-4">
            @forelse($candidates as $candidate)
                <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-xs space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                        <div class="flex items-start gap-4 min-w-0">
                            <img src="{{ $candidate->user->getProfilePictureUrl() }}"
                                 alt="{{ $candidate->user->name }}"
                                 class="w-14 h-14 rounded-full object-cover border-2 border-comsoc-200 shrink-0"/>
                            <div class="min-w-0 space-y-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="font-bold text-gray-900 text-base truncate">{{ $candidate->user->name }}</h3>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $candidate->getStatusColor() }}">
                                        {{ ucfirst($candidate->status) }}
                                    </span>
                                </div>
                                <p class="text-xs text-comsoc-700 font-bold">
                                    Running for: {{ $candidate->position->title }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    Election: {{ $candidate->election->title }} · Filed {{ $candidate->created_at->diffForHumans() }}
                                </p>
                                @if($candidate->party_name)
                                    <p class="text-xs text-gray-500">Party Slate: <span class="font-semibold text-gray-700">{{ $candidate->party_name }}</span></p>
                                @endif
                            </div>
                        </div>

                        {{-- Decision Forms for Pending Applications --}}
                        @if($candidate->status === 'pending')
                            <div class="flex items-center gap-2 shrink-0">
                                <form method="POST" action="{{ route('candidates.review', $candidate) }}">
                                    @csrf
                                    <input type="hidden" name="decision" value="approved">
                                    <button type="submit"
                                            class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition shadow-xs flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Approve
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('candidates.review', $candidate) }}"
                                      onsubmit="return confirm('Reject this candidacy application?');">
                                    @csrf
                                    <input type="hidden" name="decision" value="rejected">
                                    <button type="submit"
                                            class="px-4 py-2 bg-red-50 hover:bg-red-100 text-red-700 text-xs font-bold rounded-xl border border-red-200 transition">
                                        Reject
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="text-right text-xs text-gray-400 shrink-0">
                                <p>Reviewed by {{ $candidate->reviewer ? $candidate->reviewer->name : 'Adviser' }}</p>
                                <p>{{ $candidate->reviewed_at ? $candidate->reviewed_at->format('M d, Y') : '' }}</p>
                            </div>
                        @endif
                    </div>

                    {{-- Platform Statement --}}
                    @if($candidate->platform)
                        <div class="p-4 bg-gray-50 rounded-xl border border-gray-100 text-xs text-gray-700 leading-relaxed">
                            <p class="font-bold text-[11px] text-gray-400 uppercase tracking-wider mb-1">Platform Statement</p>
                            <p class="whitespace-pre-line">{{ $candidate->platform }}</p>
                        </div>
                    @endif
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-gray-200 p-12 text-center shadow-xs">
                    <p class="text-gray-800 font-bold text-base">No candidacy applications match your filter.</p>
                </div>
            @endforelse
        </div>

        @if($candidates->hasPages())
            <div class="pt-2">
                {{ $candidates->links() }}
            </div>
        @endif

    </div>
</x-dashboard-layout>
