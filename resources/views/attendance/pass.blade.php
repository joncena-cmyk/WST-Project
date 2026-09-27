<x-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('events.my-events') }}" class="hover:text-gray-900 transition-colors">My Events</a>
            <span>/</span>
            <span class="text-gray-800 font-semibold">QR Pass</span>
        </div>
    </x-slot>

    <div class="max-w-md mx-auto">

        @if($alreadyCheckedIn)
            <div class="mb-5 flex items-center gap-3 p-4 bg-comsoc-50 border border-comsoc-200 rounded-xl shadow-xs">
                <div class="w-8 h-8 rounded-full bg-comsoc-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-comsoc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-comsoc-800">Checked In</p>
                    <p class="text-xs text-comsoc-600 mt-0.5">Your attendance for this event has already been recorded.</p>
                </div>
            </div>
        @endif

        {{-- Pass Card Container --}}
        <div id="pass-card-container">
            <div class="bg-white border border-gray-200 rounded-3xl shadow-xl overflow-hidden">
                {{-- Header strip --}}
                <div class="relative bg-gradient-to-br from-comsoc-900 via-comsoc-800 to-slate-950 px-6 py-6 text-white">
                    <div class="absolute inset-0" style="background-image:radial-gradient(rgba(255,255,255,0.08) 1px,transparent 1px);background-size:16px 16px"></div>
                    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-gold-500 via-yellow-400 to-gold-500"></div>
                    
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <img src="{{ asset('images/comsoc_logo.png') }}" alt="" class="w-7 h-7 object-contain opacity-95"/>
                                <span class="text-[11px] font-black tracking-widest uppercase text-gold-300">COMSOC Hub · Official Pass</span>
                            </div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-white/10 text-white backdrop-blur-xs border border-white/20">
                                #{{ str_pad($registration->id, 5, '0', STR_PAD_LEFT) }}
                            </span>
                        </div>

                        <h2 class="text-xl font-black text-white leading-snug tracking-tight">{{ $event->title }}</h2>

                        <div class="grid grid-cols-2 gap-2 mt-4 pt-3 border-t border-white/10 text-xs text-emerald-100">
                            <div class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-gold-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span>{{ $event->event_date->format('M d, Y') }}</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-gold-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>{{ \Carbon\Carbon::parse($event->start_time)->format('g:i A') }}</span>
                            </div>
                            <div class="col-span-2 flex items-center gap-1.5 truncate">
                                <svg class="w-4 h-4 text-gold-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span class="truncate">{{ $event->venue }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Dashed divider (ticket tear) --}}
                <div class="relative flex items-center bg-gray-50 px-4 py-2">
                    <div class="w-6 h-6 rounded-full bg-slate-100 border border-gray-300 -ml-7 shrink-0"></div>
                    <div class="flex-1 border-t-2 border-dashed border-gray-300 mx-2"></div>
                    <div class="w-6 h-6 rounded-full bg-slate-100 border border-gray-300 -mr-7 shrink-0"></div>
                </div>

                {{-- QR code + attendee info --}}
                <div class="px-6 py-6 bg-white flex flex-col items-center">
                    {{-- QR Code Box --}}
                    <a href="{{ route('attendance.qr.download', $event) }}" download
                       class="p-4 bg-white border-2 border-dashed border-comsoc-200 rounded-2xl shadow-sm mb-4 relative group cursor-pointer block" title="Click to download QR code image">
                        <div id="qrcode" class="flex items-center justify-center"></div>
                        <div class="absolute inset-0 bg-comsoc-950/75 text-white rounded-2xl flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity p-2 text-center backdrop-blur-xs">
                            <svg class="w-6 h-6 mb-1 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            <span class="text-xs font-bold">Download QR PNG</span>
                        </div>
                    </a>

                    {{-- Attendee info --}}
                    <div class="w-full text-center">
                        <img src="{{ Auth::user()->getProfilePictureUrl() }}"
                             class="w-14 h-14 rounded-full object-cover border-2 border-comsoc-300 mx-auto mb-2 shadow-xs"/>
                        <p class="font-black text-gray-900 text-lg leading-tight">{{ Auth::user()->name }}</p>
                        <div class="flex items-center justify-center gap-2 mt-1">
                            @if(Auth::user()->student_id)
                                <span class="text-xs font-semibold text-gray-600 bg-gray-100 px-2 py-0.5 rounded-md">
                                    ID: {{ Auth::user()->student_id }}
                                </span>
                            @endif
                            @if(Auth::user()->program)
                                <span class="inline-flex px-2 py-0.5 rounded-md text-xs font-bold bg-comsoc-100 text-comsoc-800">
                                    {{ Auth::user()->program->code }}
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Token (fallback) --}}
                    <div class="mt-4 w-full">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center mb-1">Registration Token</p>
                        <div class="bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-center">
                            <code class="text-xs font-mono font-semibold text-gray-700 select-all break-all">{{ $registration->qr_code }}</code>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 text-center">
                    <p class="text-[11px] text-gray-500 font-medium">
                        Present this pass at the entrance to mark your attendance.
                    </p>
                </div>
            </div>
        </div>

        {{-- Action Buttons (Download & Print) --}}
        <div class="mt-5 space-y-2.5 no-print">
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('attendance.qr.download', $event) }}" download
                   class="flex items-center justify-center gap-2 px-4 py-3 bg-white hover:bg-gray-50 text-gray-800 border border-gray-200 rounded-xl text-xs font-bold shadow-sm transition-all hover:border-gray-300">
                    <svg class="w-4 h-4 text-comsoc-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                    Download QR Code
                </a>

                <button type="button" onclick="downloadFullPassCard()"
                        class="flex items-center justify-center gap-2 px-4 py-3 bg-comsoc-600 hover:bg-comsoc-700 text-white rounded-xl text-xs font-bold shadow-sm transition-all">
                    <svg class="w-4 h-4 shrink-0 text-gold-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Save Pass as Image
                </button>
            </div>

            <div class="flex items-center justify-between pt-2">
                <button type="button" onclick="window.print()"
                        class="inline-flex items-center gap-1.5 text-xs text-gray-500 hover:text-gray-800 font-medium transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Print / Save as PDF
                </button>

                <a href="{{ route('events.my-events') }}"
                   class="text-xs text-comsoc-600 hover:text-comsoc-800 font-semibold transition-colors">
                    ← Back to My Events
                </a>
            </div>
        </div>
    </div>

    {{-- Print Styles --}}
    <style>
        @media print {
            body * { visibility: hidden !important; }
            #pass-card-container, #pass-card-container * { visibility: visible !important; }
            #pass-card-container {
                position: absolute !important;
                left: 50% !important;
                top: 5% !important;
                transform: translateX(-50%) !important;
                width: 360px !important;
            }
            .no-print { display: none !important; }
        }
    </style>

    {{-- QR Code Generator (qrcode.js) --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        // Generate QR code
        const qrContainer = document.getElementById("qrcode");
        new QRCode(qrContainer, {
            text: "{{ $registration->qr_code }}",
            width: 190,
            height: 190,
            colorDark: "#064e3b",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });

        // Download QR code only
        function downloadQrOnly() {
            const canvas = document.querySelector('#qrcode canvas');
            let dataUrl;
            if (canvas) {
                dataUrl = canvas.toDataURL('image/png');
            } else {
                const img = document.querySelector('#qrcode img');
                if (img) dataUrl = img.src;
            }
            if (!dataUrl) {
                alert('QR code is generating. Please try again in a moment.');
                return;
            }
            const a = document.createElement('a');
            a.href = dataUrl;
            a.download = `COMSOC-QR-Pass-{{ \Illuminate\Support\Str::slug($event->title) }}-{{ Auth::user()->student_id ?? Auth::id() }}.png`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }

        // Generate and download full stylized pass card
        function downloadFullPassCard() {
            const qrCanvas = document.querySelector('#qrcode canvas');
            if (!qrCanvas) {
                alert('QR code is not ready yet.');
                return;
            }

            const canvas = document.createElement('canvas');
            canvas.width = 600;
            canvas.height = 880;
            const ctx = canvas.getContext('2d');

            // Card background
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, 600, 880);

            // Header Background (Dark Emerald Gradient)
            const grad = ctx.createLinearGradient(0, 0, 600, 240);
            grad.addColorStop(0, '#064e3b');
            grad.addColorStop(1, '#022c22');
            ctx.fillStyle = grad;
            ctx.fillRect(0, 0, 600, 230);

            // Gold accent stripe
            ctx.fillStyle = '#f59e0b';
            ctx.fillRect(0, 0, 600, 8);

            // Sub-header badge
            ctx.fillStyle = '#fef08a';
            ctx.font = 'bold 12px sans-serif';
            ctx.fillText('COMSOC HUB  •  OFFICIAL EVENT PASS', 40, 45);

            // Event Title
            ctx.fillStyle = '#ffffff';
            ctx.font = 'bold 22px sans-serif';
            const eventTitle = {{ json_encode($event->title) }};
            wrapCanvasText(ctx, eventTitle, 40, 80, 520, 28);

            // Date, Time & Venue
            ctx.fillStyle = '#a7f3d0';
            ctx.font = '14px sans-serif';
            ctx.fillText("{{ $event->event_date->format('l, F d, Y') }}  •  {{ \Carbon\Carbon::parse($event->start_time)->format('g:i A') }}", 40, 160);
            ctx.font = '13px sans-serif';
            ctx.fillText("Venue: {{ addslashes($event->venue) }}", 40, 185);

            // Perforation dashed line
            ctx.strokeStyle = '#d1d5db';
            ctx.setLineDash([8, 8]);
            ctx.lineWidth = 2;
            ctx.beginPath();
            ctx.moveTo(0, 230);
            ctx.lineTo(600, 230);
            ctx.stroke();
            ctx.setLineDash([]);

            // Draw QR Code centered inside framed card
            drawRoundRect(ctx, 170, 260, 260, 260, 16, '#f9fafb', '#d1fae5');
            ctx.drawImage(qrCanvas, 185, 275, 230, 230);

            // Attendee Name
            ctx.fillStyle = '#111827';
            ctx.font = 'bold 22px sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText({{ json_encode(Auth::user()->name) }}, 300, 575);

            // Student ID & Program
            ctx.fillStyle = '#4b5563';
            ctx.font = '14px sans-serif';
            const subBadge = "{{ Auth::user()->student_id ? 'ID: ' . Auth::user()->student_id . '  •  ' : '' }}{{ Auth::user()->program?->code ?? 'Student' }}";
            ctx.fillText(subBadge, 300, 605);

            // Token box
            drawRoundRect(ctx, 90, 640, 420, 50, 10, '#f3f4f6', '#e5e7eb');
            ctx.fillStyle = '#374151';
            ctx.font = '13px monospace';
            ctx.fillText("Token: {{ $registration->qr_code }}", 300, 672);

            // Instructions footer
            ctx.fillStyle = '#9ca3af';
            ctx.font = '12px sans-serif';
            ctx.fillText('Present this pass to the entrance officer for check-in.', 300, 750);
            ctx.fillText('University of Makati  •  Computer Society', 300, 775);

            // Trigger Download
            const passDataUrl = canvas.toDataURL('image/png');
            const a = document.createElement('a');
            a.href = passDataUrl;
            a.download = `COMSOC-Pass-{{ \Illuminate\Support\Str::slug($event->title) }}-{{ Auth::user()->student_id ?? Auth::id() }}.png`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }

        function wrapCanvasText(ctx, text, x, y, maxWidth, lineHeight) {
            const words = text.split(' ');
            let line = '';
            for (let n = 0; n < words.length; n++) {
                const testLine = line + words[n] + ' ';
                const metrics = ctx.measureText(testLine);
                if (metrics.width > maxWidth && n > 0) {
                    ctx.fillText(line, x, y);
                    line = words[n] + ' ';
                    y += lineHeight;
                } else {
                    line = testLine;
                }
            }
            ctx.fillText(line, x, y);
        }

        function drawRoundRect(ctx, x, y, width, height, radius, fill, stroke) {
            ctx.beginPath();
            ctx.moveTo(x + radius, y);
            ctx.lineTo(x + width - radius, y);
            ctx.quadraticCurveTo(x + width, y, x + width, y + radius);
            ctx.lineTo(x + width, y + height - radius);
            ctx.quadraticCurveTo(x + width, y + height, x + width - radius, y + height);
            ctx.lineTo(x + radius, y + height);
            ctx.quadraticCurveTo(x, y + height, x, y + height - radius);
            ctx.lineTo(x, y + radius);
            ctx.quadraticCurveTo(x, y, x + radius, y);
            ctx.closePath();
            if (fill) {
                ctx.fillStyle = fill;
                ctx.fill();
            }
            if (stroke) {
                ctx.strokeStyle = stroke;
                ctx.lineWidth = 1.5;
                ctx.stroke();
            }
        }
    </script>
</x-dashboard-layout>
