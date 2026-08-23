<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'COMSOC Hub') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
<div class="min-h-screen flex">

    {{-- ===== LEFT PANEL — hero photo with overlay ===== --}}
    <div class="hidden lg:flex lg:w-5/12 xl:w-1/2 relative overflow-hidden flex-col">
        {{-- Photo background --}}
        <div class="absolute inset-0">
            <img src="{{ asset('images/hero_logo.jpg') }}"
                 alt="COMSOC"
                 class="w-full h-full object-cover"
                 style="filter:brightness(0.45) saturate(1.1)"/>
        </div>
        {{-- Overlay gradient --}}
        <div class="absolute inset-0"
             style="background:linear-gradient(to bottom,rgba(5,46,22,0.85) 0%,rgba(5,46,22,0.70) 50%,rgba(5,46,22,0.90) 100%)"></div>
        {{-- Grid pattern --}}
        <div class="absolute inset-0"
             style="background-image:linear-gradient(rgba(255,255,255,0.025) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.025) 1px,transparent 1px);background-size:40px 40px"></div>
        {{-- Gold top accent --}}
        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-gold-600 via-gold-400 to-gold-600"></div>

        {{-- Content --}}
        <div class="relative z-10 flex flex-col h-full p-10">
            {{-- Logo --}}
            <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                <img src="{{ asset('images/comsoc_logo.png') }}"
                     alt="COMSOC Logo"
                     class="w-11 h-11 object-contain drop-shadow-lg"/>
                <div>
                    <p class="text-white font-black text-lg leading-none tracking-tight">COMSOC Hub</p>
                    <p class="text-comsoc-300 text-xs mt-0.5">CCIS · University of Makati</p>
                </div>
            </a>

            {{-- Centre tagline --}}
            <div class="flex-1 flex flex-col justify-center">
                @php $ayStart = (int)date('n') >= 6 ? (int)date('Y') : (int)date('Y') - 1; @endphp
                <p class="text-xs font-bold text-gold-400 uppercase tracking-widest mb-3">
                    Academic Year {{ $ayStart }}–{{ $ayStart + 1 }}
                </p>
                <h1 class="text-3xl xl:text-4xl font-black text-white leading-tight mb-4">
                    Your organization,<br>
                    <span style="background:linear-gradient(135deg,#fde047,#facc15);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">
                        one platform.
                    </span>
                </h1>
                <p class="text-comsoc-200 text-sm leading-relaxed max-w-xs mb-8">
                    Membership, events, community, attendance, and documents — all in one place for the Computer Society of CCIS.
                </p>

                {{-- Feature pills --}}
                <div class="flex flex-wrap gap-2">
                    @foreach(['Membership','Events','Community','Certificates','QR Attendance'] as $f)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-comsoc-800/60 border border-comsoc-700/50 text-comsoc-200 text-xs font-medium">
                            <span class="w-1 h-1 rounded-full bg-green-400"></span>
                            {{ $f }}
                        </span>
                    @endforeach
                </div>
            </div>

            {{-- Footer --}}
            <p class="text-comsoc-600 text-[11px]">
                &copy; {{ date('Y') }} COMSOC · CCIS · University of Makati
            </p>
        </div>
    </div>

    {{-- ===== RIGHT PANEL — form ===== --}}
    <div class="flex-1 flex flex-col justify-center items-center px-6 py-10 bg-gray-50 min-h-screen">

        {{-- Mobile brand --}}
        <div class="lg:hidden flex flex-col items-center mb-8">
            <img src="{{ asset('images/comsoc_logo.png') }}" alt="COMSOC Logo" class="w-14 h-14 object-contain mb-3"/>
            <p class="font-black text-comsoc-900 text-xl tracking-tight">COMSOC Hub</p>
            <p class="text-comsoc-500 text-xs mt-0.5">CCIS · University of Makati</p>
        </div>

        <div class="{{ ($wide ?? false) ? 'w-full max-w-md' : 'w-full max-w-sm' }}">
            {{ $slot }}
        </div>

        {{-- Back to home --}}
        <a href="{{ url('/') }}" class="mt-8 text-xs text-gray-400 hover:text-comsoc-700 transition-colors flex items-center gap-1">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to home
        </a>
    </div>

</div>
@stack('scripts')
</body>
</html>
