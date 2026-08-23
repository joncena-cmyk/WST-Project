<x-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('events.show', $event) }}" class="hover:text-gray-900 transition-colors">{{ $event->title }}</a>
            <span>/</span>
            <span class="text-gray-800 font-semibold">QR Scanner</span>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto space-y-5">

        {{-- Stats strip --}}
        <div class="grid grid-cols-3 gap-4">
            @foreach([
                ['Registered', $registeredCount, 'text-blue-600 bg-blue-50 border-blue-100'],
                ['Checked In', $attendanceCount,  'text-comsoc-600 bg-comsoc-50 border-comsoc-100'],
                ['Remaining',  max(0, $registeredCount - $attendanceCount), 'text-orange-600 bg-orange-50 border-orange-100'],
            ] as [$label, $value, $style])
                <div class="bg-white border rounded-xl p-4 shadow-sm text-center {{ $style }}">
                    <p class="text-2xl font-black" id="stat-{{ strtolower($label) }}">{{ $value }}</p>
                    <p class="text-xs font-semibold uppercase tracking-wide mt-0.5 opacity-70">{{ $label }}</p>
                </div>
            @endforeach
        </div>

        {{-- Scanner card --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            {{-- Header --}}
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 bg-comsoc-950">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></div>
                    <span class="text-sm font-bold text-white">QR Scanner — {{ $event->title }}</span>
                </div>
                <span class="text-xs text-comsoc-400" id="scan-time">Ready</span>
            </div>

            {{-- Camera viewport --}}
            <div class="relative bg-comsoc-950" style="min-height: 320px;">
                <video id="video"
                       class="w-full block"
                       style="max-height: 360px; object-fit: cover;"
                       playsinline autoplay muted></video>
                <canvas id="canvas" class="hidden"></canvas>

                {{-- Scan overlay --}}
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                    <div id="scan-overlay" class="relative">
                        {{-- Corner brackets --}}
                        <div class="w-52 h-52 relative">
                            <div class="absolute top-0 left-0 w-8 h-8 border-t-4 border-l-4 border-gold-400 rounded-tl-lg"></div>
                            <div class="absolute top-0 right-0 w-8 h-8 border-t-4 border-r-4 border-gold-400 rounded-tr-lg"></div>
                            <div class="absolute bottom-0 left-0 w-8 h-8 border-b-4 border-l-4 border-gold-400 rounded-bl-lg"></div>
                            <div class="absolute bottom-0 right-0 w-8 h-8 border-b-4 border-r-4 border-gold-400 rounded-br-lg"></div>
                            {{-- Scan line --}}
                            <div id="scan-line" class="absolute left-2 right-2 h-0.5 bg-gold-400/80 top-0"
                                 style="animation: scanLine 2s linear infinite;"></div>
                        </div>
                    </div>
                </div>

                {{-- Camera off placeholder --}}
                <div id="camera-placeholder" class="absolute inset-0 flex flex-col items-center justify-center bg-comsoc-950">
                    <svg class="w-16 h-16 text-comsoc-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <p class="text-sm text-comsoc-400 font-medium mb-3">Camera not started</p>
                    <button id="start-btn"
                            onclick="startCamera()"
                            class="px-5 py-2 bg-comsoc-600 text-white text-sm font-semibold rounded-lg hover:bg-comsoc-500 transition-colors">
                        Start Camera
                    </button>
                </div>

                {{-- Result overlay --}}
                <div id="result-overlay"
                     class="hidden absolute inset-0 flex items-center justify-center"
                     style="background:rgba(5,46,22,0.92)">
                    <div class="text-center px-6">
                        <div id="result-icon" class="text-5xl mb-3">✓</div>
                        <p id="result-title" class="text-xl font-black text-white mb-1"></p>
                        <p id="result-subtitle" class="text-sm text-comsoc-200 mb-1"></p>
                        <p id="result-detail" class="text-xs text-comsoc-400"></p>
                    </div>
                </div>
            </div>

            {{-- Controls --}}
            <div class="flex items-center gap-3 px-5 py-4 border-t border-gray-100">
                <button id="stop-btn"
                        onclick="stopCamera()"
                        class="hidden px-4 py-2 border border-gray-300 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50 transition-colors">
                    Stop Camera
                </button>
                <a href="{{ route('attendance.index', $event) }}"
                   class="ml-auto text-sm font-semibold text-comsoc-600 hover:text-comsoc-800 transition-colors">
                    View attendance list →
                </a>
            </div>
        </div>

        {{-- Manual input fallback --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
            <h3 class="text-sm font-bold text-gray-800 mb-1">Manual QR Token Entry</h3>
            <p class="text-xs text-gray-500 mb-4">Use this if the camera is unavailable. Enter the 32-character token from the student's pass.</p>
            <form method="POST" action="{{ route('attendance.manual', $event) }}" class="flex gap-3">
                @csrf
                @if(session('success'))
                    <div x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,3000)"
                         class="w-full p-3 bg-comsoc-50 border border-comsoc-200 rounded-lg text-sm text-comsoc-700 font-medium mb-2">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,4000)"
                         class="w-full p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700 font-medium mb-2">
                        {{ session('error') }}
                    </div>
                @endif
                <input type="text"
                       name="qr_code"
                       required
                       maxlength="32"
                       minlength="32"
                       placeholder="Paste or type the 32-character QR token..."
                       class="flex-1 border-gray-300 focus:border-comsoc-500 focus:ring-comsoc-500 rounded-lg text-sm font-mono"/>
                <button type="submit"
                        class="px-5 py-2 bg-comsoc-700 text-white text-sm font-semibold rounded-lg hover:bg-comsoc-800 transition-colors shrink-0">
                    Check In
                </button>
            </form>
        </div>

    </div>

    {{-- jsQR library --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jsqr/1.4.0/jsQR.min.js"></script>

    <style>
        @keyframes scanLine {
            0%   { top: 4px; }
            50%  { top: calc(100% - 4px); }
            100% { top: 4px; }
        }
    </style>

    <script>
        const VIDEO     = document.getElementById('video');
        const CANVAS    = document.getElementById('canvas');
        const RESULT    = document.getElementById('result-overlay');
        const PLACEHOLDER = document.getElementById('camera-placeholder');
        const START_BTN = document.getElementById('start-btn');
        const STOP_BTN  = document.getElementById('stop-btn');
        const SCAN_TIME = document.getElementById('scan-time');

        let stream = null;
        let scanning = false;
        let debounce = false;
        const CHECK_IN_URL = '{{ route('attendance.checkin', $event) }}';
        const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function startCamera() {
            navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
                .then(s => {
                    stream = s;
                    VIDEO.srcObject = s;
                    VIDEO.style.display = 'block';
                    PLACEHOLDER.style.display = 'none';
                    STOP_BTN.classList.remove('hidden');
                    scanning = true;
                    requestAnimationFrame(tick);
                })
                .catch(err => {
                    PLACEHOLDER.querySelector('p').textContent = 'Camera access denied. Use manual entry below.';
                });
        }

        function stopCamera() {
            scanning = false;
            if (stream) stream.getTracks().forEach(t => t.stop());
            VIDEO.style.display = 'none';
            PLACEHOLDER.style.display = 'flex';
            STOP_BTN.classList.add('hidden');
            RESULT.classList.add('hidden');
        }

        function tick() {
            if (!scanning) return;
            if (VIDEO.readyState === VIDEO.HAVE_ENOUGH_DATA) {
                const ctx = CANVAS.getContext('2d');
                CANVAS.width  = VIDEO.videoWidth;
                CANVAS.height = VIDEO.videoHeight;
                ctx.drawImage(VIDEO, 0, 0, CANVAS.width, CANVAS.height);
                const imageData = ctx.getImageData(0, 0, CANVAS.width, CANVAS.height);
                const code = jsQR(imageData.data, imageData.width, imageData.height, { inversionAttempts: 'dontInvert' });
                if (code && !debounce) {
                    debounce = true;
                    processQr(code.data);
                }
            }
            requestAnimationFrame(tick);
        }

        function processQr(qrCode) {
            SCAN_TIME.textContent = 'Processing...';

            fetch(CHECK_IN_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                    'Accept':       'application/json',
                },
                body: JSON.stringify({ qr_code: qrCode }),
            })
            .then(r => r.json())
            .then(data => {
                showResult(data);
                if (data.success) {
                    updateStats();
                }
                SCAN_TIME.textContent = new Date().toLocaleTimeString();
                setTimeout(() => {
                    RESULT.classList.add('hidden');
                    debounce = false;
                }, 3500);
            })
            .catch(() => {
                showResult({ success: false, message: 'Network error. Please try again.', type: 'error' });
                setTimeout(() => { RESULT.classList.add('hidden'); debounce = false; }, 3000);
            });
        }

        function showResult(data) {
            const icon     = document.getElementById('result-icon');
            const title    = document.getElementById('result-title');
            const subtitle = document.getElementById('result-subtitle');
            const detail   = document.getElementById('result-detail');

            if (data.success) {
                icon.textContent  = '✓';
                icon.style.color  = '#4ade80';
                title.textContent = 'Check-in Successful!';
                subtitle.textContent = data.student ? data.student.name : '';
                detail.textContent = data.student
                    ? `${data.student.student_id} · ${data.student.program} · ${data.student.checked_in_at}`
                    : '';
                RESULT.style.background = 'rgba(5,46,22,0.94)';
            } else if (data.type === 'duplicate') {
                icon.textContent  = '⚠';
                icon.style.color  = '#facc15';
                title.textContent = 'Already Checked In';
                subtitle.textContent = data.student ? data.student.name : '';
                detail.textContent = data.message;
                RESULT.style.background = 'rgba(30,30,5,0.94)';
            } else {
                icon.textContent  = '✕';
                icon.style.color  = '#f87171';
                title.textContent = 'Check-in Failed';
                subtitle.textContent = data.message;
                detail.textContent = '';
                RESULT.style.background = 'rgba(46,5,5,0.94)';
            }
            RESULT.classList.remove('hidden');
        }

        function updateStats() {
            const checkedEl   = document.getElementById('stat-checked');
            const remainingEl = document.getElementById('stat-remaining');
            if (checkedEl) {
                const newVal = parseInt(checkedEl.textContent) + 1;
                checkedEl.textContent = newVal;
            }
            if (remainingEl) {
                const newVal = Math.max(0, parseInt(remainingEl.textContent) - 1);
                remainingEl.textContent = newVal;
            }
        }
    </script>

</x-dashboard-layout>
