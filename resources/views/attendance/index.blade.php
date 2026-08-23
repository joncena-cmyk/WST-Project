<x-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('events.show', $event) }}" class="hover:text-gray-900 transition-colors">{{ $event->title }}</a>
            <span>/</span>
            <span class="text-gray-800 font-semibold">Attendance</span>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-5">

        {{-- Summary cards --}}
        <div class="grid grid-cols-3 gap-4">
            @foreach([
                ['Registered', $registeredCount, 'bg-blue-50 text-blue-700 border-blue-100'],
                ['Attended',   $attendedCount,   'bg-comsoc-50 text-comsoc-700 border-comsoc-100'],
                ['Absent',     $absentCount,     'bg-red-50 text-red-600 border-red-100'],
            ] as [$label, $value, $style])
                <div class="bg-white border rounded-xl p-5 shadow-sm text-center {{ $style }}">
                    <p class="text-3xl font-black">{{ $value }}</p>
                    <p class="text-xs font-bold uppercase tracking-wide mt-1 opacity-70">{{ $label }}</p>
                </div>
            @endforeach
        </div>

        {{-- Attendance rate --}}
        @if($registeredCount > 0)
            @php $rate = round(($attendedCount / $registeredCount) * 100); @endphp
            <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-sm font-semibold text-gray-700">Attendance Rate</p>
                    <span class="text-lg font-black text-comsoc-700">{{ $rate }}%</span>
                </div>
                <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-comsoc-600 rounded-full transition-all duration-500"
                         style="width: {{ $rate }}%"></div>
                </div>
            </div>
        @endif

        {{-- Actions --}}
        <div class="flex items-center justify-between">
            <p class="text-sm text-gray-500">{{ $attendance->count() }} check-ins recorded</p>
            <a href="{{ route('attendance.scanner', $event) }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-comsoc-700 text-white text-sm font-semibold rounded-lg hover:bg-comsoc-800 transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                </svg>
                Open Scanner
            </a>
        </div>

        {{-- Attendance table --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            @foreach(['#','Student','Student ID','Program','Status','Check-in Time','Recorded By'] as $col)
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ $col }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($attendance as $i => $record)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 text-xs text-gray-400">{{ $i + 1 }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2.5">
                                        <img src="{{ $record->user->getProfilePictureUrl() }}"
                                             class="w-8 h-8 rounded-full object-cover border border-gray-200 shrink-0"/>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">{{ $record->user->name }}</p>
                                            <p class="text-xs text-gray-400">{{ $record->user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 font-mono">{{ $record->user->student_id ?? '—' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $record->user->program?->code ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold
                                                 {{ $record->attendance_status === 'present' ? 'bg-comsoc-100 text-comsoc-700' : 'bg-orange-100 text-orange-700' }}">
                                        {{ ucfirst($record->attendance_status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap">
                                    {{ $record->checked_in_at->format('g:i A') }}
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-500">{{ $record->recordedBy->name }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center text-gray-400 text-sm">
                                    No check-ins recorded yet.
                                    <a href="{{ route('attendance.scanner', $event) }}" class="text-comsoc-600 font-medium hover:underline ml-1">Open the scanner →</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-dashboard-layout>
