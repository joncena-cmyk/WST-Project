<x-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('events.my-events') }}" class="hover:text-gray-900 transition-colors">My Events</a>
            <span>/</span>
            <span class="text-gray-800 font-semibold">QR Pass</span>
        </div>
    </x-slot>

    <div class="max-w-sm mx-auto">

        @if($alreadyCheckedIn)
            <div class="mb-5 flex items-center gap-3 p-4 bg-comsoc-50 border border-comsoc-200 rounded-xl">
                <svg class="w-5 h-5 text-comsoc-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <p class="text-sm font-semibold text-comsoc-700">You have already checked in for this event.</p>
            </div>
        @endif

        {{-- Pass Card --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-lg overflow-hidden">
            {{-- Header strip --}}
            <div class="relative bg-gradient-to-br from-comsoc-800 to-comsoc-950 px-6 py-5">
                <div class="absolute inset-0" style="background-image:linear-gradient(rgba(255,255,255,0.03) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.03) 1px,transparent 1px);background-size:24px 24px"></div>
                <div class="absolute top-0 left-0 right-0 h-0.5 bg-gradient-to-r from-gold-600 via-gold-400 to-gold-600"></div>
                <div class="relative z-10">
                    <div class="flex items-center gap-2 mb-3">
                        <img src="{{ asset('images/comsoc_logo.png') }}" alt="" class="w-7 h-7 object-contain opacity-90"/>
                        <span class="text-xs font-bold text-comsoc-300 uppercase tracking-widest">COMSOC Hub · Event Pass</span>
                    </div>
                    <h2 class="text-xl font-black text-white leading-tight">{{ $event->title }}</h2>
                    <div class="flex flex-wrap gap-3 mt-2 text-xs text-comsoc-300">
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ $event->event_date->format('M d, Y') }}
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ \Carbon\Carbon::parse($event->start_time)->format('g:i A') }}
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {{ $event->venue }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Dashed divider (ticket tear) --}}
            <div class="flex items-center px-4">
                <div class="w-5 h-5 rounded-full bg-gray-50 border border-gray-200 -ml-7 shrink-0"></div>
                <div class="flex-1 border-t-2 border-dashed border-gray-200 mx-2"></div>
                <div class="w-5 h-5 rounded-full bg-gray-50 border border-gray-200 -mr-7 shrink-0"></div>
            </div>

            {{-- QR code + attendee info --}}
            <div class="px-6 py-6 flex flex-col items-center">
                {{-- QR Code --}}
                <div class="p-4 bg-white border-2 border-gray-100 rounded-2xl shadow-sm mb-5">
                    <div id="qrcode"></div>
                </div>

                {{-- Attendee info --}}
                <div class="w-full text-center">
                    <img src="{{ Auth::user()->getProfilePictureUrl() }}"
                         class="w-12 h-12 rounded-full object-cover border-2 border-comsoc-200 mx-auto mb-2"/>
                    <p class="font-black text-gray-900 text-base">{{ Auth::user()->name }}</p>
                    @if(Auth::user()->student_id)
                        <p class="text-xs text-gray-500 mt-0.5">{{ Auth::user()->student_id }}</p>
                    @endif
                    @if(Auth::user()->program)
                        <span class="inline-flex mt-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-comsoc-100 text-comsoc-700">
                            {{ Auth::user()->program->code }}
                        </span>
                    @endif
                </div>

                {{-- Token (fallback) --}}
                <div class="mt-5 w-full">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center mb-1">QR Token (manual fallback)</p>
                    <div class="bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-center">
                        <code class="text-xs font-mono text-gray-600 break-all">{{ $registration->qr_code }}</code>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 text-center">
                <p class="text-[10px] text-gray-400">
                    Present this QR code to the officer at the venue entrance.
                    Registered {{ $registration->created_at->format('M d, Y') }}.
                </p>
            </div>
        </div>

        <div class="mt-5 text-center">
            <a href="{{ route('events.my-events') }}"
               class="text-sm text-comsoc-600 hover:text-comsoc-800 font-medium transition-colors">
                ← Back to my events
            </a>
        </div>
    </div>

    {{-- QR Code generator (qrcode.js via CDN) --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        new QRCode(document.getElementById("qrcode"), {
            text: "{{ $registration->qr_code }}",
            width: 200,
            height: 200,
            colorDark: "#14532d",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
    </script>
</x-dashboard-layout>
