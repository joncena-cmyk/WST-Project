<x-dashboard-layout>
    <x-slot name="header">
        <h1 class="text-base font-bold text-gray-900">My Membership</h1>
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-6 pb-8">

        {{-- Flash messages --}}
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition
                 class="flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-700 text-sm shadow-sm">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition
                 class="flex items-center gap-3 p-4 bg-red-50 border border-red-200 rounded-2xl text-red-700 text-sm shadow-sm">
                <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        @endif

        {{-- 1. ACTIVE MEMBERSHIP CARD --}}
        @if ($active)
            <div class="bg-gradient-to-br from-comsoc-950 via-comsoc-900 to-comsoc-800 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-gold-600 via-gold-400 to-gold-600"></div>
                <div class="absolute -top-12 -right-12 w-48 h-48 rounded-full border-2 border-gold-400/10 pointer-events-none"></div>
                <div class="absolute -bottom-8 -left-8 w-36 h-36 rounded-full border-2 border-white/5 pointer-events-none"></div>

                <div class="relative z-10">
                    <div class="flex items-start justify-between gap-4 mb-6">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-2xl bg-white/10 backdrop-blur-md border border-gold-400/40 p-2 flex items-center justify-center shrink-0 shadow-inner">
                                <img src="{{ asset('images/comsoc_logo.png') }}" alt="COMSOC" class="w-full h-full object-contain"/>
                            </div>
                            <div>
                                <p class="text-xs text-gold-400 font-bold uppercase tracking-widest">Official Member</p>
                                <h2 class="text-xl sm:text-2xl font-black text-white mt-0.5">{{ Auth::user()->name }}</h2>
                                @if(Auth::user()->student_id)
                                    <p class="text-comsoc-300 text-xs mt-0.5">{{ Auth::user()->student_id }} · {{ $active->program?->code ?? Auth::user()->program?->code ?? 'CCIS' }}</p>
                                @endif
                            </div>
                        </div>

                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-emerald-500/20 border border-emerald-400/40 text-emerald-300 text-xs font-black shadow-xs">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            Active
                        </span>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 border-t border-white/10 pt-5 text-xs">
                        <div>
                            <p class="text-comsoc-400 font-medium">Academic Year</p>
                            <p class="text-sm font-bold text-white mt-0.5">{{ $active->academic_year }}</p>
                        </div>
                        <div>
                            <p class="text-comsoc-400 font-medium">Program</p>
                            <p class="text-sm font-bold text-white mt-0.5">{{ $active->program?->code ?? 'CCIS' }}</p>
                        </div>
                        <div>
                            <p class="text-comsoc-400 font-medium">Valid Until</p>
                            <p class="text-sm font-bold text-white mt-0.5">{{ $active->end_date?->format('M d, Y') ?? 'End of A.Y.' }}</p>
                        </div>
                        <div>
                            <p class="text-comsoc-400 font-medium">Verified By</p>
                            <p class="text-sm font-bold text-gold-400 mt-0.5 truncate">{{ $active->approvedBy?->name ?? 'Treasurer' }}</p>
                        </div>
                    </div>
                </div>
            </div>

        {{-- 2. PENDING MEMBERSHIP CARD --}}
        @elseif ($pending)
            <div class="bg-amber-50/70 border-2 border-amber-300 rounded-3xl p-6 sm:p-7 shadow-sm relative overflow-hidden">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0 border border-amber-200">
                        <svg class="w-6 h-6 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center justify-between gap-2 flex-wrap">
                            <h3 class="text-base font-bold text-amber-900">Membership Application Under Review</h3>
                            <span class="px-2.5 py-1 rounded-full bg-amber-200 text-amber-800 text-[11px] font-black uppercase tracking-wide">
                                Pending Verification
                            </span>
                        </div>
                        <p class="text-xs text-amber-800 mt-1 leading-relaxed">
                            Your membership application for Academic Year <strong>{{ $pending->academic_year }}</strong> is currently queued for payment verification by the Treasurer Desk.
                        </p>

                        <div class="mt-4 pt-3 border-t border-amber-200/60 grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs">
                            <div>
                                <span class="text-amber-700 font-medium">Payment Method:</span>
                                <p class="font-bold text-amber-950 uppercase">{{ $pending->payment_method ?? 'GCash' }}</p>
                            </div>
                            <div>
                                <span class="text-amber-700 font-medium">Reference No:</span>
                                <p class="font-bold text-amber-950">{{ $pending->payment_reference ?? '—' }}</p>
                            </div>
                            <div>
                                <span class="text-amber-700 font-medium">Submitted:</span>
                                <p class="font-bold text-amber-950">{{ $pending->created_at->format('M d, Y · g:i A') }}</p>
                            </div>
                        </div>

                        @if($pending->proof_of_payment_path)
                            <div class="mt-4 flex items-center gap-3">
                                <a href="{{ asset('storage/' . $pending->proof_of_payment_path) }}" target="_blank"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-amber-300 rounded-xl text-xs font-bold text-amber-900 hover:bg-amber-100 transition">
                                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span>View Uploaded Proof of Payment</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        {{-- 3. MEMBERSHIP APPLICATION FORM (Self-service online application) --}}
        @else
            <div class="bg-white border border-gray-200 rounded-3xl p-6 sm:p-8 shadow-sm">
                <div class="flex items-center gap-3.5 mb-6 pb-5 border-b border-gray-100">
                    <div class="w-12 h-12 rounded-2xl bg-comsoc-50 text-comsoc-700 flex items-center justify-center shrink-0 border border-comsoc-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Apply for COMSOC Membership</h2>
                        <p class="text-xs text-gray-500 mt-0.5">Academic Year 2026 – 2027 · CCIS University of Makati</p>
                    </div>
                </div>

                {{-- Membership Fee & GCash Instructions Box --}}
                <div class="mb-6 p-4 bg-gradient-to-r from-comsoc-900 to-comsoc-800 rounded-2xl text-white">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-gold-400">Official Membership Dues</span>
                            <h4 class="text-xl font-black text-white">₱50.00 <span class="text-xs font-normal text-comsoc-200">/ Academic Year</span></h4>
                        </div>
                        <div class="text-xs text-comsoc-100 leading-relaxed bg-white/10 p-3 rounded-xl border border-white/10">
                            <p class="font-bold text-gold-300 mb-0.5">Payment Instructions (GCash):</p>
                            <p>Send ₱50 to <strong>0917-123-4567</strong> (COMSOC Treasurer)</p>
                            <p class="text-[11px] text-comsoc-300 mt-0.5">Please take a screenshot of your receipt and enter the reference number below.</p>
                        </div>
                    </div>
                </div>

                {{-- Application Form --}}
                <form method="POST" action="{{ route('membership.apply') }}" enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Degree Program <span class="text-red-500">*</span>
                        </label>
                        <select name="program_id" required
                                class="w-full text-sm border-gray-300 rounded-xl focus:ring-comsoc-500 focus:border-comsoc-500 shadow-xs">
                            <option value="">Select your program...</option>
                            @foreach ($programs as $prog)
                                <option value="{{ $prog->id }}" {{ (old('program_id') == $prog->id || Auth::user()->program_id == $prog->id) ? 'selected' : '' }}>
                                    {{ $prog->name }} ({{ $prog->code }})
                                </option>
                            @endforeach
                        </select>
                        @error('program_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                Payment Method <span class="text-red-500">*</span>
                            </label>
                            <select name="payment_method" required
                                    class="w-full text-sm border-gray-300 rounded-xl focus:ring-comsoc-500 focus:border-comsoc-500 shadow-xs">
                                <option value="gcash" {{ old('payment_method') === 'gcash' ? 'selected' : '' }}>GCash (Recommended)</option>
                                <option value="cash" {{ old('payment_method') === 'cash' ? 'selected' : '' }}>Cash (Direct to Officer)</option>
                                <option value="bank_transfer" {{ old('payment_method') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer / Maya</option>
                            </select>
                            @error('payment_method')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                Payment Reference Number
                            </label>
                            <input type="text" name="payment_reference" value="{{ old('payment_reference') }}"
                                   placeholder="e.g. 10023485721"
                                   class="w-full text-sm border-gray-300 rounded-xl focus:ring-comsoc-500 focus:border-comsoc-500 shadow-xs"/>
                            @error('payment_reference')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Upload Proof of Payment (Screenshot / Receipt) <span class="text-red-500">*</span>
                        </label>
                        <input type="file" name="proof_of_payment" required accept="image/png,image/jpeg,image/webp"
                               class="w-full text-xs text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-comsoc-50 file:text-comsoc-700 hover:file:bg-comsoc-100 border border-gray-300 rounded-xl p-1.5"/>
                        <p class="text-[11px] text-gray-400 mt-1">Accepted formats: JPG, PNG, WEBP (Max 4MB)</p>
                        @error('proof_of_payment')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Notes / Remarks (Optional)
                        </label>
                        <textarea name="notes" rows="2" placeholder="Any extra notes for the Treasurer..."
                                  class="w-full text-sm border-gray-300 rounded-xl focus:ring-comsoc-500 focus:border-comsoc-500 shadow-xs">{{ old('notes') }}</textarea>
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                                class="w-full sm:w-auto px-6 py-3 bg-comsoc-600 hover:bg-comsoc-700 text-white font-bold text-sm rounded-xl shadow-md transition-all flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Submit Membership Application</span>
                        </button>
                    </div>
                </form>
            </div>
        @endif

        {{-- 4. MEMBERSHIP HISTORY --}}
        @if ($memberships->isNotEmpty())
            <div class="bg-white border border-gray-200 rounded-3xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-gray-900">Membership Records</h3>
                    <span class="text-xs text-gray-400 font-medium">{{ $memberships->count() }} total record{{ $memberships->count() === 1 ? '' : 's' }}</span>
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach ($memberships as $m)
                        <div class="flex items-center justify-between px-6 py-4">
                            <div class="flex items-center gap-3.5">
                                <div class="flex items-center justify-center w-10 h-10 rounded-xl
                                            {{ $m->status === 'active' ? 'bg-emerald-50 text-emerald-600' :
                                               ($m->status === 'pending' ? 'bg-amber-50 text-amber-600' :
                                               ($m->status === 'rejected' ? 'bg-red-50 text-red-600' : 'bg-gray-50 text-gray-400')) }} shrink-0 border border-gray-100">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900">Academic Year {{ $m->academic_year }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        {{ $m->program?->code ?? 'CCIS' }}
                                        @if($m->payment_method) · {{ ucfirst($m->payment_method) }} @endif
                                        @if($m->payment_reference) (Ref: {{ $m->payment_reference }}) @endif
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                @php
                                    $mColors = [
                                        'active'    => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                        'expired'   => 'bg-gray-100 text-gray-600 border-gray-200',
                                        'pending'   => 'bg-amber-100 text-amber-800 border-amber-200',
                                        'suspended' => 'bg-red-100 text-red-800 border-red-200',
                                        'rejected'  => 'bg-red-100 text-red-800 border-red-200',
                                    ];
                                @endphp
                                <span class="inline-flex px-3 py-0.5 rounded-full text-xs font-bold border {{ $mColors[$m->status] ?? 'bg-gray-100 text-gray-500' }}">
                                    {{ ucfirst($m->status) }}
                                </span>
                                @if($m->approvedBy)
                                    <p class="text-[11px] text-gray-400 mt-1">Verified by {{ $m->approvedBy->name }}</p>
                                @endif
                                @if($m->rejection_reason)
                                    <p class="text-[11px] text-red-500 mt-1 font-medium">Reason: {{ $m->rejection_reason }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</x-dashboard-layout>
