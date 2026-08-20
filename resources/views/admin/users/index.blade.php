<x-dashboard-layout>
    <x-slot name="header">
        <h1 class="text-lg font-semibold text-gray-800">User Management</h1>
    </x-slot>

    <div class="space-y-5">

        {{-- Flash --}}
        @foreach(['success','error','info'] as $type)
            @if(session($type))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3500)" x-transition
                     class="flex items-center gap-3 p-4 rounded-xl text-sm border
                            {{ $type === 'success' ? 'bg-comsoc-50 border-comsoc-200 text-comsoc-700' : ($type === 'error' ? 'bg-red-50 border-red-200 text-red-700' : 'bg-blue-50 border-blue-200 text-blue-700') }}">
                    {{ session($type) }}
                </div>
            @endif
        @endforeach

        {{-- Stats --}}
        @php
            $total     = \App\Models\User::count();
            $active    = \App\Models\User::where('account_status', 'active')->count();
            $suspended = \App\Models\User::where('account_status', 'suspended')->count();
        @endphp
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            @foreach([
                ['Total Users',     $total,                        'text-gray-600',  'bg-gray-50'],
                ['Active',          $active,                       'text-comsoc-600','bg-comsoc-50'],
                ['Suspended',       $suspended,                    'text-red-600',   'bg-red-50'],
                ['Admins',          \App\Models\User::where('role','admin')->count(), 'text-purple-600','bg-purple-50'],
            ] as [$label, $count, $textColor, $bgColor])
                <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                    <p class="text-xs text-gray-500 font-medium">{{ $label }}</p>
                    <p class="text-2xl font-black {{ $textColor }} mt-1">{{ $count }}</p>
                </div>
            @endforeach
        </div>

        {{-- Filters --}}
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
            <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[180px]">
                    <x-input-label for="search" value="Search" />
                    <x-text-input id="search" name="search" type="text"
                                  :value="request('search')"
                                  placeholder="Name, email, student ID..."
                                  class="mt-1 block w-full text-sm" />
                </div>
                <div>
                    <x-input-label for="role" value="Role" />
                    <select id="role" name="role"
                            class="mt-1 border-gray-300 focus:border-comsoc-500 focus:ring-comsoc-500 rounded-md shadow-sm text-sm">
                        <option value="">All roles</option>
                        @foreach(\App\Models\User::ROLES as $value => $label)
                            <option value="{{ $value }}" {{ request('role') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="status" value="Status" />
                    <select id="status" name="status"
                            class="mt-1 border-gray-300 focus:border-comsoc-500 focus:ring-comsoc-500 rounded-md shadow-sm text-sm">
                        <option value="">All statuses</option>
                        <option value="active"    {{ request('status') === 'active'    ? 'selected' : '' }}>Active</option>
                        <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                        <option value="inactive"  {{ request('status') === 'inactive'  ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div>
                    <x-input-label for="program_id" value="Program" />
                    <select id="program_id" name="program_id"
                            class="mt-1 border-gray-300 focus:border-comsoc-500 focus:ring-comsoc-500 rounded-md shadow-sm text-sm">
                        <option value="">All programs</option>
                        @foreach($programs as $program)
                            <option value="{{ $program->id }}" {{ request('program_id') == $program->id ? 'selected' : '' }}>
                                {{ $program->code }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <x-primary-button class="text-sm py-2">Filter</x-primary-button>
                    @if(request()->hasAny(['search','role','status','program_id']))
                        <a href="{{ route('admin.users.index') }}"
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-600 hover:bg-gray-50 transition-colors">
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
                            @foreach(['User','Student ID','Program','Role','Status','Joined',''] as $col)
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                    {{ $col }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($users as $user)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $user->getProfilePictureUrl() }}"
                                             class="w-9 h-9 rounded-full object-cover border border-gray-200 shrink-0" alt=""/>
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $user->name }}</p>
                                            <p class="text-xs text-gray-400 truncate">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $user->student_id ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $user->program?->code ?? '—' }}
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $roleColors = [
                                            'admin'       => 'bg-purple-100 text-purple-700',
                                            'president'   => 'bg-yellow-100 text-yellow-700',
                                            'adviser'     => 'bg-teal-100 text-teal-700',
                                            'officer'     => 'bg-blue-100 text-blue-700',
                                            'program_rep' => 'bg-pink-100 text-pink-700',
                                            'member'      => 'bg-comsoc-100 text-comsoc-700',
                                            'student'     => 'bg-gray-100 text-gray-600',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $roleColors[$user->role] ?? 'bg-gray-100 text-gray-600' }}">
                                        {{ $user->getRoleLabel() }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold
                                                 {{ $user->account_status === 'active' ? 'bg-green-100 text-green-700' : ($user->account_status === 'suspended' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-500') }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $user->account_status === 'active' ? 'bg-green-500' : 'bg-red-400' }}"></span>
                                        {{ ucfirst($user->account_status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-400">
                                    {{ $user->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('admin.users.show', $user) }}"
                                       class="text-xs font-semibold text-comsoc-600 hover:text-comsoc-800 transition-colors">
                                        Manage →
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center text-gray-400 text-sm">
                                    No users found matching your filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div>{{ $users->links() }}</div>
    </div>
</x-dashboard-layout>
