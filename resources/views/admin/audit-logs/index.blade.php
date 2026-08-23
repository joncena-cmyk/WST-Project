<x-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-base font-bold text-gray-900">System Security &amp; Audit Logs</h1>
            <span class="text-xs text-gray-500">Immutable platform event history</span>
        </div>
    </x-slot>

    <div class="space-y-6">

        {{-- ===== FILTER & SEARCH BAR ===== --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-xs">
            <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                {{-- Search --}}
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Search Logs</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Search description, user, IP..."
                               class="w-full pl-9 pr-3 py-2 text-xs border border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500"/>
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>

                {{-- Action Filter --}}
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Action Type</label>
                    <select name="action" class="w-full text-xs border border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500">
                        <option value="">All Actions</option>
                        @foreach($actions as $act)
                            <option value="{{ $act }}" {{ request('action') === $act ? 'selected' : '' }}>
                                {{ ucwords(str_replace('_', ' ', $act)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Actions --}}
                <div class="flex items-end gap-2">
                    <button type="submit"
                            class="px-4 py-2 bg-comsoc-700 hover:bg-comsoc-800 text-white text-xs font-bold rounded-xl transition shadow-xs">
                        Filter Logs
                    </button>
                    @if(request()->hasAny(['search', 'action']))
                        <a href="{{ route('admin.audit-logs.index') }}"
                           class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-bold rounded-xl transition">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- ===== LOGS TABLE ===== --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50/80">
                        <tr>
                            <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Timestamp</th>
                            <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Actor</th>
                            <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Action</th>
                            <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">IP / Origin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($logs as $log)
                            <tr class="hover:bg-gray-50/60 transition">
                                <td class="px-5 py-4 whitespace-nowrap text-xs text-gray-500">
                                    <p class="font-bold text-gray-800">{{ $log->created_at->format('M d, Y') }}</p>
                                    <p class="text-gray-400 mt-0.5">{{ $log->created_at->format('h:i:s A') }}</p>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-xs">
                                    @if($log->user)
                                        <p class="font-bold text-gray-900">{{ $log->user->name }}</p>
                                        <p class="text-gray-400 text-[11px]">{{ $log->user->email }}</p>
                                    @else
                                        <span class="text-gray-400 italic">System / Anonymous</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-xs">
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-bold bg-comsoc-50 text-comsoc-800 border border-comsoc-100">
                                        {{ ucwords(str_replace('_', ' ', $log->action)) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-xs text-gray-700 max-w-md">
                                    <p class="leading-relaxed">{{ $log->description }}</p>
                                    @if($log->properties)
                                        <pre class="mt-1 text-[10px] bg-gray-50 p-2 rounded text-gray-600 overflow-x-auto border border-gray-100">{{ json_encode($log->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                    @endif
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-xs text-gray-500">
                                    <code class="text-[11px] bg-gray-100 px-2 py-0.5 rounded text-gray-700 font-mono">{{ $log->ip_address ?? '127.0.0.1' }}</code>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-12 text-center text-gray-400 text-sm">
                                    No audit logs recorded yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($logs->hasPages())
                <div class="px-5 py-4 border-t border-gray-100">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>

    </div>
</x-dashboard-layout>
