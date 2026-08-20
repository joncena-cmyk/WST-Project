<x-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('admin.memberships.index') }}" class="hover:text-gray-900">Memberships</a>
            <span>/</span>
            <span class="text-gray-800 font-semibold">{{ $membership->user->name }} — {{ $membership->academic_year }}</span>
        </div>
    </x-slot>

    <div class="max-w-xl mx-auto space-y-5">

        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition
                 class="flex items-center gap-3 p-4 bg-comsoc-50 border border-comsoc-200 rounded-xl text-comsoc-700 text-sm">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Member info card --}}
        <div class="flex items-center gap-3 bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
            <img src="{{ $membership->user->getProfilePictureUrl() }}"
                 class="w-11 h-11 rounded-full object-cover border border-gray-200 shrink-0" alt=""/>
            <div>
                <p class="font-semibold text-gray-900 text-sm">{{ $membership->user->name }}</p>
                <p class="text-xs text-gray-400">{{ $membership->user->student_id ?? $membership->user->email }}</p>
            </div>
            <span class="ml-auto text-sm font-bold text-comsoc-700 bg-comsoc-50 px-3 py-1 rounded-lg">
                {{ $membership->academic_year }}
            </span>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <form method="POST" action="{{ route('admin.memberships.update', $membership) }}" class="space-y-5">
                @csrf
                @method('PATCH')

                <div>
                    <x-input-label for="program_id" value="Program" />
                    <select id="program_id" name="program_id"
                            class="mt-1 block w-full border-gray-300 focus:border-comsoc-500 focus:ring-comsoc-500 rounded-md shadow-sm text-sm">
                        <option value="">— Not specified —</option>
                        @foreach($programs as $p)
                            <option value="{{ $p->id }}" {{ old('program_id', $membership->program_id) == $p->id ? 'selected' : '' }}>
                                {{ $p->short_name }} ({{ $p->code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <x-input-label for="status" value="Status" />
                    <select id="status" name="status" required
                            class="mt-1 block w-full border-gray-300 focus:border-comsoc-500 focus:ring-comsoc-500 rounded-md shadow-sm text-sm">
                        @foreach(['pending'=>'Pending','active'=>'Active','expired'=>'Expired','suspended'=>'Suspended'] as $v => $l)
                            <option value="{{ $v }}" {{ old('status', $membership->status) === $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('status')" class="mt-1" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="start_date" value="Start Date" />
                        <x-text-input id="start_date" name="start_date" type="date"
                                      :value="old('start_date', $membership->start_date?->format('Y-m-d'))"
                                      class="mt-1 block w-full text-sm" />
                    </div>
                    <div>
                        <x-input-label for="end_date" value="End Date" />
                        <x-text-input id="end_date" name="end_date" type="date"
                                      :value="old('end_date', $membership->end_date?->format('Y-m-d'))"
                                      class="mt-1 block w-full text-sm" />
                    </div>
                </div>

                <div>
                    <x-input-label for="notes" value="Notes" />
                    <textarea id="notes" name="notes" rows="3"
                              class="mt-1 block w-full border-gray-300 focus:border-comsoc-500 focus:ring-comsoc-500 rounded-md shadow-sm text-sm resize-none">{{ old('notes', $membership->notes) }}</textarea>
                </div>

                @if($membership->approved_at)
                    <div class="p-3 bg-green-50 border border-green-200 rounded-lg text-xs text-green-700">
                        Approved by <strong>{{ $membership->approvedBy?->name ?? 'System' }}</strong>
                        on {{ $membership->approved_at->format('M d, Y \a\t g:i A') }}
                    </div>
                @endif

                <div class="flex items-center gap-3 pt-2">
                    <x-primary-button>Save changes</x-primary-button>
                    <a href="{{ route('admin.memberships.index') }}"
                       class="text-sm text-gray-500 hover:text-gray-700 transition-colors">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-dashboard-layout>
