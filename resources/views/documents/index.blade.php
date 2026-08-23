<x-dashboard-layout>
    <x-slot name="header">
        <h1 class="text-base font-bold text-gray-900">Organization Documents</h1>
    </x-slot>

    <div class="space-y-6">

        {{-- Flash Alerts --}}
        @foreach(['success', 'error', 'info'] as $type)
            @if(session($type))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition
                     class="flex items-center gap-3 p-4 rounded-xl text-sm border
                            {{ $type === 'success' ? 'bg-comsoc-50 border-comsoc-200 text-comsoc-800' : ($type === 'error' ? 'bg-red-50 border-red-200 text-red-800' : 'bg-blue-50 border-blue-200 text-blue-800') }}">
                    <svg class="w-5 h-5 shrink-0 {{ $type === 'success' ? 'text-comsoc-600' : ($type === 'error' ? 'text-red-500' : 'text-blue-500') }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        @if($type === 'success')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        @elseif($type === 'error')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        @endif
                    </svg>
                    <span class="font-medium">{{ session($type) }}</span>
                </div>
            @endif
        @endforeach

        {{-- Metric Summary Strip --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
            <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-xs">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Documents</p>
                <p class="text-2xl font-black text-gray-900 mt-1">{{ $counts['all'] }}</p>
            </div>
            <div class="bg-white border border-blue-100 rounded-2xl p-4 shadow-xs bg-blue-50/40">
                <p class="text-xs font-bold text-blue-600 uppercase tracking-wider">My Uploads</p>
                <p class="text-2xl font-black text-blue-700 mt-1">{{ $counts['mine'] }}</p>
            </div>
            <div class="bg-white border border-amber-100 rounded-2xl p-4 shadow-xs bg-amber-50/40">
                <p class="text-xs font-bold text-amber-600 uppercase tracking-wider">Pending Review</p>
                <p class="text-2xl font-black text-amber-700 mt-1">{{ $counts['pending'] }}</p>
            </div>
            <div class="bg-white border border-green-100 rounded-2xl p-4 shadow-xs bg-green-50/40">
                <p class="text-xs font-bold text-green-600 uppercase tracking-wider">Approved</p>
                <p class="text-2xl font-black text-green-700 mt-1">{{ $counts['approved'] }}</p>
            </div>
        </div>

        {{-- Action Bar & Filters --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm space-y-4">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                {{-- Search Box --}}
                <form method="GET" action="{{ route('documents.index') }}" class="flex-1 flex gap-2">
                    @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
                    @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                    @if(request('mine')) <input type="hidden" name="mine" value="{{ request('mine') }}"> @endif
                    
                    <div class="relative flex-1">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Search documents by title, file name, or notes..."
                               class="w-full pl-9 pr-4 py-2 border-gray-200 rounded-xl text-sm focus:border-comsoc-500 focus:ring-comsoc-500"/>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition">
                        Search
                    </button>
                </form>

                <div class="flex items-center gap-2">
                    @if(Auth::user()->isAdviser() || Auth::user()->isAdmin())
                        <a href="{{ route('documents.review.index') }}"
                           class="inline-flex items-center gap-1.5 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-bold rounded-xl transition shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                            Review Queue
                            @if($counts['pending'] > 0)
                                <span class="px-1.5 py-0.5 text-xs bg-white text-amber-800 font-black rounded-full">
                                    {{ $counts['pending'] }}
                                </span>
                            @endif
                        </a>
                    @endif

                    @can('create', App\Models\Document::class)
                        <a href="{{ route('documents.create') }}"
                           class="inline-flex items-center gap-1.5 px-4 py-2 bg-comsoc-600 hover:bg-comsoc-700 text-white text-sm font-bold rounded-xl transition shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Upload Document
                        </a>
                    @endcan
                </div>
            </div>

            {{-- Quick Filter Pills --}}
            <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-gray-100 text-xs">
                <span class="text-gray-400 font-semibold uppercase tracking-wider text-[10px]">Filter:</span>
                <a href="{{ route('documents.index') }}"
                   class="px-3 py-1 rounded-lg font-bold transition {{ !request()->hasAny(['mine', 'status', 'category']) ? 'bg-comsoc-700 text-white shadow-xs' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    All ({{ $counts['all'] }})
                </a>
                <a href="{{ route('documents.index', ['mine' => 1]) }}"
                   class="px-3 py-1 rounded-lg font-bold transition {{ request('mine') ? 'bg-blue-600 text-white shadow-xs' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    My Uploads ({{ $counts['mine'] }})
                </a>
                <a href="{{ route('documents.index', ['status' => 'submitted']) }}"
                   class="px-3 py-1 rounded-lg font-bold transition {{ request('status') === 'submitted' ? 'bg-amber-600 text-white shadow-xs' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Pending ({{ $counts['pending'] }})
                </a>
                <a href="{{ route('documents.index', ['status' => 'approved']) }}"
                   class="px-3 py-1 rounded-lg font-bold transition {{ request('status') === 'approved' ? 'bg-green-600 text-white shadow-xs' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Approved ({{ $counts['approved'] }})
                </a>
                <a href="{{ route('documents.index', ['status' => 'returned']) }}"
                   class="px-3 py-1 rounded-lg font-bold transition {{ request('status') === 'returned' ? 'bg-orange-600 text-white shadow-xs' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Returned ({{ $counts['returned'] }})
                </a>
            </div>
        </div>

        {{-- Documents Table --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50/80">
                        <tr>
                            <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Document</th>
                            <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Linked Event</th>
                            <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Uploaded By</th>
                            <th class="px-5 py-3.5 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($documents as $doc)
                            <tr class="hover:bg-gray-50/60 transition-colors">
                                {{-- Title & File Meta --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-comsoc-50 border border-comsoc-100 flex items-center justify-center shrink-0">
                                            <svg class="w-5 h-5 text-comsoc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </div>
                                        <div class="min-w-0 max-w-xs">
                                            <a href="{{ route('documents.show', $doc) }}" class="font-bold text-sm text-gray-900 hover:text-comsoc-700 transition truncate block">
                                                {{ $doc->title }}
                                            </a>
                                            <p class="text-xs text-gray-400 truncate mt-0.5">
                                                {{ $doc->file_name }} · {{ $doc->getFormattedFileSize() }} · v{{ $doc->version }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Category --}}
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-semibold bg-gray-100 text-gray-700">
                                        {{ $doc->getCategoryLabel() }}
                                    </span>
                                </td>

                                {{-- Event Association --}}
                                <td class="px-5 py-4 whitespace-nowrap">
                                    @if($doc->event)
                                        <a href="{{ route('events.show', $doc->event) }}" class="text-xs font-semibold text-comsoc-600 hover:underline flex items-center gap-1">
                                            <span>📅</span> {{ Str::limit($doc->event->title, 24) }}
                                        </a>
                                    @else
                                        <span class="text-xs text-gray-400">— Organization Level</span>
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold {{ $doc->getStatusColor() }}">
                                        {{ $doc->getStatusLabel() }}
                                    </span>
                                </td>

                                {{-- Uploader --}}
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="text-xs">
                                        <p class="font-bold text-gray-800">{{ $doc->uploader->name }}</p>
                                        <p class="text-gray-400">{{ $doc->created_at->format('M d, Y') }}</p>
                                    </div>
                                </td>

                                {{-- Actions --}}
                                <td class="px-5 py-4 whitespace-nowrap text-right text-xs">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('documents.download', $doc) }}"
                                           title="Download Document"
                                           class="p-1.5 bg-gray-100 hover:bg-comsoc-50 text-gray-600 hover:text-comsoc-700 rounded-lg transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                            </svg>
                                        </a>
                                        <a href="{{ route('documents.show', $doc) }}"
                                           class="px-3 py-1.5 bg-comsoc-50 hover:bg-comsoc-100 text-comsoc-700 font-bold rounded-lg transition">
                                            View →
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center text-gray-400 text-sm">
                                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="font-medium">No documents found matching your criteria.</p>
                                    @can('create', App\Models\Document::class)
                                        <a href="{{ route('documents.create') }}" class="text-comsoc-600 font-bold hover:underline mt-1 inline-block">
                                            Upload the first document →
                                        </a>
                                    @endcan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($documents->hasPages())
                <div class="px-5 py-4 border-t border-gray-100">
                    {{ $documents->links() }}
                </div>
            @endif
        </div>

    </div>
</x-dashboard-layout>
