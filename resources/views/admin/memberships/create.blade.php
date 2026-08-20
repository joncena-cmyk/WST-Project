<x-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('admin.memberships.index') }}" class="hover:text-gray-900">Memberships</a>
            <span>/</span>
            <span class="text-gray-800 font-semibold">Add Membership</span>
        </div>
    </x-slot>

    <div class="max-w-xl mx-auto">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 space-y-5">

            <form method="POST" action="{{ route('admin.memberships.store') }}" class="space-y-5">
                @csrf

                <div>
                    <x-input-label for="user_id" value="Member" />
                    <select id="user_id" name="user_id" required
                            class="mt-1 block w-full border-gray-300 focus:border-comsoc-500 focus:ring-comsoc-500 rounded-md shadow-sm text-sm">
                        <option value="">— Select a user —</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ old('user_id') == $u->id ? 'selected' : '' }}>
                                {{ $u->name }} {{ $u->student_id ? "({$u->student_id})" : "({$u->email})" }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('user_id')" class="mt-1" />
                </div>

                <div>
                    <x-input-label for="academic_year" value="Academic Year" />
                    <x-text-input id="academic_year" name="academic_year" type="text"
                                  :value="old('academic_year', date('Y').'-'.(date('Y')+1))"
                                  placeholder="e.g. 2025-2026"
                                  class="mt-1 block w-full text-sm" />
                    <x-input-error :messages="$errors->get('academic_year')" class="mt-1" />
                </div>

                <div>
                    <x-input-label for="program_id" value="Program (optional)" />
                    <select id="program_id" name="program_id"
                            class="mt-1 block w-full border-gray-300 focus:border-comsoc-500 focus:ring-comsoc-500 rounded-md shadow-sm text-sm">
                        <option value="">— Use user's program —</option>
                        @foreach($programs as $p)
                            <option value="{{ $p->id }}" {{ old('program_id') == $p->id ? 'selected' : '' }}>
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
                            <option value="{{ $v }}" {{ old('status','pending') === $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('status')" class="mt-1" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="start_date" value="Start Date" />
                        <x-text-input id="start_date" name="start_date" type="date"
                                      :value="old('start_date')" class="mt-1 block w-full text-sm" />
                        <x-input-error :messages="$errors->get('start_date')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="end_date" value="End Date" />
                        <x-text-input id="end_date" name="end_date" type="date"
                                      :value="old('end_date')" class="mt-1 block w-full text-sm" />
                        <x-input-error :messages="$errors->get('end_date')" class="mt-1" />
                    </div>
                </div>

                <div>
                    <x-input-label for="notes" value="Notes (optional)" />
                    <textarea id="notes" name="notes" rows="3"
                              class="mt-1 block w-full border-gray-300 focus:border-comsoc-500 focus:ring-comsoc-500 rounded-md shadow-sm text-sm resize-none"
                              placeholder="Internal notes...">{{ old('notes') }}</textarea>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <x-primary-button>Create Membership</x-primary-button>
                    <a href="{{ route('admin.memberships.index') }}"
                       class="text-sm text-gray-500 hover:text-gray-700 transition-colors">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-dashboard-layout>
