<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Certificate — {{ $certificate->event->title }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            .cert-page { box-shadow: none !important; border: 2px solid #166534 !important; }
        }
        body { background: #f3f4f6; }
    </style>
</head>
<body class="font-sans min-h-screen flex flex-col items-center justify-start py-8 px-4">

    {{-- Toolbar (no-print) --}}
    <div class="no-print flex items-center gap-3 mb-6 w-full max-w-3xl">
        <a href="{{ route('certificates.index') }}"
           class="flex items-center gap-1.5 text-sm text-gray-600 hover:text-gray-900 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to certificates
        </a>
        <div class="ml-auto flex items-center gap-2">
            <button onclick="window.print()"
                    class="inline-flex items-center gap-2 px-5 py-2 bg-comsoc-700 text-white text-sm font-semibold rounded-lg hover:bg-comsoc-800 transition-colors shadow">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print / Save PDF
            </button>
        </div>
    </div>

    {{-- Certificate --}}
    <div class="cert-page w-full max-w-3xl bg-white rounded-2xl shadow-xl overflow-hidden"
         style="min-height: 500px;">

        {{-- Top border accent --}}
        <div class="h-2 bg-gradient-to-r from-comsoc-800 via-gold-400 to-comsoc-800"></div>

        <div class="px-10 py-10">
            {{-- Header --}}
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/comsoc_logo.png') }}" alt="COMSOC" class="w-14 h-14 object-contain"/>
                    <div>
                        <p class="font-black text-comsoc-900 text-base leading-tight">COMPUTER SOCIETY</p>
                        <p class="text-comsoc-600 text-xs font-semibold">CCIS · University of Makati</p>
                    </div>
                </div>
                <div class="text-right flex items-center gap-3">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Certificate No.</p>
                        <p class="font-mono text-sm font-bold text-comsoc-800">{{ $certificate->certificate_number }}</p>
                        <p class="text-[9px] text-gray-400 mt-0.5">Scan QR to authenticate</p>
                    </div>
                    <div class="p-1.5 bg-white border border-gray-200 rounded-xl shadow-xs">
                        <img src="{{ (new \chillerlan\QRCode\QRCode)->render(route('certificates.verify', $certificate->certificate_number)) }}"
                             alt="Verification QR" class="w-14 h-14"/>
                    </div>
                </div>
            </div>

            {{-- Certificate body --}}
            <div class="text-center py-4">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-[0.3em] mb-4">Certificate of Participation</p>

                <p class="text-sm text-gray-500 mb-2">This is to certify that</p>

                <h1 class="text-4xl font-black text-comsoc-900 mb-1"
                    style="font-family: Georgia, serif;">
                    {{ $certificate->user->name }}
                </h1>

                @if($certificate->user->student_id)
                    <p class="text-sm text-gray-400 font-medium mb-4">{{ $certificate->user->student_id }}
                        @if($certificate->user->program)
                            · {{ $certificate->user->program->short_name }}
                        @endif
                    </p>
                @endif

                <p class="text-sm text-gray-500 mb-2">has successfully participated in</p>

                <h2 class="text-2xl font-black text-comsoc-800 mb-2">{{ $certificate->event->title }}</h2>

                <p class="text-sm text-gray-500">
                    held on <strong>{{ $certificate->event->event_date->format('F d, Y') }}</strong>
                    at <strong>{{ $certificate->event->venue }}</strong>
                </p>
            </div>

            {{-- Decorative divider --}}
            <div class="flex items-center gap-4 my-8">
                <div class="flex-1 h-px bg-gradient-to-r from-transparent via-comsoc-200 to-transparent"></div>
                <div class="w-8 h-8 rounded-full border-2 border-gold-400 flex items-center justify-center">
                    <div class="w-3 h-3 rounded-full bg-gold-400"></div>
                </div>
                <div class="flex-1 h-px bg-gradient-to-r from-transparent via-comsoc-200 to-transparent"></div>
            </div>

            {{-- Footer signature area --}}
            <div class="grid grid-cols-2 gap-8">
                <div class="text-center">
                    <div class="h-12 border-b-2 border-gray-300 mb-2"></div>
                    <p class="text-xs font-bold text-gray-700">{{ $certificate->issuedBy->name }}</p>
                    <p class="text-[10px] text-gray-400 uppercase tracking-wide">{{ $certificate->issuedBy->getRoleLabel() }}</p>
                    <p class="text-[10px] text-gray-400">Issued {{ $certificate->issued_at->format('M d, Y') }}</p>
                </div>
                <div class="text-center">
                    <div class="h-12 border-b-2 border-gray-300 mb-2"></div>
                    <p class="text-xs font-bold text-gray-700">COMSOC Adviser</p>
                    <p class="text-[10px] text-gray-400 uppercase tracking-wide">College of Computing & IS</p>
                    <p class="text-[10px] text-gray-400">University of Makati</p>
                </div>
            </div>
        </div>

        {{-- Bottom border accent --}}
        <div class="h-2 bg-gradient-to-r from-comsoc-800 via-gold-400 to-comsoc-800"></div>
    </div>

    {{-- Verification note --}}
    <p class="no-print mt-4 text-xs text-gray-400 text-center">
        Certificate <span class="font-mono font-bold text-gray-600">{{ $certificate->certificate_number }}</span> · Issued by COMSOC Hub · 
        <a href="{{ route('certificates.verify', $certificate->certificate_number) }}" target="_blank" class="text-comsoc-600 hover:text-comsoc-800 underline font-semibold">
            Verify authenticity online →
        </a>
    </p>

</body>
</html>
