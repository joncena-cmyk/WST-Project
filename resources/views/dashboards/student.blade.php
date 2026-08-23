<x-dashboard-layout>
    <x-slot name="header">
        <h1 class="text-base font-bold text-gray-900">Student Dashboard</h1>
    </x-slot>

    <div class="space-y-6">

        {{-- ===== HERO WELCOME BANNER ===== --}}
        <div class="relative rounded-2xl overflow-hidden shadow-md" style="min-height:160px;">
            <div class="absolute inset-0">
                <img src="{{ asset('images/hero_logo.jpg') }}" alt="" class="w-full h-full object-cover"/>
            </div>
            <div class="absolute inset-0" style="background:linear-gradient(135deg,rgba(5,46,22,0.92) 0%,rgba(5,46,22,0.75) 50%,rgba(5,46,22,0.50) 100%)"></div>
            <div class="absolute inset-0" style="background-image:linear-gradient(rgba(255,255,255,0.025) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.025) 1px,transparent 1px);background-size:32px 32px"></div>
            <div class="absolute top-0 left-0 right-0 h-0.5 bg-gradient-to-r from-gold-600 via-gold-400 to-gold-600"></div>

            <div class="relative z-10 flex items-center gap-5 p-6">
                <div class="relative shrink-0">
                    <img src="{{ $user->getProfilePictureUrl() }}"
                         alt="{{ $user->name }}"
                         class="w-16 h-16 rounded-full object-cover border-2 border-gold-400 shadow-lg"/>
                    <span class="absolute bottom-0 right-0 w-4 h-4 rounded-full bg-green-400 border-2 border-comsoc-900 shadow"></span>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-gold-400 uppercase tracking-widest mb-0.5">Welcome back</p>
                    <h2 class="text-xl sm:text-2xl font-black text-white truncate">{{ $user->name }}</h2>
                    <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-comsoc-700/80 border border-comsoc-600/50 text-white uppercase tracking-wide">
                            {{ $user->getRoleLabel() }}
                        </span>
                        @if($user->program)
                            <span class="text-comsoc-300 text-xs font-semibold">{{ $user->program->code }}</span>
                        @endif
                        @if($user->student_id)
                            <span class="text-comsoc-400 text-xs">· {{ $user->student_id }}</span>
                        @endif
                    </div>
                </div>
                <div class="ml-auto shrink-0 hidden sm:block opacity-20">
                    <img src="{{ asset('images/comsoc_logo.png') }}" alt="" class="w-16 h-16 object-contain"/>
                </div>
            </div>
        </div>

        {{-- ===== QUICK METRIC STATS ===== --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
            <div class="bg-white border border-blue-100 rounded-2xl p-4 shadow-xs flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Registered</p>
                    <p class="text-xl font-black text-gray-900 mt-0.5">{{ $myRegistrations->count() }} Events</p>
                </div>
            </div>

            <div class="bg-white border border-green-100 rounded-2xl p-4 shadow-xs flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-green-50 text-green-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Attended</p>
                    <p class="text-xl font-black text-gray-900 mt-0.5">{{ $attendedCount }} Check-ins</p>
                </div>
            </div>

            <div class="bg-white border border-amber-100 rounded-2xl p-4 shadow-xs flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Certificates</p>
                    <p class="text-xl font-black text-gray-900 mt-0.5">{{ $certificates->count() }} Issued</p>
                </div>
            </div>

            <div class="bg-white border {{ $activeMembership ? 'border-comsoc-100 bg-comsoc-50/30' : 'border-gray-200' }} rounded-2xl p-4 shadow-xs flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl {{ $activeMembership ? 'bg-comsoc-100 text-comsoc-700' : 'bg-gray-100 text-gray-400' }} flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Membership</p>
                    <p class="text-sm font-black {{ $activeMembership ? 'text-comsoc-800' : 'text-gray-500' }} mt-0.5">
                        {{ $activeMembership ? 'Active Member' : 'Not Enrolled' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- ===== ACTIVE DIGITAL QR PASSES ===== --}}
        @if($myRegistrations->isNotEmpty())
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-gold-400 animate-pulse"></span>
                        <h3 class="font-bold text-gray-900 text-base">Your Active Event QR Passes</h3>
                    </div>
                    <a href="{{ route('events.my-events') }}" class="text-xs font-bold text-comsoc-600 hover:text-comsoc-800 transition">
                        View All Registrations →
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($myRegistrations as $reg)
                        <div class="border border-gray-200 bg-gradient-to-br from-white to-gray-50/50 rounded-xl p-4 flex flex-col justify-between shadow-xs hover:border-gold-400 transition">
                            <div>
                                <div class="flex items-start justify-between gap-2 mb-1.5">
                                    <h4 class="font-bold text-gray-900 text-sm leading-tight">{{ $reg->event->title }}</h4>
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full {{ $reg->event->getStatusColor() }} shrink-0">
                                        {{ $reg->event->getStatusLabel() }}
                                    </span>
                                </div>
                                <div class="text-xs text-gray-500 space-y-1 mt-2">
                                    <p class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        {{ $reg->event->event_date->format('M d, Y') }} · {{ \Carbon\Carbon::parse($reg->event->start_time)->format('g:i A') }}
                                    </p>
                                    <p class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        {{ $reg->event->venue }}
                                    </p>
                                </div>
                            </div>
                            <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between">
                                <span class="text-[11px] text-gray-400 font-mono">Token: {{ Str::limit($reg->qr_code, 10) }}</span>
                                <a href="{{ route('attendance.pass', $reg->event) }}"
                                   class="px-3.5 py-1.5 bg-gold-400 hover:bg-gold-500 text-comsoc-950 text-xs font-black rounded-lg transition shadow-2xs flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                    </svg>
                                    Open QR Pass
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- ===== TWO-COLUMN SECTION: Upcoming Events & Certificates ===== --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            {{-- Upcoming Events --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-gray-900 text-base flex items-center gap-2">
                        <svg class="w-4 h-4 text-comsoc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Upcoming Events
                    </h3>
                    <a href="{{ route('events.index') }}" class="text-xs font-bold text-comsoc-600 hover:text-comsoc-800 transition">
                        Browse All →
                    </a>
                </div>

                <div class="space-y-3">
                    @forelse($upcomingEvents as $evt)
                        <div class="p-3.5 rounded-xl border border-gray-100 hover:border-comsoc-200 bg-gray-50/50 flex items-center justify-between gap-3 transition">
                            <div class="min-w-0">
                                <h4 class="font-bold text-gray-900 text-sm truncate">{{ $evt->title }}</h4>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    {{ $evt->event_date->format('M d, Y') }} · {{ \Carbon\Carbon::parse($evt->start_time)->format('g:i A') }} · {{ $evt->venue }}
                                </p>
                            </div>
                            <a href="{{ route('events.show', $evt) }}"
                               class="px-3 py-1.5 bg-comsoc-600 hover:bg-comsoc-700 text-white text-xs font-bold rounded-lg transition shrink-0">
                                View Event
                            </a>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 italic text-center py-4">No upcoming events scheduled at the moment.</p>
                    @endforelse
                </div>
            </div>

            {{-- Earned Certificates --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-gray-900 text-base flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138z"/>
                        </svg>
                        My Certificates
                    </h3>
                    <a href="{{ route('certificates.index') }}" class="text-xs font-bold text-comsoc-600 hover:text-comsoc-800 transition">
                        View All →
                    </a>
                </div>

                <div class="space-y-3">
                    @forelse($certificates as $cert)
                        <div class="p-3.5 rounded-xl border border-amber-100 bg-amber-50/30 flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <h4 class="font-bold text-gray-900 text-sm truncate">{{ $cert->event->title }}</h4>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    Code: <code class="font-mono text-[11px] font-bold text-comsoc-800">{{ $cert->certificate_code }}</code> · {{ $cert->issued_at ? $cert->issued_at->format('M d, Y') : '' }}
                                </p>
                            </div>
                            <a href="{{ route('certificates.show', $cert) }}"
                               class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-lg transition shrink-0">
                                Download
                            </a>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 italic text-center py-4">Attend COMSOC events to earn certified certificates of participation.</p>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</x-dashboard-layout>
