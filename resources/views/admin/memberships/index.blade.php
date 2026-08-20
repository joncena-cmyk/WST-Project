<x-dashboard-layout>
    <x-slot name="header">
        <h1 class="text-lg font-semibold text-gray-800">Membership Management</h1>
    </x-slot>

    <div class="space-y-5">

        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition
                 class="flex items-center gap-3 p-4 bg-comsoc-50 border border-comsoc-200 rounded-xl text-comsoc-700 text-sm">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Header row --}}
        <div class="flex items-center justify-between">
            <p class="text-sm text-gray-500">
                {{ $memberships->total() }} membership{{ $memberships->total() !== 1 ? 's' : '' }} found
            </p>
            <a href="{{ route('admin.memberships.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-comsoc-600 text-white text-sm font-semibold rounded-lg hover:bg-comsoc-700 transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Membership
            </a>
        </div>

        {{-- Filters --}}
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
            <form method="GET" class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[160px]">
                    <x-input-label for="search" value="Search" />
                    <x-text-input id="search" name="search" type="text"
                                  :value="request('search')"
                                  placeholder="Name or student ID..."
                                  class="mt-1 block w-full text-sm" />
                </div>
                <div>
                    <x-input-label for="status" value="Status" />
                    <select id="status" name="status"
                            class="mt-1 border-gray-300 focus:border-comsoc-500 focus:ring-comsoc-500 rounded-md shadow-sm text-sm">
                        <option value="">All statuses</option>
                        @foreach(['pending','active','expired','suspended'] as $s)
                            <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                @if($academicYears->isNotEmpty())
                    <div>
                        <x-input-label for="academic_year" value="Academic Year" />
                        <select id="academic_year" name="academic_year"
                                class="mt-1 border-gray-300 focus:border-comsoc-500 focus:ring-comsoc-500 rounded-md shadow-sm text-sm">
                            <option value="">All years</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year }}" {{ request('academic_year') === $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="flex gap-2">
                    <x-primary-button class="text-sm py-2">Filter</x-primary-button>
                    @if(request()->hasAny(['search','status','academic_year']))
                        <a href="{{ route('admin.memberships.index') }}"
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-600 hover:bg-gray-50">
                            Clear
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            @foreach(['Member','Academic Year','Program','Status','Dates','Approved By',''] as $col)
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ $col }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($memberships as $m)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $m->user->getProfilePictureUrl() }}"
                                             class="w-8 h-8 rounded-full object-cover border border-gray-200 shrink-0" alt=""/>
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $m->user->name }}</p>
                                            <p class="text-xs text-gray-400">{{ $m->user->student_id ?? $m->user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm font-semibold text-gray-800">{{ $m->academic_year }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $m->program?->code ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    @php $mColors = ['active'=>'bg-green-100 text-green-700','expired'=>'bg-gray-100 text-gray-500','pending'=>'bg-yellow-100 text-yellow-700','suspended'=>'bg-red-100 text-red-700']; @endphp
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $mColors[$m->status] ?? 'bg-gray-100' }}">
                                        {{ ucfirst($m->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-500">
                                    @if($m->start_date)
                                        {{ $m->start_date->format('M d, Y') }}
                                        @if($m->end_date) — {{ $m->end_date->format('M d, Y') }} @endif
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-500">{{ $m->approvedBy?->name ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('admin.memberships.edit', $m) }}"
                                       class="text-xs font-semibold text-comsoc-600 hover:text-comsoc-800 transition-colors">
                                        Edit →
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center text-gray-400 text-sm">No memberships found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div>{{ $memberships->links() }}</div>
    </div>
</x-dashboard-layout>
