<x-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('documents.index') }}" class="hover:text-gray-900 transition-colors">Documents</a>
            <span>/</span>
            <span class="text-gray-800 font-semibold truncate">{{ Str::limit($document->title, 32) }}</span>
        </div>
    </x-slot>

    <div class="space-y-6">

        {{-- Flash messages --}}
        @foreach(['success', 'error'] as $type)
            @if(session($type))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition
                     class="flex items-center gap-3 p-4 rounded-xl text-sm border
                            {{ $type === 'success' ? 'bg-comsoc-50 border-comsoc-200 text-comsoc-800' : 'bg-red-50 border-red-200 text-red-800' }}">
                    <span class="font-medium">{{ session($type) }}</span>
                </div>
            @endif
        @endforeach

        {{-- Document Header Card --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-comsoc-950 to-comsoc-900 text-white">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-white/15 text-gold-300">
                                {{ $document->getCategoryLabel() }}
                            </span>
                            <span class="text-xs text-comsoc-300">Version {{ $document->version }}</span>
                        </div>
                        <h1 class="text-xl sm:text-2xl font-black text-white leading-tight">
                            {{ $document->title }}
                        </h1>
                    </div>

                    <div class="flex items-center gap-3 shrink-0">
                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-black shadow-xs {{ $document->getStatusColor() }}">
                            {{ $document->getStatusLabel() }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- File Details Strip --}}
            <div class="p-6 bg-gray-50/50 border-b border-gray-100">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 bg-white border border-gray-200 rounded-xl shadow-xs">
                    <div class="flex items-center gap-3.5">
                        <div class="w-12 h-12 rounded-xl bg-comsoc-100 border border-comsoc-200 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6 text-comsoc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-gray-900 text-sm truncate">{{ $document->file_name }}</p>
                            <div class="flex items-center gap-3 text-xs text-gray-400 mt-0.5">
                                <span>{{ $document->getFormattedFileSize() }}</span>
                                <span>·</span>
                                <span>Uploaded {{ $document->created_at->diffForHumans() }}</span>
                                <span>·</span>
                                <span>By <strong>{{ $document->uploader->name }}</strong></span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        @if(str_contains(strtolower($document->file_mime), 'pdf') || str_contains(strtolower($document->file_name), '.pdf'))
                            <a href="{{ route('documents.preview', $document) }}"
                               target="_blank"
                               class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Preview
                            </a>
                        @endif

                        <a href="{{ route('documents.download', $document) }}"
                           class="px-4 py-2 bg-comsoc-700 hover:bg-comsoc-800 text-white text-xs font-bold rounded-xl transition shadow-xs flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Download File
                        </a>
                    </div>
                </div>
            </div>

            {{-- Metadata & Notes --}}
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-gray-400 font-semibold uppercase tracking-wider block mb-1">Associated Event</span>
                        @if($document->event)
                            <a href="{{ route('events.show', $document->event) }}" class="font-bold text-comsoc-600 hover:underline flex items-center gap-1.5 text-sm">
                                <span>📅</span> {{ $document->event->title }}
                            </a>
                        @else
                            <p class="text-gray-600 font-medium">None (General Organization Document)</p>
                        @endif
                    </div>
                    <div>
                        <span class="text-gray-400 font-semibold uppercase tracking-wider block mb-1">Submission Status</span>
                        @if($document->submitted_at)
                            <p class="text-gray-700 font-semibold">Submitted on {{ $document->submitted_at->format('M d, Y g:i A') }}</p>
                        @else
                            <p class="text-amber-600 font-semibold">Draft (Not yet submitted for review)</p>
                        @endif
                    </div>
                </div>

                @if($document->notes)
                    <div class="pt-4 border-t border-gray-100">
                        <span class="text-gray-400 font-semibold uppercase tracking-wider text-xs block mb-1">Submitter Notes</span>
                        <div class="p-3.5 bg-gray-50 rounded-xl text-xs text-gray-700 leading-relaxed">
                            {{ $document->notes }}
                        </div>
                    </div>
                @endif
            </div>

            {{-- Officer Action Bar --}}
            @if(Auth::id() === $document->uploaded_by || Auth::user()->isAdmin() || Auth::user()->isAdviser())
                <div class="p-5 bg-gray-50 border-t border-gray-200 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        @can('update', $document)
                            <a href="{{ route('documents.edit', $document) }}"
                               class="px-3.5 py-2 bg-white hover:bg-gray-100 text-gray-700 text-xs font-bold rounded-xl border border-gray-300 transition">
                                Edit & Replace File
                            </a>
                        @endcan

                        @can('delete', $document)
                            <form method="POST" action="{{ route('documents.destroy', $document) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        onclick="return confirm('Delete this draft document?')"
                                        class="px-3 py-2 text-xs text-red-600 hover:text-red-800 font-bold transition">
                                    Delete Draft
                                </button>
                            </form>
                        @endcan
                    </div>

                    <div class="flex items-center gap-2">
                        {{-- Submit Button --}}
                        @can('submit', $document)
                            <form method="POST" action="{{ route('documents.submit', $document) }}">
                                @csrf
                                <button type="submit"
                                        onclick="return confirm('Submit this document for Adviser review?')"
                                        class="px-5 py-2 bg-comsoc-700 hover:bg-comsoc-800 text-white text-xs font-bold rounded-xl transition shadow-xs flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                    </svg>
                                    Submit for Review
                                </button>
                            </form>
                        @endcan

                        {{-- Adviser Review Shortcut --}}
                        @if(Auth::user()->isAdviser() || Auth::user()->isAdmin())
                            @if(in_array($document->status, [App\Models\Document::STATUS_SUBMITTED, App\Models\Document::STATUS_UNDER_REVIEW]))
                                <a href="{{ route('documents.review.show', $document) }}"
                                   class="px-5 py-2 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-xl transition shadow-xs flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                    </svg>
                                    Review Document
                                </a>
                            @endif
                        @endif
                    </div>
                </div>
            @endif
        </div>

        {{-- Review Remarks & Audit History --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-4 h-4 text-comsoc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Review & Approval History
            </h3>

            @forelse($document->reviews as $rev)
                <div class="p-4 rounded-xl border {{ $rev->action === 'approved' ? 'bg-green-50/50 border-green-200' : ($rev->action === 'returned' ? 'bg-orange-50/50 border-orange-200' : 'bg-red-50/50 border-red-200') }}">
                    <div class="flex items-center justify-between gap-3 mb-2">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-black {{ $rev->getActionColor() }}">
                                {{ $rev->getActionLabel() }}
                            </span>
                            <span class="text-xs font-bold text-gray-800">by {{ $rev->reviewer->name }} (Adviser)</span>
                        </div>
                        <span class="text-xs text-gray-400">{{ $rev->created_at->format('M d, Y g:i A') }}</span>
                    </div>
                    @if($rev->remarks)
                        <p class="text-xs text-gray-700 mt-1 pl-1 italic">
                            "{{ $rev->remarks }}"
                        </p>
                    @endif
                </div>
            @empty
                <p class="text-xs text-gray-400 italic">No formal reviews recorded yet.</p>
            @endforelse
        </div>

    </div>
</x-dashboard-layout>
