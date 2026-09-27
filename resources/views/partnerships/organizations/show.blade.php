<x-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <a href="{{ route('partner-organizations.index') }}" class="text-xs font-bold text-comsoc-600 hover:text-comsoc-800 transition">
                ← Back to Directory
            </a>
            <div class="flex items-center gap-2">
                @can('update', $partnerOrganization)
                    <a href="{{ route('partner-organizations.edit', $partnerOrganization) }}"
                       class="px-3.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition">
                        Edit Profile
                    </a>
                @endcan
                @can('create', App\Models\Partnership::class)
                    <a href="{{ route('partnerships.create', ['partner_id' => $partnerOrganization->id]) }}"
                       class="px-4 py-1.5 bg-comsoc-700 hover:bg-comsoc-800 text-white text-xs font-bold rounded-xl transition shadow-xs flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Initiate Partnership
                    </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">

        {{-- ===== ORG PROFILE CARD ===== --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-xs space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                <div class="space-y-2 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $partnerOrganization->getTypeBadgeColor() }}">
                            {{ $partnerOrganization->getTypeLabel() }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $partnerOrganization->status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ ucfirst($partnerOrganization->status) }}
                        </span>
                    </div>

                    <h1 class="text-xl sm:text-2xl font-black text-gray-900 leading-tight">{{ $partnerOrganization->name }}</h1>

                    @if($partnerOrganization->description)
                        <p class="text-xs text-gray-600 leading-relaxed max-w-3xl">{{ $partnerOrganization->description }}</p>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 pt-4 border-t border-gray-100 text-xs">
                <div>
                    <p class="text-gray-400 font-medium">Contact Person</p>
                    <p class="font-bold text-gray-800 mt-0.5">{{ $partnerOrganization->contact_person ?? 'Not specified' }}</p>
                </div>
                <div>
                    <p class="text-gray-400 font-medium">Contact Email</p>
                    <p class="font-bold text-gray-800 mt-0.5">{{ $partnerOrganization->contact_email ?? 'Not specified' }}</p>
                </div>
                <div>
                    <p class="text-gray-400 font-medium">Website</p>
                    @if($partnerOrganization->website)
                        <a href="{{ $partnerOrganization->website }}" target="_blank" rel="noopener" class="font-bold text-blue-600 hover:underline mt-0.5 block truncate">
                            {{ $partnerOrganization->website }}
                        </a>
                    @else
                        <p class="font-bold text-gray-400 mt-0.5">None</p>
                    @endif
                </div>
                <div>
                    <p class="text-gray-400 font-medium">Assigned Representative</p>
                    <p class="font-bold text-gray-800 mt-0.5">
                        {{ $partnerOrganization->assignedOfficer ? $partnerOrganization->assignedOfficer->name : 'Unassigned' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- ===== PARTNERSHIP HISTORY ===== --}}
        <div class="space-y-4">
            <h2 class="text-base font-bold text-gray-900">Partnership History &amp; Agreements</h2>

            <div class="space-y-3">
                @forelse($partnerOrganization->partnerships as $partnership)
                    <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $partnership->getStatusColor() }}">
                                    {{ $partnership->getStatusLabel() }}
                                </span>
                                <span class="text-xs text-gray-400">AY {{ $partnership->academic_year }}</span>
                            </div>
                            <h3 class="text-sm font-bold text-gray-900">{{ $partnership->title }}</h3>
                            <p class="text-xs text-gray-500">
                                {{ $partnership->start_date ? $partnership->start_date->format('M d, Y') : 'TBD' }}
                                – {{ $partnership->end_date ? $partnership->end_date->format('M d, Y') : 'TBD' }}
                                · {{ $partnership->activities->count() }} Activities · {{ $partnership->documents->count() }} Documents
                            </p>
                        </div>

                        <a href="{{ route('partnerships.show', $partnership) }}"
                           class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition self-start sm:self-auto">
                            View Partnership →
                        </a>
                    </div>
                @empty
                    <div class="bg-white rounded-2xl border border-gray-200 p-8 text-center text-xs text-gray-400 italic">
                        No partnerships recorded with this organization yet.
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</x-dashboard-layout>
