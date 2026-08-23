<x-dashboard-layout>
    <x-slot name="header">
        <h1 class="text-lg font-semibold text-gray-800">Manage Events</h1>
    </x-slot>

    <div class="space-y-5">

        @foreach(['success','error'] as $type)
            @if(session($type))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3500)" x-transition
                     class="flex items-center gap-3 p-4 rounded-xl text-sm border
                            {{ $type === 'success' ? 'bg-comsoc-50 border-comsoc-200 text-comsoc-700' : 'bg-red-50 border-red-200 text-red-700' }}">
                    {{ session($type) }}
                </div>
            @endif
        @endforeach

        <div class="flex items-center justify-between">
            <p class="text-sm text-gray-500">{{ $events->total() }} event{{ $events->total() !== 1 ? 's' : '' }}</p>
            <div class="flex items-center gap-2">
                @if(Auth::user()->isAdviser() || Auth::user()->isAdmin())
                    @php
                        $pendingCount = \App\Models\Event::whereIn('status', [\App\Models\Event::STATUS_SUBMITTED, \App\Models\Event::STATUS_UNDER_REVIEW])->count();
                    @endphp
                    <a href="{{ route('events.approval.index') }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        Event Approvals
                        @if($pendingCount > 0)
                            <span class="px-1.5 py-0.5 text-xs bg-white text-amber-800 font-black rounded-full">
                                {{ $pendingCount }}
                            </span>
                        @endif
                    </a>
                @endif
                <a href="{{ route('events.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-comsoc-600 text-white text-sm font-semibold rounded-lg hover:bg-comsoc-700 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Create Event
                </a>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            @foreach(['Event','Date','Venue','Status','Registered',''] as $col)
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ $col }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($events as $event)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-4 max-w-xs">
                                    <div class="flex items-center gap-3">
                                        @if($event->getImageUrl())
                                            <img src="{{ $event->getImageUrl() }}" class="w-10 h-10 rounded-lg object-cover shrink-0"/>
                                        @else
                                            <div class="w-10 h-10 rounded-lg bg-comsoc-100 flex items-center justify-center shrink-0">
                                                <svg class="w-5 h-5 text-comsoc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $event->title }}</p>
                                            <p class="text-xs text-gray-400">By {{ $event->creator->name }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-600 whitespace-nowrap">
                                    {{ $event->event_date->format('M d, Y') }}
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-600 max-w-[140px] truncate">
                                    {{ $event->venue }}
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $event->getStatusColor() }}">
                                        {{ $event->getStatusLabel() }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-600">
                                    {{ $event->registeredCount() }}
                                    @if($event->participant_limit)
                                        / {{ $event->participant_limit }}
                                    @endif
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        @can('publish', $event)
                                            <form method="POST" action="{{ route('events.publish', $event) }}">
                                                @csrf
                                                <button type="submit"
                                                        class="px-2.5 py-1 bg-comsoc-600 hover:bg-comsoc-700 text-white text-xs font-bold rounded-lg transition shadow-xs">
                                                    Publish
                                                </button>
                                            </form>
                                        @endcan
                                        @can('submit', $event)
                                            <form method="POST" action="{{ route('events.submit', $event) }}">
                                                @csrf
                                                <button type="submit"
                                                        class="px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg transition shadow-xs">
                                                    Submit
                                                </button>
                                            </form>
                                        @endcan
                                        <a href="{{ route('events.show', $event) }}"
                                           class="text-xs font-semibold text-comsoc-600 hover:text-comsoc-800 transition-colors whitespace-nowrap">
                                            View →
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center text-gray-400 text-sm">
                                    No events yet. <a href="{{ route('events.create') }}" class="text-comsoc-600 font-medium hover:underline">Create your first event.</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div>{{ $events->links() }}</div>
    </div>
</x-dashboard-layout>
