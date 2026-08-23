<x-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-base font-bold text-gray-900">System Administrator Console</h1>
            <a href="{{ route('analytics.index') }}" class="px-3 py-1.5 bg-comsoc-50 hover:bg-comsoc-100 text-comsoc-700 text-xs font-bold rounded-lg border border-comsoc-200 transition flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                System Analytics →
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">

        {{-- ===== HERO ADMIN BANNER ===== --}}
        <div class="relative rounded-2xl overflow-hidden shadow-md" style="min-height:140px;">
            <div class="absolute inset-0">
                <img src="{{ asset('images/hero_logo.jpg') }}" alt="" class="w-full h-full object-cover"/>
            </div>
            <div class="absolute inset-0" style="background:linear-gradient(135deg,rgba(15,23,42,0.96) 0%,rgba(5,46,22,0.85) 100%)"></div>
            <div class="absolute top-0 left-0 right-0 h-0.5 bg-gradient-to-r from-red-500 via-gold-400 to-comsoc-500"></div>

            <div class="relative z-10 flex items-center justify-between p-6 text-white">
                <div class="flex items-center gap-4">
                    <img src="{{ $user->getProfilePictureUrl() }}"
                         alt="{{ $user->name }}"
                         class="w-14 h-14 rounded-full object-cover border-2 border-red-400 shadow-md"/>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-red-500 text-white uppercase tracking-wide">
                                Super Administrator
                            </span>
                            <span class="text-xs text-slate-300">Platform Governance &amp; Security</span>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-black mt-1">{{ $user->name }}</h2>
                    </div>
                </div>

                <div class="hidden sm:flex items-center gap-2">
                    <a href="{{ route('admin.users.index') }}"
                       class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-xl transition shadow-xs flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        Manage Users
                    </a>
                </div>
            </div>
        </div>

        {{-- ===== PLATFORM METRICS ===== --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
            <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-xs">
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Total Registered Accounts</p>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ $totalUsers }}</p>
            </div>

            <div class="bg-white border border-green-100 rounded-2xl p-4 shadow-xs bg-green-50/40">
                <p class="text-[11px] font-bold text-green-700 uppercase tracking-wider">Active Verified Accounts</p>
                <p class="text-2xl font-black text-green-800 mt-1">{{ $activeUsers }}</p>
            </div>

            <div class="bg-white border border-blue-100 rounded-2xl p-4 shadow-xs bg-blue-50/40">
                <p class="text-[11px] font-bold text-blue-700 uppercase tracking-wider">Events Created</p>
                <p class="text-2xl font-black text-blue-800 mt-1">{{ $totalEvents }}</p>
            </div>

            <div class="bg-white border border-purple-100 rounded-2xl p-4 shadow-xs bg-purple-50/40">
                <p class="text-[11px] font-bold text-purple-700 uppercase tracking-wider">Organization Documents</p>
                <p class="text-2xl font-black text-purple-800 mt-1">{{ $totalDocuments }}</p>
            </div>
        </div>

        {{-- ===== TWO-COLUMN: Academic Program Distribution & Recent Users ===== --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            {{-- Academic Program Distribution --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm space-y-4">
                <h3 class="font-bold text-gray-900 text-base flex items-center gap-2">
                    <svg class="w-4 h-4 text-comsoc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Users by CCIS Academic Program
                </h3>

                <div class="space-y-3">
                    @foreach($programs as $prog)
                        @php
                            $percentage = $totalUsers > 0 ? round(($prog->users_count / $totalUsers) * 100) : 0;
                        @endphp
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="font-bold text-gray-800">{{ $prog->code }} — {{ $prog->name }}</span>
                                <span class="font-black text-comsoc-800">{{ $prog->users_count }} ({{ $percentage }}%)</span>
                            </div>
                            <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-comsoc-600 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Recent User Accounts --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-gray-900 text-base flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        Recently Registered Users
                    </h3>
                    <a href="{{ route('admin.users.index') }}" class="text-xs font-bold text-comsoc-600 hover:text-comsoc-800 transition">
                        View All →
                    </a>
                </div>

                <div class="space-y-3">
                    @foreach($recentUsers as $u)
                        <div class="p-3 rounded-xl border border-gray-100 bg-gray-50/50 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <img src="{{ $u->getProfilePictureUrl() }}" class="w-8 h-8 rounded-full object-cover border"/>
                                <div class="min-w-0">
                                    <p class="font-bold text-gray-900 text-xs truncate">{{ $u->name }}</p>
                                    <p class="text-[11px] text-gray-400 truncate">{{ $u->email }}</p>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-200 text-gray-800 uppercase tracking-wide shrink-0">
                                {{ $u->getRoleLabel() }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</x-dashboard-layout>
