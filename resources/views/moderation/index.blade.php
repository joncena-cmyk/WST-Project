<x-dashboard-layout>
    <x-slot name="header">
        <h1 class="text-lg font-semibold text-gray-800">Content Moderation</h1>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-5">

        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                 x-transition
                 class="flex items-center gap-3 p-4 bg-comsoc-50 border border-comsoc-200 rounded-xl text-comsoc-700 text-sm">
                <svg class="w-5 h-5 text-comsoc-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Stats strip --}}
        <div class="grid grid-cols-2 gap-4">
            @foreach([
                ['Pending Reports', $reports->total(), 'text-yellow-700'],
                ['Open Cases',      $reports->total(), 'text-red-700'],
            ] as [$label, $count, $color])
                <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                    <p class="text-xs text-gray-500 font-medium">{{ $label }}</p>
                    <p class="text-2xl font-black {{ $color }} mt-1">{{ $count }}</p>
                </div>
            @endforeach
        </div>

        {{-- Reports list --}}
        @forelse ($reports as $report)
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                {{-- Report header --}}
                <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100 bg-gray-50">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                            Pending
                        </span>
                        <span class="text-xs text-gray-500">
                            Reported by <strong>{{ $report->reporter->name }}</strong>
                            · {{ $report->created_at->diffForHumans() }}
                        </span>
                    </div>
                    <span class="text-xs font-semibold text-red-600 uppercase tracking-wide">
                        {{ \App\Models\Report::REASONS[$report->reason] ?? $report->reason }}
                    </span>
                </div>

                {{-- Reported content preview --}}
                <div class="px-5 py-4">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">
                        Reported {{ class_basename($report->reportable_type) }}
                    </p>
                    @if ($report->reportable)
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 text-sm text-gray-700 line-clamp-3">
                            {{ $report->reportable->body }}
                        </div>
                        <p class="text-xs text-gray-400 mt-1">
                            By {{ $report->reportable->author->name ?? 'Unknown' }}
                        </p>
                    @else
                        <p class="text-sm text-gray-400 italic">Content no longer exists.</p>
                    @endif

                    @if ($report->notes)
                        <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-xs text-yellow-800">
                            <strong>Reporter's note:</strong> {{ $report->notes }}
                        </div>
                    @endif
                </div>

                {{-- Resolution form --}}
                <div class="px-5 py-4 border-t border-gray-100" x-data="{ action: 'resolve' }">
                    <form method="POST" action="{{ route('moderation.resolve', $report) }}">
                        @csrf
                        <div class="flex flex-wrap items-start gap-4">
                            {{-- Action choice --}}
                            <div class="flex items-center gap-3">
                                <label class="flex items-center gap-2 text-sm cursor-pointer">
                                    <input type="radio" name="action" value="resolve"
                                           x-model="action"
                                           class="text-comsoc-600 focus:ring-comsoc-500">
                                    <span class="font-medium text-gray-700">Resolve</span>
                                </label>
                                <label class="flex items-center gap-2 text-sm cursor-pointer">
                                    <input type="radio" name="action" value="dismiss"
                                           x-model="action"
                                           class="text-gray-600 focus:ring-gray-500">
                                    <span class="font-medium text-gray-700">Dismiss</span>
                                </label>
                            </div>

                            {{-- Hide content checkbox --}}
                            @if ($report->reportable)
                                <label class="flex items-center gap-2 text-sm cursor-pointer">
                                    <input type="checkbox" name="hide_content" value="1"
                                           class="rounded text-red-600 focus:ring-red-500">
                                    <span class="text-gray-700">Hide content from feed</span>
                                </label>
                            @endif
                        </div>

                        <div class="mt-3">
                            <textarea name="resolution_notes"
                                      rows="2"
                                      placeholder="Resolution notes (optional)..."
                                      class="w-full text-sm border-gray-300 rounded-lg focus:ring-comsoc-500 focus:border-comsoc-500 resize-none"></textarea>
                        </div>

                        <div class="flex justify-end mt-3">
                            <button type="submit"
                                    :class="action === 'resolve'
                                        ? 'bg-comsoc-600 hover:bg-comsoc-700 text-white'
                                        : 'bg-gray-200 hover:bg-gray-300 text-gray-700'"
                                    class="px-5 py-2 text-sm font-semibold rounded-lg transition-colors">
                                <span x-text="action === 'resolve' ? 'Resolve Report' : 'Dismiss Report'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white border border-gray-200 rounded-xl p-12 text-center shadow-sm">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-gray-500 font-medium">No pending reports.</p>
                <p class="text-gray-400 text-sm mt-1">Everything looks clean!</p>
            </div>
        @endforelse

        {{-- Pagination --}}
        <div class="pb-4">{{ $reports->links() }}</div>
    </div>
</x-dashboard-layout>
