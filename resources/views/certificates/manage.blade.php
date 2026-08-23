<x-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('events.show', $event) }}" class="hover:text-gray-900 transition-colors">{{ $event->title }}</a>
            <span>/</span>
            <span class="text-gray-800 font-semibold">Certificates</span>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-5">

        @foreach(['success','error'] as $type)
            @if(session($type))
                <div x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,3500)" x-transition
                     class="p-4 rounded-xl text-sm font-medium border
                            {{ $type==='success' ? 'bg-comsoc-50 border-comsoc-200 text-comsoc-700' : 'bg-red-50 border-red-200 text-red-700' }}">
                    {{ session($type) }}
                </div>
            @endif
        @endforeach

        {{-- Summary --}}
        <div class="grid grid-cols-3 gap-4">
            @php
                $totalAttended = $attendance->count();
                $totalIssued   = count($issuedCertificates);
                $pending       = $totalAttended - $totalIssued;
            @endphp
            @foreach([
                ['Attended',            $totalAttended, 'text-blue-600 bg-blue-50 border-blue-100'],
                ['Certificates Issued', $totalIssued,   'text-comsoc-600 bg-comsoc-50 border-comsoc-100'],
                ['Pending',             $pending,        'text-orange-600 bg-orange-50 border-orange-100'],
            ] as [$label, $value, $style])
                <div class="bg-white border rounded-xl p-4 text-center shadow-sm {{ $style }}">
                    <p class="text-2xl font-black">{{ $value }}</p>
                    <p class="text-xs font-semibold uppercase tracking-wide mt-0.5 opacity-70">{{ $label }}</p>
                </div>
            @endforeach
        </div>

        {{-- Issue all button --}}
        @if($pending > 0)
            <div class="flex items-center justify-between bg-yellow-50 border border-yellow-200 rounded-xl px-5 py-4">
                <div>
                    <p class="text-sm font-bold text-yellow-800">{{ $pending }} attendee(s) haven't received certificates yet.</p>
                    <p class="text-xs text-yellow-600 mt-0.5">Issue certificates to all eligible attendees at once.</p>
                </div>
                <form method="POST" action="{{ route('certificates.issue-all', $event) }}">
                    @csrf
                    <button type="submit"
                            onclick="return confirm('Issue certificates to all {{ $pending }} eligible attendees?')"
                            class="px-5 py-2.5 bg-comsoc-700 text-white text-sm font-bold rounded-lg hover:bg-comsoc-800 transition-colors shadow-sm shrink-0 ml-4">
                        Issue All ({{ $pending }})
                    </button>
                </form>
            </div>
        @else
            <div class="flex items-center gap-3 bg-comsoc-50 border border-comsoc-200 rounded-xl px-5 py-4">
                <svg class="w-5 h-5 text-comsoc-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <p class="text-sm font-semibold text-comsoc-700">All attendees have received their certificates.</p>
            </div>
        @endif

        {{-- Attendee table --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-800">Attendees — {{ $event->title }}</h3>
                <span class="text-xs text-gray-400">{{ $attendance->count() }} total</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            @foreach(['Student','Program','Check-in','Certificate','Action'] as $col)
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ $col }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($attendance as $record)
                            @php $hasCert = in_array($record->user_id, $issuedCertificates); @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2.5">
                                        <img src="{{ $record->user->getProfilePictureUrl() }}"
                                             class="w-8 h-8 rounded-full object-cover border border-gray-200 shrink-0"/>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">{{ $record->user->name }}</p>
                                            <p class="text-xs text-gray-400">{{ $record->user->student_id ?? $record->user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $record->user->program?->code ?? '—' }}</td>
                                <td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap">
                                    {{ $record->checked_in_at->format('g:i A') }}
                                    <span class="ml-1 px-1.5 py-0.5 rounded text-[10px] font-semibold
                                                 {{ $record->attendance_status === 'present' ? 'bg-comsoc-100 text-comsoc-700' : 'bg-orange-100 text-orange-700' }}">
                                        {{ ucfirst($record->attendance_status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @if($hasCert)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-comsoc-100 text-comsoc-700">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Issued
                                        </span>
                                    @else
                                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">
                                            Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if(!$hasCert)
                                        <form method="POST" action="{{ route('certificates.issue', $event) }}">
                                            @csrf
                                            <input type="hidden" name="user_id" value="{{ $record->user_id }}">
                                            <button type="submit"
                                                    class="text-xs font-semibold text-comsoc-600 hover:text-comsoc-800 transition-colors">
                                                Issue →
                                            </button>
                                        </form>
                                    @else
                                        @php
                                            $cert = \App\Models\Certificate::where('event_id', $event->id)
                                                ->where('user_id', $record->user_id)->first();
                                        @endphp
                                        @if($cert)
                                            <div class="flex items-center gap-3">
                                                <a href="{{ route('certificates.show', $cert) }}"
                                                   target="_blank"
                                                   class="text-xs font-semibold text-comsoc-600 hover:text-comsoc-800 transition-colors">
                                                    View
                                                </a>
                                                <form method="POST" action="{{ route('certificates.revoke', $cert) }}">
                                                    @csrf
                                                    <button type="submit"
                                                            onclick="return confirm('Revoke this certificate?')"
                                                            class="text-xs font-semibold text-red-500 hover:text-red-700 transition-colors">
                                                        Revoke
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-gray-400 text-sm">
                                    No attendees found. Run the QR scanner first.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-dashboard-layout>
