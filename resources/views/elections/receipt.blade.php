<x-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-base font-bold text-gray-900">Official Digital Voting Receipt</h1>
            <a href="{{ route('elections.show', $election) }}" class="text-xs font-bold text-comsoc-600 hover:text-comsoc-800 transition">
                ← Back to Election
            </a>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto space-y-6">

        {{-- ===== PRINTABLE RECEIPT CARD ===== --}}
        <div id="receipt-card" class="bg-white border-2 border-emerald-500/30 rounded-3xl p-8 sm:p-10 shadow-lg relative overflow-hidden space-y-6">
            
            {{-- Background Watermark / Accent --}}
            <div class="absolute -top-12 -right-12 w-48 h-48 bg-emerald-500/5 rounded-full blur-2xl pointer-events-none"></div>
            <div class="absolute -bottom-12 -left-12 w-48 h-48 bg-gold-500/5 rounded-full blur-2xl pointer-events-none"></div>
            <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-emerald-600 via-gold-400 to-comsoc-700"></div>

            {{-- Receipt Header --}}
            <div class="flex items-start justify-between gap-4 border-b border-gray-100 pb-6">
                <div class="flex items-center gap-3.5">
                    <img src="{{ asset('images/logo.png') }}" alt="COMSOC" class="w-12 h-12 object-contain"
                         onerror="this.src='{{ asset('images/hero_logo.jpg') }}'"/>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-emerald-700">Computer Society · University of Makati</p>
                        <h2 class="text-lg font-black text-gray-900">Official Electoral Proof of Vote</h2>
                        <p class="text-xs text-gray-400">Cryptographically Sealed Participation Record</p>
                    </div>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 flex items-center gap-1.5 shrink-0">
                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    Verified
                </span>
            </div>

            {{-- Key Details Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-gray-50/80 p-5 rounded-2xl border border-gray-100 text-xs">
                <div>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-0.5">Voter Name</span>
                    <p class="font-bold text-gray-900 text-sm">{{ $participation->user->name }}</p>
                    <p class="text-gray-500 text-[11px] mt-0.5">{{ $participation->user->student_id ?? 'Student Member' }}</p>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-0.5">Receipt Serial Code</span>
                    <p class="font-mono font-black text-emerald-700 text-sm tracking-wider">{{ $participation->receipt_code ?? 'VOTE-' . $participation->id }}</p>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-0.5">Election Event</span>
                    <p class="font-bold text-gray-800">{{ $election->title }}</p>
                    <p class="text-gray-500 text-[11px] mt-0.5">AY {{ $election->academic_year }}</p>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-0.5">Recorded Timestamp</span>
                    <p class="font-semibold text-gray-800">{{ $participation->voted_at ? $participation->voted_at->format('M d, Y · h:i:s A') : 'Recorded' }}</p>
                    <p class="text-gray-400 text-[11px] mt-0.5">Asia/Manila (PHT)</p>
                </div>
            </div>

            {{-- Cryptographic Verification Checksum --}}
            @if($participation->verification_hash)
                <div class="space-y-1.5">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">SHA-256 Cryptographic Checksum</span>
                    <div class="p-3 bg-gray-900 text-emerald-400 rounded-xl font-mono text-[11px] break-all select-all leading-relaxed shadow-inner">
                        {{ $participation->verification_hash }}
                    </div>
                </div>
            @endif

            {{-- Confidentiality Disclaimer --}}
            <div class="p-4 bg-emerald-50/60 rounded-xl border border-emerald-100 flex items-start gap-3 text-xs text-emerald-900">
                <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                <div class="space-y-0.5">
                    <p class="font-bold">Secret Ballot Constitutional Protection</p>
                    <p class="text-emerald-800 text-[11px] leading-relaxed">
                        To preserve secret voting integrity, this receipt proves your participation without recording which candidate was chosen for any seat.
                    </p>
                </div>
            </div>

            {{-- Action Controls --}}
            <div class="flex items-center justify-between pt-4 border-t border-gray-100 print:hidden">
                <button type="button" onclick="window.print()"
                        class="px-5 py-2.5 bg-comsoc-700 hover:bg-comsoc-800 text-white text-xs font-bold rounded-xl transition shadow-xs flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Print / Save Receipt
                </button>
                <a href="{{ route('elections.show', $election) }}"
                   class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition">
                    Done
                </a>
            </div>

        </div>

    </div>

    {{-- Print Style Optimization --}}
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #receipt-card, #receipt-card * {
                visibility: visible;
            }
            #receipt-card {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                border: 1px solid #000;
                box-shadow: none !important;
            }
        }
    </style>
</x-dashboard-layout>
