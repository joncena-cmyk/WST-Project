<x-dashboard-layout>
    <x-slot name="header">
        <h1 class="text-lg font-semibold text-gray-800">Dashboard</h1>
    </x-slot>

    {{-- Welcome banner --}}
    <div class="mb-6 rounded-xl bg-gradient-to-r from-comsoc-800 to-comsoc-700 p-6 text-white shadow-sm">
        <div class="flex items-center gap-4">
            <img src="{{ Auth::user()->getProfilePictureUrl() }}"
                 alt="{{ Auth::user()->name }}"
                 class="w-14 h-14 rounded-full object-cover border-2 border-comsoc-500 shrink-0" />
            <div>
                <h2 class="text-xl font-bold">Welcome back, {{ Auth::user()->name }}!</h2>
                <p class="mt-0.5 text-comsoc-200 text-sm flex items-center gap-2">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-comsoc-600 text-white">
                        {{ Auth::user()->getRoleLabel() }}
                    </span>
                    @if(Auth::user()->program)
                        <span>· {{ Auth::user()->program->short_name }}</span>
                    @endif
                </p>
            </div>
        </div>
    </div>

    {{-- Quick stats placeholders --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @foreach ([
            ['label' => 'Upcoming Events', 'value' => '—'],
            ['label' => 'Registered Events', 'value' => '—'],
            ['label' => 'Membership Status', 'value' => '—'],
            ['label' => 'Certificates', 'value' => '—'],
        ] as $stat)
            <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                <p class="text-sm font-medium text-gray-500">{{ $stat['label'] }}</p>
                <p class="mt-2 text-2xl font-bold text-gray-800">{{ $stat['value'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- Getting started --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
        <h3 class="text-base font-semibold text-gray-800">Getting Started</h3>
        <p class="mt-2 text-sm text-gray-600">
            COMSOC Hub is being built module by module. Authentication is ready — upcoming modules will add
            membership, community, events, attendance, and more.
        </p>
        <ul class="mt-4 space-y-2 text-sm text-gray-600">
            <li class="flex items-center gap-2">
                <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-green-100 text-green-600 text-xs font-bold">✓</span>
                Authentication &amp; profile management
            </li>
            <li class="flex items-center gap-2">
                <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-gray-100 text-gray-400 text-xs font-bold">·</span>
                Roles, permissions &amp; membership
            </li>
            <li class="flex items-center gap-2">
                <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-gray-100 text-gray-400 text-xs font-bold">·</span>
                Community, events &amp; registration
            </li>
        </ul>
    </div>
</x-dashboard-layout>
