<x-dashboard-layout>
    <x-slot name="header">
        <h1 class="text-base font-bold text-gray-900">My Certificates</h1>
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-5">

        @if(session('success'))
            <div x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,3500)" x-transition
                 class="p-4 bg-comsoc-50 border border-comsoc-200 rounded-xl text-sm text-comsoc-700 font-medium">
                {{ session('success') }}
            </div>
        @endif

        @forelse($certificates as $cert)
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                <div class="flex items-center gap-4 p-5">
                    {{-- Certificate icon --}}
                    <div class="flex items-center justify-center w-14 h-14 rounded-xl bg-gradient-to-br from-gold-500 to-yellow-600 shrink-0 shadow">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                  d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                        </svg>
                    </div>
                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <h3 class="font-bold text-gray-900 truncate">{{ $cert->event->title }}</h3>
                        <p class="text-xs text-gray-500 mt-0.5">
                            {{ $cert->event->event_date->format('M d, Y') }}
                            · Issued {{ $cert->issued_at->format('M d, Y') }}
                        </p>
                        <p class="text-[10px] font-mono text-comsoc-500 mt-1">{{ $cert->certificate_number }}</p>
                    </div>
                    {{-- Actions --}}
                    <div class="shrink-0 flex items-center gap-2">
                        <a href="{{ route('certificates.show', $cert) }}"
                           class="inline-flex items-center gap-1.5 px-4 py-2 bg-comsoc-700 text-white text-xs font-semibold rounded-lg hover:bg-comsoc-800 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            View
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white border border-gray-200 rounded-xl p-12 text-center shadow-sm">
                <div class="flex items-center justify-center w-16 h-16 rounded-full bg-yellow-50 border border-yellow-100 mx-auto mb-4">
                    <svg class="w-8 h-8 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </div>
                <p class="text-gray-700 font-bold">No certificates yet</p>
                <p class="text-gray-400 text-sm mt-1 max-w-xs mx-auto">
                    Attend COMSOC events and certificates will be issued here once an officer approves them.
                </p>
                <a href="{{ route('events.index') }}"
                   class="mt-4 inline-flex items-center px-4 py-2 bg-comsoc-700 text-white text-sm font-semibold rounded-lg hover:bg-comsoc-800 transition-colors">
                    Browse Events
                </a>
            </div>
        @endforelse
    </div>
</x-dashboard-layout>
