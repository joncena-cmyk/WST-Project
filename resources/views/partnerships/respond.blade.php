<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Official Collaboration Proposal — {{ $partnership->partner->name }} x COMSOC</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-900 bg-slate-900 min-h-full py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto space-y-6">

        {{-- Top Brand Bar --}}
        <div class="flex items-center justify-between text-white/80 text-xs">
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 rounded-lg bg-indigo-500/20 text-indigo-300 font-bold border border-indigo-400/30">
                    Official Partner Portal
                </span>
                <span class="font-mono text-slate-400">Ref: COL-{{ $partnership->academic_year }}-{{ str_pad((string)$partnership->id, 4, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div>
                <span>University of Makati · CCIS</span>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-sm font-semibold flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-base shrink-0">✓</div>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-sm font-semibold space-y-1">
                @foreach($errors->all() as $err)
                    <p class="flex items-center gap-2">
                        <span class="text-rose-400">✕</span> {{ $err }}
                    </p>
                @endforeach
            </div>
        @endif

        {{-- Main Document Card --}}
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-slate-200">
            
            {{-- Formal Header / Letterhead --}}
            <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 p-8 text-center text-white space-y-1.5 border-b border-indigo-900/40">
                <p class="text-[11px] font-bold text-indigo-300 uppercase tracking-widest">Republic of the Philippines</p>
                <h1 class="text-xl sm:text-2xl font-black text-white uppercase tracking-wider">University of Makati</h1>
                <p class="text-xs sm:text-sm font-bold text-slate-300">College of Computing and Information Sciences</p>
                <p class="text-xs sm:text-sm font-black text-indigo-400">Computer Society (COMSOC)</p>
                <p class="text-[11px] text-slate-400">J.P. Rizal Ext., West Rembo, Taguig City / Makati City</p>
            </div>

            <div class="p-6 sm:p-10 space-y-8">

                {{-- Status & Summary Ribbon --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 rounded-2xl bg-slate-50 border border-slate-200">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Proposal Addressed To</span>
                        <h2 class="text-base font-black text-slate-900">{{ $partnership->partner->name }}</h2>
                        <p class="text-xs text-slate-600">Contact: <strong>{{ $partnership->partner->contact_person ?? 'Executive Board' }}</strong> ({{ $partnership->partner->contact_email ?? 'No email registered' }})</p>
                    </div>

                    <div class="sm:text-right shrink-0">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold {{ $partnership->getPartnerResponseStatusBadge() }}">
                            {{ $partnership->getPartnerResponseStatusLabel() }}
                        </span>
                        <p class="text-[11px] text-slate-500 mt-1">A.Y. {{ $partnership->academic_year }}</p>
                    </div>
                </div>

                {{-- Key Details Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="p-4 rounded-xl bg-indigo-50/60 border border-indigo-100">
                        <span class="text-[10px] font-bold uppercase text-indigo-600 tracking-wider">Engagement Type</span>
                        <p class="text-xs font-black text-indigo-950 mt-0.5">{{ $partnership->getPartnershipTypeLabel() }}</p>
                    </div>
                    <div class="p-4 rounded-xl bg-purple-50/60 border border-purple-100">
                        <span class="text-[10px] font-bold uppercase text-purple-600 tracking-wider">Activity / Title</span>
                        <p class="text-xs font-black text-purple-950 mt-0.5">{{ $partnership->title }}</p>
                    </div>
                    <div class="p-4 rounded-xl bg-emerald-50/60 border border-emerald-100">
                        <span class="text-[10px] font-bold uppercase text-emerald-600 tracking-wider">Support / Financial Fund</span>
                        <p class="text-xs font-black text-emerald-950 mt-0.5">
                            {{ $partnership->sponsorship_amount > 0 ? 'PHP ' . number_format($partnership->sponsorship_amount, 2) : 'Joint Collaboration / Non-Financial' }}
                        </p>
                    </div>
                </div>

                {{-- Letter of Intent Body --}}
                <div class="space-y-3">
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-500">Official Letter of Intent &amp; Activity Proposal</h3>
                    <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-xs whitespace-pre-line text-xs sm:text-sm text-slate-800 leading-relaxed font-sans">
{{ $partnership->getDefaultLetterContent() }}
                    </div>
                </div>

                {{-- Signatures Preview --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4 border-t border-slate-200 text-xs">
                    <div class="space-y-0.5">
                        <p class="text-[10px] font-bold uppercase text-slate-400">Prepared &amp; Proposed By:</p>
                        <p class="font-black text-slate-900">{{ $partnership->creator->name ?? 'COMSOC Officer' }}</p>
                        <p class="text-[11px] text-slate-500">{{ $partnership->creator?->getRoleLabel() ?? 'Officer' }}, Computer Society (COMSOC)</p>
                    </div>

                    <div class="space-y-0.5 sm:text-right">
                        <p class="text-[10px] font-bold uppercase text-slate-400">Academic Adviser Endorsement:</p>
                        @if($partnership->endorsed_by)
                            <p class="font-black text-emerald-700">✍️ {{ $partnership->endorser->name ?? 'Academic Adviser' }}</p>
                            <p class="text-[11px] text-emerald-600">Endorsed on {{ $partnership->endorsed_at?->format('M d, Y') }}</p>
                        @else
                            <p class="italic text-slate-400 text-xs">Under Review / Endorsement by Faculty</p>
                        @endif
                    </div>
                </div>

                {{-- ===== RESPONSE SECTION ===== --}}
                <div class="pt-6 border-t-2 border-slate-200 space-y-6">

                    @if($partnership->partner_responded_at)
                        {{-- Already Responded Banner --}}
                        <div class="p-6 rounded-2xl {{ $partnership->isPartnerAccepted() ? 'bg-emerald-50 border-2 border-emerald-500/40 text-emerald-950' : 'bg-amber-50 border-2 border-amber-500/40 text-amber-950' }} space-y-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl {{ $partnership->isPartnerAccepted() ? 'bg-emerald-600 text-white' : 'bg-amber-600 text-white' }} flex items-center justify-center font-black text-lg shadow-sm">
                                    {{ $partnership->isPartnerAccepted() ? '✓' : '📝' }}
                                </div>
                                <div>
                                    <h4 class="text-base font-black">
                                        {{ $partnership->isPartnerAccepted() ? 'Collaboration Formally Accepted & Confirmed' : 'Response Submitted to COMSOC' }}
                                    </h4>
                                    <p class="text-xs {{ $partnership->isPartnerAccepted() ? 'text-emerald-800' : 'text-amber-800' }}">
                                        Signed by <strong>{{ $partnership->partner_responder_name }}</strong> ({{ $partnership->partner_responder_title ?? 'Representative' }}) on {{ $partnership->partner_responded_at->format('F d, Y · h:i A') }}
                                    </p>
                                </div>
                            </div>

                            @if($partnership->partner_response_remarks)
                                <div class="bg-white/80 p-4 rounded-xl border border-emerald-200 text-xs text-slate-800 leading-relaxed">
                                    <strong>Partner Remarks / Notes:</strong><br>
                                    {{ $partnership->partner_response_remarks }}
                                </div>
                            @endif
                        </div>
                    @else
                        {{-- Interactive Response Form --}}
                        <div class="bg-gradient-to-br from-indigo-50/80 via-purple-50/50 to-slate-50 p-6 sm:p-8 rounded-3xl border border-indigo-200/80 shadow-xs space-y-5"
                             x-data="{ decision: 'accepted' }">
                            <div>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-600 text-white uppercase tracking-wider">
                                    Partner Organization Action
                                </span>
                                <h3 class="text-lg font-black text-slate-900 mt-1">Official Response &amp; Letter Confirmation</h3>
                                <p class="text-xs text-slate-600">Please select your organization's decision regarding this collaboration with University of Makati COMSOC.</p>
                            </div>

                            <form method="POST" action="{{ route('partnerships.respond.submit', ['partnership' => $partnership->id, 'token' => $token]) }}"
                                  enctype="multipart/form-data" class="space-y-5">
                                @csrf

                                {{-- Decision Radios --}}
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <label class="relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition shadow-2xs"
                                           :class="decision === 'accepted' ? 'bg-emerald-50/80 border-emerald-500 text-emerald-950' : 'bg-white border-slate-200 text-slate-700 hover:border-slate-300'">
                                        <div class="flex items-center justify-between">
                                            <input type="radio" name="response_status" value="accepted" x-model="decision" class="text-emerald-600 focus:ring-emerald-500">
                                            <span class="text-base">🎉</span>
                                        </div>
                                        <span class="text-xs font-black mt-2">Accept &amp; Confirm</span>
                                        <span class="text-[10px] text-slate-500 mt-0.5">Authorize joint collaboration with COMSOC.</span>
                                    </label>

                                    <label class="relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition shadow-2xs"
                                           :class="decision === 'changes_requested' ? 'bg-amber-50/80 border-amber-500 text-amber-950' : 'bg-white border-slate-200 text-slate-700 hover:border-slate-300'">
                                        <div class="flex items-center justify-between">
                                            <input type="radio" name="response_status" value="changes_requested" x-model="decision" class="text-amber-600 focus:ring-amber-500">
                                            <span class="text-base">📝</span>
                                        </div>
                                        <span class="text-xs font-black mt-2">Request Revisions</span>
                                        <span class="text-[10px] text-slate-500 mt-0.5">Suggest date/agenda modifications.</span>
                                    </label>

                                    <label class="relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition shadow-2xs"
                                           :class="decision === 'declined' ? 'bg-rose-50/80 border-rose-500 text-rose-950' : 'bg-white border-slate-200 text-slate-700 hover:border-slate-300'">
                                        <div class="flex items-center justify-between">
                                            <input type="radio" name="response_status" value="declined" x-model="decision" class="text-rose-600 focus:ring-rose-500">
                                            <span class="text-base">✕</span>
                                        </div>
                                        <span class="text-xs font-black mt-2">Decline Proposal</span>
                                        <span class="text-[10px] text-slate-500 mt-0.5">Unable to partner at this time.</span>
                                    </label>
                                </div>

                                {{-- Representative Info --}}
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-800 mb-1">Authorized Representative Name *</label>
                                        <input type="text" name="responder_name" required
                                               value="{{ old('responder_name', $partnership->partner->contact_person) }}"
                                               placeholder="e.g. John Doe"
                                               class="w-full text-xs border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 bg-white shadow-2xs"/>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-800 mb-1">Designation / Role in Organization</label>
                                        <input type="text" name="responder_title"
                                               value="{{ old('responder_title') }}"
                                               placeholder="e.g. President, External Lead, Director"
                                               class="w-full text-xs border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 bg-white shadow-2xs"/>
                                    </div>
                                </div>

                                {{-- Remarks / Message --}}
                                <div>
                                    <label class="block text-xs font-bold text-slate-800 mb-1">Remarks / Endorsement Message (Optional)</label>
                                    <textarea name="remarks" rows="3"
                                              placeholder="Add remarks, preferred schedule, speaker confirmations, or notes for the COMSOC Executive Board..."
                                              class="w-full text-xs border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 bg-white shadow-2xs"></textarea>
                                </div>

                                {{-- Optional Signed Document Upload --}}
                                <div>
                                    <label class="block text-xs font-bold text-slate-800 mb-1">Attach Signed MoA / Formal Endorsement File (Optional)</label>
                                    <input type="file" name="signed_document"
                                           class="w-full text-xs border border-slate-300 bg-white rounded-xl file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 shadow-2xs"/>
                                    <p class="text-[10px] text-slate-500 mt-1">Accepted formats: PDF, DOC, DOCX, PNG, JPG (Max 10MB)</p>
                                </div>

                                <div class="pt-2 flex justify-end">
                                    <button type="submit"
                                            class="w-full sm:w-auto px-8 py-3 rounded-xl font-black text-xs text-white shadow-md transition flex items-center justify-center gap-2"
                                            :class="decision === 'accepted' ? 'bg-emerald-600 hover:bg-emerald-700' : (decision === 'changes_requested' ? 'bg-amber-600 hover:bg-amber-700' : 'bg-rose-600 hover:bg-rose-700')">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Submit Official Response to COMSOC
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif

                </div>

            </div>

            {{-- Footer --}}
            <div class="bg-slate-50 p-6 text-center text-slate-500 text-xs border-t border-slate-200">
                <p class="font-bold text-slate-700">University of Makati — Computer Society (COMSOC)</p>
                <p class="text-[11px] text-slate-400 mt-0.5">College of Computing and Information Sciences · Collaboration &amp; External Partnerships Portal</p>
            </div>
        </div>

    </div>
</body>
</html>
