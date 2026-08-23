<x-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('documents.index') }}" class="hover:text-gray-900 transition-colors">Documents</a>
            <span>/</span>
            <span class="text-gray-800 font-semibold">Adviser Review Queue</span>
        </div>
    </x-slot>

    <div class="space-y-6">

        {{-- Flash Alerts --}}
        @foreach(['success', 'error', 'info'] as $type)
            @if(session($type))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition
                     class="flex items-center gap-3 p-4 rounded-xl text-sm border
                            {{ $type === 'success' ? 'bg-comsoc-50 border-comsoc-200 text-comsoc-800' : 'bg-red-50 border-red-200 text-red-800' }}">
                    <span class="font-medium">{{ session($type) }}</span>
                </div>
            @endif
        @endforeach

        {{-- Queue Tabs --}}
        <div x-data="{ tab: 'pending' }" class="space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex p-1 bg-gray-100 rounded-xl">
                    <button type="button"
                            @click="tab = 'pending'"
                            :class="tab === 'pending' ? 'bg-white text-comsoc-800 font-bold shadow-xs' : 'text-gray-600 hover:text-gray-900 font-medium'"
                            class="px-4 py-2 text-xs sm:text-sm rounded-lg transition-all flex items-center gap-2">
                        <span>Awaiting Review</span>
                        <span class="px-2 py-0.5 text-xs font-black rounded-full {{ $pending->count() > 0 ? 'bg-amber-100 text-amber-800' : 'bg-gray-200 text-gray-600' }}">
                            {{ $pending->count() }}
                        </span>
                    </button>
                    <button type="button"
                            @click="tab = 'history'"
                            :class="tab === 'history' ? 'bg-white text-comsoc-800 font-bold shadow-xs' : 'text-gray-600 hover:text-gray-900 font-medium'"
                            class="px-4 py-2 text-xs sm:text-sm rounded-lg transition-all flex items-center gap-2">
                        <span>Review History</span>
                    </button>
                </div>

                <a href="{{ route('documents.index') }}" class="text-xs font-bold text-comsoc-600 hover:text-comsoc-800 transition">
                    ← All Documents
                </a>
            </div>

            {{-- 1. PENDING QUEUE TAB --}}
            <div x-show="tab === 'pending'" class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-5 border-b border-gray-100 bg-amber-50/40 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-amber-950">Documents Awaiting Adviser Decision</h2>
                        <p class="text-xs text-amber-800/80 mt-0.5">Review submissions and provide official approval, return notes, or rejection remarks.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50/80">
                            <tr>
                                <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Document</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Category</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Linked Event</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Submitted By</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Submitted</th>
                                <th class="px-5 py-3.5 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($pending as $doc)
                                <tr class="hover:bg-gray-50/60 transition">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-center shrink-0">
                                                <svg class="w-5 h-5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            </div>
                                            <div class="min-w-0 max-w-xs">
                                                <a href="{{ route('documents.review.show', $doc) }}" class="font-bold text-sm text-gray-900 hover:text-comsoc-700 transition block truncate">
                                                    {{ $doc->title }}
                                                </a>
                                                <p class="text-xs text-gray-400 mt-0.5 truncate">
                                                    {{ $doc->file_name }} · v{{ $doc->version }} · {{ $doc->getFormattedFileSize() }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-xs">
                                        <span class="inline-flex px-2.5 py-1 rounded-lg font-semibold bg-gray-100 text-gray-700">
                                            {{ $doc->getCategoryLabel() }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-xs">
                                        @if($doc->event)
                                            <span class="font-semibold text-comsoc-600 flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5 text-comsoc-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                {{ Str::limit($doc->event->title, 20) }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">— Organization</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-xs font-bold text-gray-800">
                                        {{ $doc->uploader->name }}
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-xs text-gray-500">
                                        {{ $doc->submitted_at ? $doc->submitted_at->diffForHumans() : $doc->created_at->diffForHumans() }}
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-right text-xs">
                                        <a href="{{ route('documents.review.show', $doc) }}"
                                           class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-xl transition shadow-xs">
                                            Review →
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-12 text-center text-gray-400 text-sm">
                                        <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <p class="font-bold text-gray-600">Queue is clear!</p>
                                        <p class="text-xs text-gray-400 mt-0.5">There are no documents currently awaiting Adviser review.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 2. HISTORY TAB --}}
            <div x-show="tab === 'history'" class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50/80">
                            <tr>
                                <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Document</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Decision</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Remarks</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Reviewed By</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-5 py-3.5 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">View</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($history as $item)
                                <tr class="hover:bg-gray-50/60 transition">
                                    <td class="px-5 py-4">
                                        <div class="font-bold text-sm text-gray-900 truncate max-w-xs">
                                            {{ $item->document->title }}
                                        </div>
                                        <p class="text-xs text-gray-400 truncate">
                                            Uploaded by {{ $item->document->uploader->name }}
                                        </p>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-xs">
                                        <span class="inline-flex px-2.5 py-1 rounded-full font-black {{ $item->getActionColor() }}">
                                            {{ $item->getActionLabel() }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-xs text-gray-600 max-w-xs truncate">
                                        {{ $item->remarks ?: '— No remarks provided —' }}
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-xs font-bold text-gray-800">
                                        {{ $item->reviewer->name }}
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-xs text-gray-400">
                                        {{ $item->created_at->format('M d, Y g:i A') }}
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-right text-xs">
                                        <a href="{{ route('documents.show', $item->document) }}"
                                           class="text-comsoc-600 hover:text-comsoc-800 font-bold">
                                            View Doc →
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-12 text-center text-gray-400 text-sm">
                                        No review records logged yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($history->hasPages())
                    <div class="px-5 py-4 border-t border-gray-100">
                        {{ $history->links() }}
                    </div>
                @endif
            </div>
        </div>

    </div>
</x-dashboard-layout>
