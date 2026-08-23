<x-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('documents.review.index') }}" class="hover:text-gray-900 transition-colors">Review Queue</a>
            <span>/</span>
            <span class="text-gray-800 font-semibold truncate">{{ Str::limit($document->title, 32) }}</span>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-6">

        {{-- Document Summary Card --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-amber-950 to-amber-900 text-white">
                <div class="flex items-center gap-2 mb-2">
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-white/15 text-amber-300">
                        {{ $document->getCategoryLabel() }}
                    </span>
                    <span class="text-xs text-amber-200">Version {{ $document->version }}</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-white leading-tight">
                    {{ $document->title }}
                </h1>
                <p class="text-xs text-amber-200 mt-2">
                    Submitted by <strong>{{ $document->uploader->name }}</strong> · {{ $document->submitted_at ? $document->submitted_at->diffForHumans() : $document->created_at->diffForHumans() }}
                </p>
            </div>

            {{-- File Attachment Box --}}
            <div class="p-6 bg-amber-50/30 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-xl bg-white border border-amber-200 flex items-center justify-center shrink-0 shadow-xs">
                        <svg class="w-6 h-6 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="font-bold text-gray-900 text-sm truncate">{{ $document->file_name }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $document->getFormattedFileSize() }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    @if(str_contains(strtolower($document->file_mime), 'pdf') || str_contains(strtolower($document->file_name), '.pdf'))
                        <a href="{{ route('documents.preview', $document) }}"
                           target="_blank"
                           class="px-4 py-2 bg-white hover:bg-gray-100 text-gray-700 text-xs font-bold rounded-xl border border-gray-200 transition shadow-xs flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Preview PDF
                        </a>
                    @endif

                    <a href="{{ route('documents.download', $document) }}"
                       class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded-xl transition shadow-xs flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Download & Review
                    </a>
                </div>
            </div>

            {{-- Context & Submitter Notes --}}
            <div class="p-6 space-y-4">
                @if($document->event)
                    <div>
                        <span class="text-gray-400 font-semibold uppercase tracking-wider text-xs block mb-1">Associated Event</span>
                        <p class="font-bold text-gray-800 text-sm">📅 {{ $document->event->title }} ({{ $document->event->event_date->format('M d, Y') }})</p>
                    </div>
                @endif

                @if($document->notes)
                    <div>
                        <span class="text-gray-400 font-semibold uppercase tracking-wider text-xs block mb-1">Submitter Notes</span>
                        <div class="p-4 bg-gray-50 rounded-xl text-xs text-gray-700 leading-relaxed border border-gray-100">
                            {{ $document->notes }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Decision Console Card --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 space-y-5">
            <div class="flex items-center gap-2 border-b border-gray-100 pb-3">
                <span class="w-1.5 h-4 bg-amber-500 rounded-full"></span>
                <h3 class="font-bold text-gray-900 text-base">Adviser Formal Decision</h3>
            </div>

            <form method="POST" action="{{ route('documents.review.process', $document) }}" class="space-y-5">
                @csrf

                {{-- Action Radio Cards --}}
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Select Review Action</label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <label class="relative flex flex-col p-4 bg-white border-2 border-gray-200 rounded-xl cursor-pointer hover:border-green-500 has-[:checked]:border-green-600 has-[:checked]:bg-green-50/40 transition">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-bold text-sm text-green-700">Approve</span>
                                <input type="radio" name="action" value="approved" required class="text-green-600 focus:ring-green-500"/>
                            </div>
                            <span class="text-xs text-gray-500">Document approved for official organization use.</span>
                        </label>

                        <label class="relative flex flex-col p-4 bg-white border-2 border-gray-200 rounded-xl cursor-pointer hover:border-orange-500 has-[:checked]:border-orange-600 has-[:checked]:bg-orange-50/40 transition">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-bold text-sm text-orange-700">Return for Revision</span>
                                <input type="radio" name="action" value="returned" class="text-orange-600 focus:ring-orange-500"/>
                            </div>
                            <span class="text-xs text-gray-500">Requires officer to revise and re-upload attachment.</span>
                        </label>

                        <label class="relative flex flex-col p-4 bg-white border-2 border-gray-200 rounded-xl cursor-pointer hover:border-red-500 has-[:checked]:border-red-600 has-[:checked]:bg-red-50/40 transition">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-bold text-sm text-red-700">Reject</span>
                                <input type="radio" name="action" value="rejected" class="text-red-600 focus:ring-red-500"/>
                            </div>
                            <span class="text-xs text-gray-500">Permanently reject document proposal.</span>
                        </label>
                    </div>
                </div>

                {{-- Remarks --}}
                <div>
                    <label for="remarks" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Adviser Remarks / Feedback <span class="text-gray-400 font-normal">(Included in audit trail and officer notification)</span>
                    </label>
                    <textarea id="remarks"
                              name="remarks"
                              rows="3"
                              placeholder="e.g. Approved with recommended budget cap. Or specify sections that need revision..."
                              class="w-full border-gray-300 focus:border-amber-500 focus:ring-amber-500 rounded-xl text-sm">{{ old('remarks') }}</textarea>
                </div>

                {{-- Submit Buttons --}}
                <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                    <a href="{{ route('documents.review.index') }}" class="text-xs font-bold text-gray-500 hover:text-gray-700">
                        ← Back to Queue
                    </a>
                    <button type="submit"
                            onclick="return confirm('Confirm and submit this review decision?')"
                            class="px-6 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-sm font-bold rounded-xl transition shadow-sm">
                        Submit Review Decision
                    </button>
                </div>
            </form>
        </div>

    </div>
</x-dashboard-layout>
