<x-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('events.show', $event) }}" class="hover:text-gray-900 transition-colors">{{ $event->title }}</a>
            <span>/</span>
            <span class="text-gray-800 font-semibold">QR Attendance Scanner</span>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto space-y-5">

        {{-- Stats strip --}}
        <div class="grid grid-cols-3 gap-3 sm:gap-4">
            <div class="bg-white border border-blue-100 rounded-2xl p-4 shadow-sm text-center bg-blue-50/50">
                <p class="text-2xl sm:text-3xl font-black text-blue-600" id="stat-registered">{{ $registeredCount }}</p>
                <p class="text-[11px] font-bold text-blue-700/70 uppercase tracking-wider mt-0.5">Registered</p>
            </div>
            <div class="bg-white border border-green-100 rounded-2xl p-4 shadow-sm text-center bg-green-50/50">
                <p class="text-2xl sm:text-3xl font-black text-green-600" id="stat-checked">{{ $attendanceCount }}</p>
                <p class="text-[11px] font-bold text-green-700/70 uppercase tracking-wider mt-0.5">Checked In</p>
            </div>
            <div class="bg-white border border-amber-100 rounded-2xl p-4 shadow-sm text-center bg-amber-50/50">
                <p class="text-2xl sm:text-3xl font-black text-amber-600" id="stat-remaining">{{ max(0, $registeredCount - $attendanceCount) }}</p>
                <p class="text-[11px] font-bold text-amber-700/70 uppercase tracking-wider mt-0.5">Remaining</p>
            </div>
        </div>

        {{-- Scan Modes Tabs --}}
        <div x-data="{ mode: 'camera' }" class="space-y-4">
            <div class="flex p-1 bg-gray-100 rounded-xl">
                <button type="button"
                        @click="mode = 'camera'"
                        :class="mode === 'camera' ? 'bg-white text-comsoc-800 font-bold shadow-xs' : 'text-gray-600 hover:text-gray-900 font-medium'"
                        class="flex-1 py-2 text-xs sm:text-sm rounded-lg transition-all flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Live Camera
                </button>
                <button type="button"
                        @click="mode = 'file'"
                        :class="mode === 'file' ? 'bg-white text-comsoc-800 font-bold shadow-xs' : 'text-gray-600 hover:text-gray-900 font-medium'"
                        class="flex-1 py-2 text-xs sm:text-sm rounded-lg transition-all flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Upload QR Image
                </button>
                <button type="button"
                        @click="mode = 'manual'"
                        :class="mode === 'manual' ? 'bg-white text-comsoc-800 font-bold shadow-xs' : 'text-gray-600 hover:text-gray-900 font-medium'"
                        class="flex-1 py-2 text-xs sm:text-sm rounded-lg transition-all flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Manual Token
                </button>
            </div>

            {{-- 1. CAMERA SCANNER MODE --}}
            <div x-show="mode === 'camera'" class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100 bg-comsoc-950">
                    <div class="flex items-center gap-2">
                        <div id="scanner-indicator" class="w-2.5 h-2.5 rounded-full bg-gray-400"></div>
                        <span class="text-xs sm:text-sm font-bold text-white">Live Scanner</span>
                    </div>
                    <span class="text-xs text-comsoc-300 font-mono" id="scan-status">Ready</span>
                </div>

                {{-- Camera Viewport --}}
                <div class="relative bg-comsoc-950 flex items-center justify-center overflow-hidden" style="min-height: 340px;">
                    <video id="video"
                           class="w-full block hidden"
                           style="max-height: 380px; object-fit: cover;"
                           playsinline autoplay muted></video>
                    <canvas id="canvas" class="hidden"></canvas>

                    {{-- Scan Viewport Overlay --}}
                    <div id="scan-frame" class="absolute inset-0 flex items-center justify-center pointer-events-none hidden">
                        <div class="w-56 h-56 sm:w-64 sm:h-64 relative">
                            {{-- Corners --}}
                            <div class="absolute top-0 left-0 w-8 h-8 border-t-4 border-l-4 border-gold-400 rounded-tl-xl"></div>
                            <div class="absolute top-0 right-0 w-8 h-8 border-t-4 border-r-4 border-gold-400 rounded-tr-xl"></div>
                            <div class="absolute bottom-0 left-0 w-8 h-8 border-b-4 border-l-4 border-gold-400 rounded-bl-xl"></div>
                            <div class="absolute bottom-0 right-0 w-8 h-8 border-b-4 border-r-4 border-gold-400 rounded-br-xl"></div>
                            {{-- Animated Scan line --}}
                            <div class="absolute left-2 right-2 h-1 bg-gradient-to-r from-transparent via-gold-400 to-transparent top-0"
                                 style="animation: scanLine 2s linear infinite;"></div>
                        </div>
                    </div>

                    {{-- Placeholder before start --}}
                    <div id="camera-placeholder" class="absolute inset-0 flex flex-col items-center justify-center p-6 text-center bg-comsoc-950">
                        <div class="w-16 h-16 rounded-2xl bg-comsoc-900 border border-comsoc-800 flex items-center justify-center mb-3">
                            <svg class="w-8 h-8 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-bold text-white mb-1">Point your camera at a student QR Pass</p>
                        <p class="text-xs text-comsoc-400 mb-4 max-w-xs" id="camera-error-msg">Click below to allow camera access for instant scanning.</p>
                        <button type="button"
                                id="start-btn"
                                onclick="startCamera()"
                                class="px-6 py-2.5 bg-comsoc-600 hover:bg-comsoc-500 text-white text-sm font-black rounded-xl transition shadow-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                            </svg>
                            Start Camera
                        </button>
                    </div>

                    {{-- Feedback Result Overlay --}}
                    <div id="result-overlay"
                         class="hidden absolute inset-0 flex items-center justify-center p-6 text-center z-20 transition-all duration-300">
                        <div class="max-w-xs w-full bg-white rounded-2xl p-6 shadow-2xl text-center space-y-3 border">
                            <div id="result-icon-wrap" class="w-14 h-14 rounded-full flex items-center justify-center mx-auto text-2xl font-black"></div>
                            <div>
                                <h4 id="result-title" class="font-black text-gray-900 text-lg"></h4>
                                <p id="result-subtitle" class="font-bold text-sm text-comsoc-700 mt-0.5"></p>
                                <p id="result-detail" class="text-xs text-gray-500 mt-1"></p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Camera Controls --}}
                <div class="flex items-center justify-between p-4 border-t border-gray-100 bg-gray-50/50">
                    <div class="flex items-center gap-2">
                        <select id="camera-select"
                                onchange="switchCamera(this.value)"
                                class="hidden text-xs rounded-lg border-gray-300 bg-white py-1.5 px-2.5 font-medium text-gray-700">
                        </select>
                        <button type="button"
                                id="stop-btn"
                                onclick="stopCamera()"
                                class="hidden px-3 py-1.5 text-xs font-semibold text-gray-600 hover:text-gray-900 bg-white border border-gray-300 rounded-lg shadow-xs transition">
                            Stop Camera
                        </button>
                    </div>
                    <a href="{{ route('attendance.index', $event) }}"
                       class="text-xs font-bold text-comsoc-600 hover:text-comsoc-800 transition">
                        View Attendance List →
                    </a>
                </div>
            </div>

            {{-- 2. FILE UPLOAD SCAN MODE --}}
            <div x-show="mode === 'file'" class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 space-y-4">
                <div>
                    <h3 class="text-sm font-bold text-gray-900">Upload QR Pass Screenshot</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Useful when testing on the same screen, receiving a screenshot via chat, or without a camera.</p>
                </div>

                <div id="drop-zone"
                     onclick="document.getElementById('qr-file-input').click()"
                     class="border-2 border-dashed border-gray-300 hover:border-comsoc-500 bg-gray-50 hover:bg-comsoc-50/30 rounded-2xl p-8 text-center cursor-pointer transition">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-sm font-bold text-gray-700">Click to select or drag & drop QR image</p>
                    <p class="text-xs text-gray-400 mt-1">PNG, JPG, WebP screenshot of student pass</p>
                    <input type="file" id="qr-file-input" accept="image/*" class="hidden" onchange="handleImageUpload(event)"/>
                </div>

                <div id="file-scan-status" class="hidden p-4 rounded-xl text-sm font-semibold"></div>
            </div>

            {{-- 3. MANUAL TOKEN MODE --}}
            <div x-show="mode === 'manual'" class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 space-y-4">
                <div>
                    <h3 class="text-sm font-bold text-gray-900">Manual 32-Character QR Token Entry</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Enter the alphanumeric token printed directly beneath the student's QR code.</p>
                </div>

                @if(session('success'))
                    <div x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,4000)"
                         class="p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-800 font-bold">
                        ✓ {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,5000)"
                         class="p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-800 font-bold">
                        ✕ {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('attendance.manual', $event) }}" class="space-y-3">
                    @csrf
                    <div>
                        <input type="text"
                               name="qr_code"
                               required
                               maxlength="32"
                               minlength="32"
                               placeholder="e.g. 9b1deb4d-3b7d-4bad-9bdd-2b0d7b3dcb6d (32 chars)"
                               class="w-full border-gray-300 focus:border-comsoc-500 focus:ring-comsoc-500 rounded-xl text-sm font-mono tracking-wider"/>
                    </div>
                    <button type="submit"
                            class="w-full py-2.5 bg-comsoc-700 hover:bg-comsoc-800 text-white text-sm font-bold rounded-xl transition shadow-sm">
                        Submit Manual Check-In
                    </button>
                </form>
            </div>
        </div>

    </div>

    {{-- jsQR library --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jsqr/1.4.0/jsQR.min.js"></script>

    <style>
        @keyframes scanLine {
            0%   { top: 4px; opacity: 0.8; }
            50%  { top: calc(100% - 4px); opacity: 1; }
            100% { top: 4px; opacity: 0.8; }
        }
    </style>

    <script>
        const VIDEO       = document.getElementById('video');
        const CANVAS      = document.getElementById('canvas');
        const RESULT      = document.getElementById('result-overlay');
        const PLACEHOLDER = document.getElementById('camera-placeholder');
        const START_BTN   = document.getElementById('start-btn');
        const STOP_BTN    = document.getElementById('stop-btn');
        const SCAN_FRAME  = document.getElementById('scan-frame');
        const SCAN_STATUS = document.getElementById('scan-status');
        const INDICATOR   = document.getElementById('scanner-indicator');
        const CAMERA_SELECT = document.getElementById('camera-select');
        const CHECK_IN_URL= '{{ route('attendance.checkin', $event) }}';
        const CSRF        = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        let stream = null;
        let scanning = false;
        let debounce = false;

        // Audio Beep generator
        function playBeep(success = true) {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(success ? 880 : 320, ctx.currentTime);
                gain.gain.setValueAtTime(0.15, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.2);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start();
                osc.stop(ctx.currentTime + 0.2);
            } catch(e) {}
        }

        async function getAvailableCameras() {
            try {
                const devices = await navigator.mediaDevices.enumerateDevices();
                const videoDevices = devices.filter(d => d.kind === 'videoinput');
                if (videoDevices.length > 1) {
                    CAMERA_SELECT.innerHTML = '';
                    videoDevices.forEach((dev, idx) => {
                        const opt = document.createElement('option');
                        opt.value = dev.deviceId;
                        opt.text = dev.label || `Camera ${idx + 1}`;
                        CAMERA_SELECT.appendChild(opt);
                    });
                    CAMERA_SELECT.classList.remove('hidden');
                }
            } catch (e) {}
        }

        async function startCamera(deviceId = null) {
            SCAN_STATUS.textContent = 'Initializing...';

            const constraints = {
                video: deviceId
                    ? { deviceId: { exact: deviceId }, width: { ideal: 1280 }, height: { ideal: 720 } }
                    : { facingMode: { ideal: 'environment' }, width: { ideal: 1280 }, height: { ideal: 720 } }
            };

            try {
                try {
                    stream = await navigator.mediaDevices.getUserMedia(constraints);
                } catch (e) {
                    stream = await navigator.mediaDevices.getUserMedia({ video: true });
                }

                VIDEO.srcObject = stream;
                VIDEO.classList.remove('hidden');
                PLACEHOLDER.classList.add('hidden');
                STOP_BTN.classList.remove('hidden');
                SCAN_FRAME.classList.remove('hidden');
                INDICATOR.className = 'w-2.5 h-2.5 rounded-full bg-green-400 animate-pulse';
                SCAN_STATUS.textContent = 'Scanning live...';

                await getAvailableCameras();

                scanning = true;
                requestAnimationFrame(tick);
            } catch (err) {
                console.error('Camera error:', err);
                INDICATOR.className = 'w-2.5 h-2.5 rounded-full bg-red-400';
                SCAN_STATUS.textContent = 'Camera error';
                document.getElementById('camera-error-msg').textContent = 'Camera blocked or unavailable: ' + (err.message || 'Please check browser camera permissions.');
            }
        }

        function switchCamera(deviceId) {
            stopCamera();
            startCamera(deviceId);
        }

        function stopCamera() {
            scanning = false;
            if (stream) {
                stream.getTracks().forEach(t => t.stop());
                stream = null;
            }
            VIDEO.classList.add('hidden');
            PLACEHOLDER.classList.remove('hidden');
            STOP_BTN.classList.add('hidden');
            SCAN_FRAME.classList.add('hidden');
            RESULT.classList.add('hidden');
            INDICATOR.className = 'w-2.5 h-2.5 rounded-full bg-gray-400';
            SCAN_STATUS.textContent = 'Stopped';
        }

        async function tick() {
            if (!scanning) return;

            if (VIDEO.readyState === VIDEO.HAVE_ENOUGH_DATA) {
                const ctx = CANVAS.getContext('2d');
                CANVAS.width  = VIDEO.videoWidth;
                CANVAS.height = VIDEO.videoHeight;
                ctx.drawImage(VIDEO, 0, 0, CANVAS.width, CANVAS.height);

                let qrValue = null;

                // 1. Pass 1: Try Native BarcodeDetector on original frame
                if ('BarcodeDetector' in window) {
                    try {
                        const detector = new BarcodeDetector({ formats: ['qr_code'] });
                        const barcodes = await detector.detect(CANVAS);
                        if (barcodes.length > 0) {
                            qrValue = barcodes[0].rawValue;
                        }
                    } catch(e) {}
                }

                // 2. Pass 2: Try jsQR on original frame
                if (!qrValue && typeof jsQR !== 'undefined') {
                    const imageData = ctx.getImageData(0, 0, CANVAS.width, CANVAS.height);
                    const code = jsQR(imageData.data, imageData.width, imageData.height, {
                        inversionAttempts: 'attemptBoth'
                    });
                    if (code) {
                        qrValue = code.data;
                    }
                }

                // 3. Pass 3: Test horizontally mirrored frame (handles inverted/mirrored front webcams)
                if (!qrValue && typeof jsQR !== 'undefined') {
                    const fCanvas = document.createElement('canvas');
                    fCanvas.width = CANVAS.width;
                    fCanvas.height = CANVAS.height;
                    const fctx = fCanvas.getContext('2d');
                    fctx.translate(CANVAS.width, 0);
                    fctx.scale(-1, 1);
                    fctx.drawImage(CANVAS, 0, 0);

                    const fData = fctx.getImageData(0, 0, CANVAS.width, CANVAS.height);
                    const fCode = jsQR(fData.data, fData.width, fData.height, {
                        inversionAttempts: 'attemptBoth'
                    });
                    if (fCode) {
                        qrValue = fCode.data;
                    }
                }

                if (qrValue && !debounce) {
                    debounce = true;
                    processQr(qrValue);
                }
            }

            if (scanning) {
                requestAnimationFrame(tick);
            }
        }

        function processQr(qrCode) {
            SCAN_STATUS.textContent = 'Validating...';

            fetch(CHECK_IN_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                    'Accept':       'application/json',
                },
                body: JSON.stringify({ qr_code: qrCode }),
            })
            .then(r => r.json().then(data => ({ status: r.status, body: data })))
            .then(({ status, body }) => {
                const isSuccess = status >= 200 && status < 300 && body.success;
                playBeep(isSuccess);
                showResult(body, isSuccess);

                if (isSuccess) {
                    updateStats();
                }

                SCAN_STATUS.textContent = isSuccess ? 'Checked in!' : 'Retry scan';
                setTimeout(() => {
                    RESULT.classList.add('hidden');
                    debounce = false;
                    SCAN_STATUS.textContent = 'Scanning live...';
                }, 3000);
            })
            .catch(err => {
                playBeep(false);
                showResult({ success: false, message: 'Network error or server unreachable.', type: 'error' }, false);
                setTimeout(() => {
                    RESULT.classList.add('hidden');
                    debounce = false;
                }, 3000);
            });
        }

        function showResult(data, isSuccess) {
            const iconWrap = document.getElementById('result-icon-wrap');
            const title    = document.getElementById('result-title');
            const subtitle = document.getElementById('result-subtitle');
            const detail   = document.getElementById('result-detail');

            if (isSuccess) {
                iconWrap.className = 'w-14 h-14 rounded-full flex items-center justify-center mx-auto text-2xl font-black bg-green-100 text-green-700';
                iconWrap.textContent = '✓';
                title.textContent = 'Check-in Verified!';
                subtitle.textContent = data.student ? data.student.name : 'Student Verified';
                detail.textContent = data.student
                    ? `${data.student.student_id} · ${data.student.program} · Checked in at ${data.student.checked_in_at}`
                    : '';
            } else if (data.type === 'duplicate') {
                iconWrap.className = 'w-14 h-14 rounded-full flex items-center justify-center mx-auto text-2xl font-black bg-amber-100 text-amber-700';
                iconWrap.textContent = '⚠';
                title.textContent = 'Already Checked In';
                subtitle.textContent = data.student ? data.student.name : '';
                detail.textContent = data.message;
            } else {
                iconWrap.className = 'w-14 h-14 rounded-full flex items-center justify-center mx-auto text-2xl font-black bg-red-100 text-red-700';
                iconWrap.textContent = '✕';
                title.textContent = 'Check-in Failed';
                subtitle.textContent = data.message || 'Invalid QR code.';
                detail.textContent = 'Make sure the student is registered for this specific event.';
            }

            RESULT.classList.remove('hidden');
        }

        function updateStats() {
            const checkedEl   = document.getElementById('stat-checked');
            const remainingEl = document.getElementById('stat-remaining');
            if (checkedEl) {
                checkedEl.textContent = parseInt(checkedEl.textContent) + 1;
            }
            if (remainingEl) {
                remainingEl.textContent = Math.max(0, parseInt(remainingEl.textContent) - 1);
            }
        }

        // Image file upload scanning
        function handleImageUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            const statusEl = document.getElementById('file-scan-status');
            statusEl.className = 'p-4 rounded-xl text-sm font-semibold bg-blue-50 text-blue-800 block';
            statusEl.textContent = 'Scanning uploaded image...';

            const reader = new FileReader();
            reader.onload = function(e) {
                const img = new Image();
                img.onload = async function() {
                    const canvas = document.createElement('canvas');
                    canvas.width = img.width;
                    canvas.height = img.height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0);

                    let qrFound = null;

                    // 1. Try BarcodeDetector
                    if ('BarcodeDetector' in window) {
                        try {
                            const detector = new BarcodeDetector({ formats: ['qr_code'] });
                            const barcodes = await detector.detect(canvas);
                            if (barcodes.length > 0) qrFound = barcodes[0].rawValue;
                        } catch(e) {}
                    }

                    // 2. Try jsQR
                    if (!qrFound && typeof jsQR !== 'undefined') {
                        const imgData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                        const code = jsQR(imgData.data, imgData.width, imgData.height, { inversionAttempts: 'attemptBoth' });
                        if (code) qrFound = code.data;
                    }

                    if (qrFound) {
                        statusEl.className = 'p-4 rounded-xl text-sm font-semibold bg-green-50 text-green-800 block';
                        statusEl.textContent = 'QR Code detected! Submitting check-in...';
                        processQr(qrFound);
                    } else {
                        statusEl.className = 'p-4 rounded-xl text-sm font-semibold bg-red-50 text-red-800 block';
                        statusEl.textContent = 'Could not find a QR code in the uploaded image. Please ensure the image is clear or use Manual Token entry.';
                    }
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    </script>
</x-dashboard-layout>
