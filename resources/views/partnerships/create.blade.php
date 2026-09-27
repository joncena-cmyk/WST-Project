<x-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-base font-bold text-gray-900">Initiate Partnership Proposal</h1>
            <a href="{{ route('partnerships.index') }}" class="text-xs font-bold text-comsoc-600 hover:text-comsoc-800 transition">
                ← Back to Partnerships
            </a>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <form method="POST" action="{{ route('partnerships.store') }}" class="space-y-6">
            @csrf

            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-xs space-y-4">
                <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide border-b border-gray-100 pb-3">
                    Partnership Details
                </h2>

                <div class="space-y-4">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-bold text-gray-700">Partner Organization *</label>
                            <a href="{{ route('partner-organizations.create') }}" class="text-[11px] font-bold text-comsoc-600 hover:underline">
                                + Register New Organization
                            </a>
                        </div>
                        <select name="partner_id" required class="w-full text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500">
                            <option value="">Select partner organization...</option>
                            @foreach($partners as $partner)
                                <option value="{{ $partner->id }}" {{ old('partner_id') == $partner->id ? 'selected' : '' }}>
                                    {{ $partner->name }} ({{ $partner->getTypeLabel() }})
                                </option>
                            @endforeach
                        </select>
                        @error('partner_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Partnership / Engagement Type *</label>
                            <select name="partnership_type" required class="w-full text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500">
                                <option value="co_organizer" {{ old('partnership_type') == 'co_organizer' ? 'selected' : '' }}>🤝 Joint Organizer &amp; Co-Host</option>
                                <option value="technical_support" {{ old('partnership_type') == 'technical_support' ? 'selected' : '' }}>🛠️ Technical Partner &amp; Audio-Visual / Stage Ops</option>
                                <option value="resource_speakers" {{ old('partnership_type') == 'resource_speakers' ? 'selected' : '' }}>🎤 Resource Speakers &amp; Mentorship</option>
                                <option value="sponsorship" {{ old('partnership_type') == 'sponsorship' ? 'selected' : '' }}>💰 Sponsorship &amp; Grant Support</option>
                            </select>
                            <p class="text-[11px] text-gray-400 mt-1">Select "Technical Partner" if an external org requested COMSOC for technical / AV assistance.</p>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Estimated Support Fund / Budget (₱)</label>
                            <input type="number" step="0.01" name="sponsorship_amount" value="{{ old('sponsorship_amount', '0.00') }}" placeholder="0.00"
                                   class="w-full text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500"/>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Partnership Title *</label>
                        <input type="text" name="title" value="{{ old('title') }}" required
                               placeholder="e.g. Technical Operations Partner for University Hackathon 2026"
                               class="w-full text-sm border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500"/>
                        @error('title')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Academic Year *</label>
                            <input type="text" name="academic_year" value="{{ old('academic_year', '2025-2026') }}" required
                                   class="w-full text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500"/>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Start Date</label>
                            <input type="date" name="start_date" value="{{ old('start_date') }}"
                                   class="w-full text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500"/>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">End Date</label>
                            <input type="date" name="end_date" value="{{ old('end_date') }}"
                                   class="w-full text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500"/>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Partnership Description &amp; Scope</label>
                        <textarea name="description" rows="4"
                                  placeholder="Describe the collaboration objectives, expected student benefits, mutual commitments, and planned activities..."
                                  class="w-full text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('partnerships.index') }}" class="px-5 py-2.5 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-xl">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-comsoc-700 hover:bg-comsoc-800 text-white text-xs font-bold rounded-xl shadow-xs">
                    Create Partnership Proposal
                </button>
            </div>
        </form>
    </div>
</x-dashboard-layout>
