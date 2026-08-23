<x-dashboard-layout>
    <x-slot name="header">
        <h1 class="text-lg font-semibold text-gray-800">Event Approval Queue</h1>
    </x-slot>

    <div class="space-y-6">

        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition
                 class="flex items-center gap-3 p-4 bg-comsoc-50 border border-comsoc-200 rounded-xl text-comsoc-700 text-sm">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Pending events --}}
        <div>
            <h2 class="text-sm font-bold text-gray-800 mb-3">
                Pending Review
                @if($pending->isNotEmpty())
                    <span class="ml-2 inline-flex items-center justify-center w-5 h-5 rounded-full bg-yellow-100 text-yellow-700 text-xs font-black">
                        {{ $pending->count() }}
                    </span>
                @endif
            </h2>

            @forelse($pending as $event)
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden mb-3">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 bg-gray-50">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $event->getStatusColor() }}">
                                {{ $event->getStatusLabel() }}
                            </span>
                            <p class="text-sm font-bold text-gray-900">{{ $event->title }}</p>
                        </div>
                        <span class="text-xs text-gray-400">Submitted {{ $event->updated_at->diffForHumans() }}</span>
                    </div>
                    <div class="px-5 py-4 flex flex-wrap items-center justify-between gap-4">
                        <div class="text-xs text-gray-600 space-y-1">
                            <p class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ $event->event_date->format('M d, Y') }} · {{ \Carbon\Carbon::parse($event->start_time)->format('g:i A') }}
                            </p>
                            <p class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                {{ $event->venue }}
                            </p>
                            <p class="text-xs text-gray-400 mt-1">Created by {{ $event->creator->name }}</p>
                        </div>
                        <a href="{{ route('events.approval.show', $event) }}"
                           class="inline-flex items-center gap-2 px-5 py-2.5 bg-comsoc-600 text-white text-sm font-semibold rounded-lg hover:bg-comsoc-700 transition-colors shadow-sm">
                            Review Event
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </div>
            @empty
                <div class="bg-white border border-gray-200 rounded-xl p-10 text-center shadow-sm">
                    <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-gray-500 font-medium text-sm">No events pending review.</p>
                </div>
            @endforelse
        </div>

        {{-- Approval history --}}
        <div>
            <h2 class="text-sm font-bold text-gray-800 mb-3">Approval History</h2>
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                @foreach(['Event','Action','Reviewed By','Remarks','Date'] as $col)
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ $col }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($history as $approval)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 max-w-[200px] truncate">
                                        {{ $approval->event?->title ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @php $ac = ['approved'=>'bg-comsoc-100 text-comsoc-700','rejected'=>'bg-red-100 text-red-700','returned'=>'bg-orange-100 text-orange-700']; @endphp
                                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $ac[$approval->action] ?? 'bg-gray-100' }}">
                                            {{ ucfirst($approval->action) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $approval->reviewer->name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-500 max-w-xs truncate">{{ $approval->remarks ?? '—' }}</td>
                                    <td class="px-4 py-3 text-xs text-gray-400 whitespace-nowrap">{{ $approval->created_at->format('M d, Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-400 text-sm">No approval history yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="mt-3">{{ $history->links() }}</div>
        </div>
    </div>
</x-dashboard-layout>
