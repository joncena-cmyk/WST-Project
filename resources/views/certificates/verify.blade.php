<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Certificate Verification — COMSOC Hub</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css'])
</head>
<body class="font-sans antialiased bg-gray-100 min-h-screen flex flex-col items-center justify-between p-4 sm:p-6">

    {{-- Top Brand Bar --}}
    <header class="w-full max-w-2xl flex items-center justify-between py-4">
        <a href="{{ url('/') }}" class="flex items-center gap-3 group">
            <div class="w-10 h-10 rounded-xl bg-comsoc-900 border border-gold-400/40 p-1 flex items-center justify-center shadow-md">
                <img src="{{ asset('images/comsoc_logo.png') }}" alt="COMSOC" class="w-full h-full object-contain"/>
            </div>
            <div>
                <span class="text-sm font-black text-comsoc-950 tracking-wide block leading-tight">COMSOC Hub</span>
                <span class="text-[10px] text-gray-500 font-semibold">CCIS · University of Makati</span>
            </div>
        </a>

        @auth
            <a href="{{ route('dashboard') }}" class="text-xs font-bold text-comsoc-700 hover:text-comsoc-900 bg-white px-3 py-1.5 rounded-xl border border-gray-200 shadow-2xs">
                Back to Dashboard →
            </a>
        @else
            <a href="{{ route('login') }}" class="text-xs font-bold text-comsoc-700 hover:text-comsoc-900 bg-white px-3 py-1.5 rounded-xl border border-gray-200 shadow-2xs">
                Sign In
            </a>
        @endauth
    </header>

    {{-- Main Verification Card --}}
    <main class="w-full max-w-2xl my-auto py-6">

        @if($certificate && $certificate->status === 'issued')
            {{-- ===== CASE 1: VERIFIED AUTHENTIC ===== --}}
            <div class="bg-white rounded-3xl border border-emerald-200 shadow-xl overflow-hidden">
                {{-- Header Strip --}}
                <div class="p-6 sm:p-8 bg-gradient-to-r from-emerald-800 via-emerald-700 to-comsoc-900 text-white text-center relative overflow-hidden">
                    <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#facc15 1px, transparent 1px); background-size: 14px 14px;"></div>

                    <div class="w-16 h-16 rounded-full bg-emerald-500/20 border-2 border-emerald-300 text-emerald-300 flex items-center justify-center mx-auto mb-3 shadow-inner">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>

                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-900/60 text-emerald-200 text-xs font-black uppercase tracking-widest border border-emerald-500/40 mb-2">
                        Official Academic Credential
                    </span>
                    <h1 class="text-2xl font-black text-white">Verified Authentic Certificate</h1>
                    <p class="text-xs text-emerald-100/80 mt-1">This certificate has been issued and verified in the official COMSOC Registry.</p>
                </div>

                {{-- Credential Details Table --}}
                <div class="p-6 sm:p-8 space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                            <span class="text-gray-400 font-bold uppercase tracking-wider block text-[10px]">Certificate Number</span>
                            <span class="text-base font-mono font-black text-comsoc-800 mt-0.5 block">{{ $certificate->certificate_number }}</span>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                            <span class="text-gray-400 font-bold uppercase tracking-wider block text-[10px]">Issue Date</span>
                            <span class="text-sm font-bold text-gray-900 mt-0.5 block">{{ $certificate->issued_at?->format('F d, Y') }}</span>
                        </div>
                    </div>

                    <div class="border-t border-b border-gray-100 py-5 space-y-4">
                        <div>
                            <span class="text-gray-400 font-bold uppercase tracking-wider text-[10px] block">Recipient</span>
                            <p class="text-lg font-black text-gray-900 mt-0.5">{{ $certificate->user->name }}</p>
                            <p class="text-xs text-gray-500">
                                {{ $certificate->user->program?->name ?? 'College of Computing and Information Sciences' }}
                                @if($certificate->user->student_id)
                                    · <span class="font-mono text-gray-400">{{ substr($certificate->user->student_id, 0, 4) }}****</span>
                                @endif
                            </p>
                        </div>

                        <div>
                            <span class="text-gray-400 font-bold uppercase tracking-wider text-[10px] block">Event Participated</span>
                            <p class="text-base font-bold text-comsoc-900 mt-0.5">{{ $certificate->event->title }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">
                                Held on <strong>{{ $certificate->event->event_date?->format('M d, Y') }}</strong> at {{ $certificate->event->venue }}
                            </p>
                        </div>

                        <div>
                            <span class="text-gray-400 font-bold uppercase tracking-wider text-[10px] block">Issued By</span>
                            <p class="text-xs font-bold text-gray-800 mt-0.5">{{ $certificate->issuedBy?->name ?? 'COMSOC Officer' }} ({{ $certificate->issuedBy?->getRoleLabel() ?? 'Executive Board' }})</p>
                        </div>
                    </div>

                    {{-- Security Guarantee --}}
                    <div class="flex items-center gap-3 p-3.5 bg-emerald-50/70 border border-emerald-200 rounded-2xl text-emerald-800 text-xs">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <p class="leading-relaxed">This record is permanently recorded on the CCIS Computer Society Hub ledger at University of Makati.</p>
                    </div>
                </div>
            </div>

        @elseif($certificate && $certificate->status === 'revoked')
            {{-- ===== CASE 2: REVOKED ===== --}}
            <div class="bg-white rounded-3xl border border-red-200 shadow-xl overflow-hidden">
                <div class="p-6 sm:p-8 bg-gradient-to-r from-red-800 to-red-900 text-white text-center">
                    <div class="w-16 h-16 rounded-full bg-red-700/50 border-2 border-red-400 text-red-200 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <span class="inline-flex px-3 py-1 rounded-full bg-red-950/70 text-red-200 text-xs font-black uppercase tracking-widest mb-2 border border-red-500/30">
                        Invalid Credential
                    </span>
                    <h1 class="text-2xl font-black text-white">Certificate Revoked</h1>
                    <p class="text-xs text-red-100 mt-1">This certificate has been formally revoked and is no longer valid.</p>
                </div>

                <div class="p-6 sm:p-8 space-y-4 text-xs">
                    <div class="bg-red-50 p-4 rounded-2xl border border-red-100">
                        <span class="text-red-700 font-bold uppercase tracking-wider block text-[10px]">Revocation Reason</span>
                        <p class="text-sm font-semibold text-red-900 mt-1">{{ $certificate->revocation_reason ?? 'Administrative revocation by student organization.' }}</p>
                        <p class="text-[11px] text-red-500 mt-1">Revoked on {{ $certificate->revoked_at?->format('M d, Y') }}</p>
                    </div>

                    <div class="pt-2 text-gray-500">
                        <p>Originally issued to <strong>{{ $certificate->user->name }}</strong> for <strong>{{ $certificate->event->title }}</strong>.</p>
                    </div>
                </div>
            </div>

        @else
            {{-- ===== CASE 3: NOT FOUND ===== --}}
            <div class="bg-white rounded-3xl border border-gray-200 shadow-xl p-8 text-center space-y-4">
                <div class="w-16 h-16 rounded-full bg-amber-50 border border-amber-200 text-amber-500 flex items-center justify-center mx-auto">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <h2 class="text-xl font-black text-gray-900">Certificate Not Found</h2>
                <p class="text-xs text-gray-500 max-w-sm mx-auto leading-relaxed">
                    No certificate matching serial number <strong class="font-mono text-gray-800">{{ $certificate_number }}</strong> was found in our verification registry.
                </p>

                <div class="pt-4 max-w-xs mx-auto">
                    <form onsubmit="event.preventDefault(); window.location.href = '{{ url('/verify-certificate') }}/' + encodeURIComponent(document.getElementById('certInput').value.trim());" class="space-y-2">
                        <input type="text" id="certInput" placeholder="Enter Certificate No..." required
                               class="w-full text-xs border-gray-300 rounded-xl focus:ring-comsoc-500 focus:border-comsoc-500 text-center uppercase font-mono py-2.5"/>
                        <button type="submit"
                                class="w-full py-2.5 px-4 bg-comsoc-700 hover:bg-comsoc-800 text-white font-bold text-xs rounded-xl shadow-xs transition">
                            Verify Number
                        </button>
                    </form>
                </div>
            </div>
        @endif

    </main>

    {{-- Footer --}}
    <footer class="w-full max-w-2xl text-center py-4 text-xs text-gray-400">
        <p>© 2026 Computer Society · College of Computing and Information Sciences · University of Makati</p>
    </footer>

</body>
</html>
