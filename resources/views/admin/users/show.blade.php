<x-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('admin.users.index') }}" class="hover:text-gray-900 transition-colors">Users</a>
            <span>/</span>
            <span class="text-gray-800 font-semibold">{{ $user->name }}</span>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-5">

        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition
                 class="flex items-center gap-3 p-4 bg-comsoc-50 border border-comsoc-200 rounded-xl text-comsoc-700 text-sm">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Profile card --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <div class="flex items-start gap-5">
                <img src="{{ $user->getProfilePictureUrl() }}"
                     class="w-20 h-20 rounded-full object-cover border-2 border-gray-200 shrink-0" alt=""/>
                <div class="flex-1 min-w-0">
                    <h2 class="text-xl font-bold text-gray-900">{{ $user->name }}</h2>
                    <p class="text-gray-500 text-sm mt-0.5">{{ $user->email }}</p>
                    <div class="flex flex-wrap gap-2 mt-3">
                        @php
                            $roleColors = [
                                'admin'=>'bg-purple-100 text-purple-700','president'=>'bg-yellow-100 text-yellow-700',
                                'adviser'=>'bg-teal-100 text-teal-700','officer'=>'bg-blue-100 text-blue-700',
                                'program_rep'=>'bg-pink-100 text-pink-700','member'=>'bg-comsoc-100 text-comsoc-700',
                                'student'=>'bg-gray-100 text-gray-600',
                            ];
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $roleColors[$user->role] ?? 'bg-gray-100 text-gray-600' }}">
                            {{ $user->getRoleLabel() }}
                        </span>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold
                                     {{ $user->account_status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $user->account_status === 'active' ? 'bg-green-500' : 'bg-red-400' }}"></span>
                            {{ ucfirst($user->account_status) }}
                        </span>
                        @if($user->program)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                {{ $user->program->code }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="text-xs text-gray-400 shrink-0 text-right">
                    <p>Joined {{ $user->created_at->format('M d, Y') }}</p>
                    @if($user->student_id)
                        <p class="mt-1 font-medium text-gray-600">{{ $user->student_id }}</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Edit form --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <h3 class="text-base font-bold text-gray-800 mb-5">Account Settings</h3>

            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-5">
                @csrf
                @method('PATCH')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    {{-- Role --}}
                    <div>
                        <x-input-label for="role" value="Role" />
                        <select id="role" name="role"
                                class="mt-1 block w-full border-gray-300 focus:border-comsoc-500 focus:ring-comsoc-500 rounded-md shadow-sm text-sm">
                            @foreach(\App\Models\User::ROLES as $value => $label)
                                <option value="{{ $value }}" {{ $user->role === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('role')" class="mt-1" />
                    </div>

                    {{-- Account status --}}
                    <div>
                        <x-input-label for="account_status" value="Account Status" />
                        <select id="account_status" name="account_status"
                                class="mt-1 block w-full border-gray-300 focus:border-comsoc-500 focus:ring-comsoc-500 rounded-md shadow-sm text-sm">
                            <option value="active"    {{ $user->account_status === 'active'    ? 'selected' : '' }}>Active</option>
                            <option value="suspended" {{ $user->account_status === 'suspended' ? 'selected' : '' }}>Suspended</option>
                            <option value="inactive"  {{ $user->account_status === 'inactive'  ? 'selected' : '' }}>Inactive</option>
                        </select>
                        <x-input-error :messages="$errors->get('account_status')" class="mt-1" />
                    </div>

                    {{-- Student ID --}}
                    <div>
                        <x-input-label for="student_id" value="Student ID" />
                        <x-text-input id="student_id" name="student_id" type="text"
                                      :value="old('student_id', $user->student_id)"
                                      class="mt-1 block w-full text-sm"
                                      placeholder="e.g. 2021-00001" />
                        <x-input-error :messages="$errors->get('student_id')" class="mt-1" />
                    </div>

                    {{-- Program --}}
                    <div>
                        <x-input-label for="program_id" value="Program" />
                        <select id="program_id" name="program_id"
                                class="mt-1 block w-full border-gray-300 focus:border-comsoc-500 focus:ring-comsoc-500 rounded-md shadow-sm text-sm">
                            <option value="">— Not specified —</option>
                            @foreach($programs as $program)
                                <option value="{{ $program->id }}" {{ $user->program_id == $program->id ? 'selected' : '' }}>
                                    {{ $program->short_name }} ({{ $program->code }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('program_id')" class="mt-1" />
                    </div>
                </div>

                @if($user->id === Auth::id())
                    <div class="flex items-center gap-2 p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-xs text-yellow-700">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        You cannot change your own role.
                    </div>
                @endif

                <div class="flex items-center gap-3 pt-2">
                    <x-primary-button>Save changes</x-primary-button>
                    <a href="{{ route('admin.users.index') }}"
                       class="text-sm text-gray-500 hover:text-gray-700 transition-colors">Cancel</a>
                </div>
            </form>
        </div>

        {{-- Membership history --}}
        @if($user->memberships->isNotEmpty())
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="text-sm font-bold text-gray-800">Membership History</h3>
                </div>
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            @foreach(['Academic Year','Program','Status','Start','End','Approved By'] as $col)
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ $col }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($user->memberships as $m)
                            <tr>
                                <td class="px-4 py-3 text-sm font-semibold text-gray-900">{{ $m->academic_year }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $m->program?->code ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $mColors = ['active'=>'bg-green-100 text-green-700','expired'=>'bg-gray-100 text-gray-500','pending'=>'bg-yellow-100 text-yellow-700','suspended'=>'bg-red-100 text-red-700'];
                                    @endphp
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $mColors[$m->status] ?? 'bg-gray-100 text-gray-500' }}">
                                        {{ ucfirst($m->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-500">{{ $m->start_date?->format('M d, Y') ?? '—' }}</td>
                                <td class="px-4 py-3 text-xs text-gray-500">{{ $m->end_date?->format('M d, Y') ?? '—' }}</td>
                                <td class="px-4 py-3 text-xs text-gray-500">{{ $m->approvedBy?->name ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-dashboard-layout>
