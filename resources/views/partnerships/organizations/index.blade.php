<x-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-base font-bold text-gray-900">Partner Organizations Directory</h1>
                <p class="text-xs text-gray-500">Corporate, academic, and community partners collaborating with COMSOC</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('partnerships.index') }}"
                   class="px-3.5 py-1.5 bg-white hover:bg-gray-50 text-gray-700 text-xs font-bold rounded-xl border border-gray-300 transition shadow-2xs flex items-center gap-1.5">
                    ← Back to Partnerships
                </a>
                @can('create', App\Models\PartnerOrganization::class)
                    <a href="{{ route('partner-organizations.create') }}"
                       class="px-4 py-1.5 bg-comsoc-700 hover:bg-comsoc-800 text-white text-xs font-bold rounded-xl transition shadow-xs flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Organization
                    </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">

        {{-- ===== FILTER & SEARCH ===== --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-xs">
            <form method="GET" action="{{ route('partner-organizations.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-gray-600 mb-1">Search Organization</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Search organization name, contact person, email..."
                           class="w-full text-xs border border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500"/>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Type</label>
                    <select name="type" class="w-full text-xs border border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500">
                        <option value="">All Types</option>
                        <option value="industry" {{ request('type') === 'industry' ? 'selected' : '' }}>Industry / Tech Partner</option>
                        <option value="academic" {{ request('type') === 'academic' ? 'selected' : '' }}>Academic Institution</option>
                        <option value="student_org" {{ request('type') === 'student_org' ? 'selected' : '' }}>Student Organization</option>
                        <option value="government" {{ request('type') === 'government' ? 'selected' : '' }}>Government Agency</option>
                        <option value="ngo" {{ request('type') === 'ngo' ? 'selected' : '' }}>Non-Profit / NGO</option>
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit"
                            class="px-4 py-2 bg-comsoc-700 hover:bg-comsoc-800 text-white text-xs font-bold rounded-xl transition shadow-xs w-full sm:w-auto">
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'type']))
                        <a href="{{ route('partner-organizations.index') }}"
                           class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-bold rounded-xl transition">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- ===== ORGANIZATIONS GRID ===== --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($organizations as $org)
                <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-xs hover:border-comsoc-300 transition flex flex-col justify-between space-y-4">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $org->getTypeBadgeColor() }}">
                                {{ $org->getTypeLabel() }}
                            </span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $org->status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ ucfirst($org->status) }}
                            </span>
                        </div>

                        <h3 class="text-base font-bold text-gray-900 leading-snug">{{ $org->name }}</h3>

                        @if($org->description)
                            <p class="text-xs text-gray-600 line-clamp-2 leading-relaxed">{{ $org->description }}</p>
                        @endif

                        <div class="space-y-1 text-xs text-gray-500 pt-1">
                            @if($org->contact_person)
                                <p class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    {{ $org->contact_person }}
                                </p>
                            @endif
                            @if($org->contact_email)
                                <p class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $org->contact_email }}
                                </p>
                            @endif
                            @if($org->website)
                                <p class="flex items-center gap-1.5 truncate">
                                    <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                                    </svg>
                                    <a href="{{ $org->website }}" target="_blank" rel="noopener" class="text-blue-600 hover:underline truncate">
                                        {{ $org->website }}
                                    </a>
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-2 border-t border-gray-100 text-xs">
                        <span class="text-gray-400 font-medium">{{ $org->partnerships_count }} Partnerships</span>
                        <div class="flex items-center gap-2">
                            @can('update', $org)
                                <a href="{{ route('partner-organizations.edit', $org) }}" class="text-gray-500 hover:text-gray-700 font-semibold">
                                    Edit
                                </a>
                            @endcan
                            <a href="{{ route('partner-organizations.show', $org) }}" class="text-comsoc-700 hover:text-comsoc-900 font-bold">
                                View Profile →
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-3 bg-white rounded-2xl border border-gray-200 p-12 text-center shadow-xs">
                    <p class="text-gray-800 font-bold text-base">No partner organizations found.</p>
                </div>
            @endforelse
        </div>

        @if($organizations->hasPages())
            <div class="pt-2">
                {{ $organizations->links() }}
            </div>
        @endif

    </div>
</x-dashboard-layout>
