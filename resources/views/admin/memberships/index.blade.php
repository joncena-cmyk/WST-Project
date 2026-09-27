<x-dashboard-layout>
    <x-slot name="header">
        <h1 class="text-base font-bold text-gray-900">Membership Management</h1>
    </x-slot>

    <div class="space-y-5 pb-8">

        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition
                 class="flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-700 text-sm shadow-sm">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        {{-- Top action bar --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-bold text-gray-900">Membership Registry & Verification</h2>
                <p class="text-xs text-gray-500 mt-0.5">Manage member rosters, verify GCash dues payments, and track academic year activations.</p>
            </div>
            <a href="{{ route('admin.memberships.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-comsoc-600 hover:bg-comsoc-700 text-white text-xs font-bold rounded-xl shadow-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Add Membership Record</span>
            </a>
        </div>

        {{-- Filters & Search --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-sm">
            <form method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-5">
                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Search Member</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Search by student name, ID, or email..."
                           class="w-full text-xs border-gray-300 rounded-xl focus:ring-comsoc-500 focus:border-comsoc-500 shadow-xs"/>
                </div>

                <div class="sm:col-span-3">
                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Status</label>
                    <select name="status"
                            class="w-full text-xs border-gray-300 rounded-xl focus:ring-comsoc-500 focus:border-comsoc-500 shadow-xs">
                        <option value="">All Statuses</option>
                        @foreach(['pending' => 'Pending Verification', 'active' => 'Active', 'rejected' => 'Rejected', 'expired' => 'Expired', 'suspended' => 'Suspended'] as $key => $label)
                            <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                @if($academicYears->isNotEmpty())
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Academic Year</label>
                        <select name="academic_year"
                                class="w-full text-xs border-gray-300 rounded-xl focus:ring-comsoc-500 focus:border-comsoc-500 shadow-xs">
                            <option value="">All Years</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year }}" {{ request('academic_year') === $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="sm:col-span-2 flex gap-2">
                    <button type="submit"
                            class="w-full py-2.5 px-4 bg-comsoc-800 hover:bg-comsoc-900 text-white text-xs font-bold rounded-xl shadow-xs transition-colors">
                        Filter
                    </button>
                    @if(request()->hasAny(['search','status','academic_year']))
                        <a href="{{ route('admin.memberships.index') }}"
                           class="py-2.5 px-3 border border-gray-300 rounded-xl text-xs font-semibold text-gray-600 hover:bg-gray-50 transition-colors">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden" x-data="{ rejectModal: false, activeMembershipId: null, activeStudentName: '' }">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-left">
                    <thead class="bg-gray-50 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-3.5">Student / Member</th>
                            <th class="px-4 py-3.5">Program</th>
                            <th class="px-4 py-3.5">Academic Year</th>
                            <th class="px-4 py-3.5">Status</th>
                            <th class="px-4 py-3.5">Payment / Receipt</th>
                            <th class="px-4 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-xs">
                        @forelse($memberships as $m)
                            <tr class="hover:bg-gray-50/70 transition-colors">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $m->user->getProfilePictureUrl() }}"
                                             class="w-9 h-9 rounded-full object-cover border border-gray-200 shrink-0" alt=""/>
                                        <div class="min-w-0">
                                            <p class="font-bold text-gray-900 truncate">{{ $m->user->name }}</p>
                                            <p class="text-gray-400 text-[11px]">{{ $m->user->student_id ?? $m->user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 font-semibold text-gray-700">
                                    {{ $m->program?->code ?? $m->user->program?->code ?? '—' }}
                                </td>
                                <td class="px-4 py-3.5 font-bold text-gray-900">
                                    {{ $m->academic_year }}
                                </td>
                                <td class="px-4 py-3.5">
                                    @php
                                        $badges = [
                                            'active'    => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                            'pending'   => 'bg-amber-100 text-amber-800 border-amber-200 animate-pulse',
                                            'rejected'  => 'bg-red-100 text-red-800 border-red-200',
                                            'expired'   => 'bg-gray-100 text-gray-600 border-gray-200',
                                            'suspended' => 'bg-red-100 text-red-800 border-red-200',
                                        ];
                                    @endphp
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $badges[$m->status] ?? 'bg-gray-100 text-gray-600' }}">
                                        {{ ucfirst($m->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="space-y-1">
                                        @if($m->payment_method)
                                            <span class="font-bold text-gray-800 uppercase text-[11px]">{{ $m->payment_method }}</span>
                                            @if($m->payment_reference)
                                                <span class="text-gray-500 block text-[11px]">Ref: {{ $m->payment_reference }}</span>
                                            @endif
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif

                                        @if($m->proof_of_payment_path)
                                            <a href="{{ asset('storage/' . $m->proof_of_payment_path) }}" target="_blank"
                                               class="inline-flex items-center gap-1 text-[11px] font-bold text-comsoc-600 hover:text-comsoc-800 hover:underline">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                <span>View Receipt</span>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    <div class="flex items-center justify-end gap-1.5 flex-wrap">
                                        @if($m->status === 'pending')
                                            {{-- Approve Button --}}
                                            <form method="POST" action="{{ route('admin.memberships.approve', $m) }}">
                                                @csrf
                                                <button type="submit" onclick="return confirm('Verify payment and activate membership for {{ $m->user->name }}?')"
                                                        class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg shadow-xs transition-colors flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                    <span>Verify & Activate</span>
                                                </button>
                                            </form>

                                            {{-- Reject Button --}}
                                            <button type="button"
                                                    @click="rejectModal = true; activeMembershipId = {{ $m->id }}; activeStudentName = '{{ addslashes($m->user->name) }}'"
                                                    class="px-2.5 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 font-bold rounded-lg border border-red-200 transition-colors">
                                                Reject
                                            </button>
                                        @endif

                                        <a href="{{ route('admin.memberships.edit', $m) }}"
                                           class="p-1.5 text-gray-400 hover:text-comsoc-700 hover:bg-gray-100 rounded-lg transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center text-gray-400">
                                    No memberships found matching the filter criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Rejection Modal --}}
            <div x-show="rejectModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
                <div @click.outside="rejectModal = false" class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-red-100 text-red-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900">Reject Application</h3>
                            <p class="text-xs text-gray-500" x-text="'Applicant: ' + activeStudentName"></p>
                        </div>
                    </div>

                    <form :action="'{{ url('admin/memberships') }}/' + activeMembershipId + '/reject'" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                Reason for Rejection <span class="text-red-500">*</span>
                            </label>
                            <textarea name="rejection_reason" required rows="3"
                                      placeholder="e.g. Invalid GCash reference number, unreadable receipt image, duplicate entry..."
                                      class="w-full text-xs border-gray-300 rounded-xl focus:ring-red-500 focus:border-red-500 shadow-xs"></textarea>
                            <p class="text-[11px] text-gray-400 mt-1">This reason will be sent as a notification to the student so they can correct it.</p>
                        </div>

                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" @click="rejectModal = false"
                                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-xl shadow-xs transition">
                                Confirm Rejection
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div>{{ $memberships->links() }}</div>
    </div>
</x-dashboard-layout>
