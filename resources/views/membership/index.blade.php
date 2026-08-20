<x-dashboard-layout>
    <x-slot name="header">
        <h1 class="text-lg font-semibold text-gray-800">My Membership</h1>
    </x-slot>

    <div class="max-w-2xl mx-auto space-y-5">

        {{-- Active membership card --}}
        @if ($active)
            <div class="bg-gradient-to-br from-comsoc-800 to-comsoc-900 rounded-2xl p-6 text-white shadow-lg relative overflow-hidden">
                {{-- Decorative ring --}}
                <div class="absolute -top-10 -right-10 w-40 h-40 rounded-full border-2 border-white/10 pointer-events-none"></div>
                <div class="absolute -bottom-6 -left-6 w-28 h-28 rounded-full border-2 border-white/5 pointer-events-none"></div>

                <div class="relative z-10">
                    <div class="flex items-start justify-between mb-5">
                        <div>
                            <p class="text-xs text-comsoc-300 font-semibold uppercase tracking-widest mb-1">COMSOC Member</p>
                            <h2 class="text-xl font-black">{{ Auth::user()->name }}</h2>
                            @if(Auth::user()->student_id)
                                <p class="text-comsoc-300 text-sm mt-0.5">{{ Auth::user()->student_id }}</p>
                            @endif
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-green-500/20 border border-green-400/30 text-green-300 text-xs font-bold">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse"></span>
                            Active
                        </span>
                    </div>
                    <div class="grid grid-cols-3 gap-4 border-t border-white/10 pt-4">
                        <div>
                            <p class="text-xs text-comsoc-400 font-medium">Academic Year</p>
                            <p class="text-sm font-bold mt-0.5">{{ $active->academic_year }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-comsoc-400 font-medium">Program</p>
                            <p class="text-sm font-bold mt-0.5">{{ $active->program?->code ?? Auth::user()->program?->code ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-comsoc-400 font-medium">Valid Until</p>
                            <p class="text-sm font-bold mt-0.5">{{ $active->end_date?->format('M d, Y') ?? 'End of year' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-white border border-gray-200 rounded-xl p-8 text-center shadow-sm">
                <div class="flex items-center justify-center w-16 h-16 rounded-full bg-yellow-50 border border-yellow-200 mx-auto mb-4">
                    <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-gray-800 font-bold">No active membership</h3>
                <p class="text-gray-500 text-sm mt-2 max-w-sm mx-auto">
                    You don't have an active COMSOC membership for the current academic year.
                    Contact a COMSOC officer to get your membership registered.
                </p>
            </div>
        @endif

        {{-- Membership history --}}
        @if ($memberships->isNotEmpty())
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="text-sm font-bold text-gray-800">Membership History</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach ($memberships as $m)
                        <div class="flex items-center justify-between px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex items-center justify-center w-10 h-10 rounded-xl
                                            {{ $m->status === 'active' ? 'bg-comsoc-50' : 'bg-gray-50' }} shrink-0">
                                    <svg class="w-5 h-5 {{ $m->status === 'active' ? 'text-comsoc-600' : 'text-gray-400' }}"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $m->academic_year }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        {{ $m->program?->code ?? '—' }}
                                        @if($m->start_date)
                                            · {{ $m->start_date->format('M Y') }}
                                            @if($m->end_date) – {{ $m->end_date->format('M Y') }} @endif
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                @php $mColors = ['active'=>'bg-green-100 text-green-700','expired'=>'bg-gray-100 text-gray-500','pending'=>'bg-yellow-100 text-yellow-700','suspended'=>'bg-red-100 text-red-700']; @endphp
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $mColors[$m->status] ?? 'bg-gray-100 text-gray-500' }}">
                                    {{ ucfirst($m->status) }}
                                </span>
                                @if($m->approvedBy)
                                    <p class="text-[11px] text-gray-400 mt-1">Approved by {{ $m->approvedBy->name }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-dashboard-layout>
