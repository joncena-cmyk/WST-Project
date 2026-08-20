<x-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('events.show', $event) }}" class="hover:text-gray-900">{{ $event->title }}</a>
            <span>/</span>
            <span class="text-gray-800 font-semibold">Participants</span>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-5">

        {{-- Summary --}}
        <div class="grid grid-cols-3 gap-4">
            @php
                $registered = $registrations->where('status','registered')->count();
                $attended   = $registrations->where('status','attended')->count();
                $cancelled  = $registrations->where('status','cancelled')->count();
            @endphp
            @foreach([['Registered',$registered,'text-comsoc-600'],['Attended',$attended,'text-blue-600'],['Cancelled',$cancelled,'text-red-500']] as [$l,$v,$c])
                <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm text-center">
                    <p class="text-xs text-gray-500 font-medium">{{ $l }}</p>
                    <p class="text-2xl font-black {{ $c }} mt-1">{{ $v }}</p>
                </div>
            @endforeach
        </div>

        {{-- Table --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-800">Participant List</h3>
                <p class="text-xs text-gray-400">{{ $registrations->count() }} total</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            @foreach(['#','Participant','Student ID','Program','Status','Registered At'] as $col)
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ $col }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($registrations as $i => $reg)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 text-xs text-gray-400">{{ $i + 1 }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2.5">
                                        <img src="{{ $reg->user->getProfilePictureUrl() }}"
                                             class="w-8 h-8 rounded-full object-cover border border-gray-200 shrink-0"/>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">{{ $reg->user->name }}</p>
                                            <p class="text-xs text-gray-400">{{ $reg->user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $reg->user->student_id ?? '—' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $reg->user->program?->code ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    @php $sc = ['registered'=>'bg-comsoc-100 text-comsoc-700','attended'=>'bg-blue-100 text-blue-700','cancelled'=>'bg-red-100 text-red-600']; @endphp
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $sc[$reg->status] ?? 'bg-gray-100' }}">
                                        {{ ucfirst($reg->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-400">{{ $reg->created_at->format('M d, Y g:i A') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center text-gray-400 text-sm">No participants yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-dashboard-layout>
