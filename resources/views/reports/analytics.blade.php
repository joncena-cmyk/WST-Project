<x-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-base font-bold text-gray-900">Organization Reports &amp; Analytics</h1>
            <div class="flex items-center gap-2">
                <a href="{{ route('analytics.export.events') }}"
                   class="px-3 py-1.5 bg-white hover:bg-gray-50 text-gray-700 text-xs font-bold rounded-lg border border-gray-300 transition shadow-2xs flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-comsoc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Export Events CSV
                </a>
                <a href="{{ route('analytics.export.memberships') }}"
                   class="px-3 py-1.5 bg-comsoc-700 hover:bg-comsoc-800 text-white text-xs font-bold rounded-lg transition shadow-xs flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Export Memberships CSV
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">

        {{-- ===== OVERALL ORGANIZATION KPIS ===== --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-blue-100 rounded-2xl p-5 shadow-xs bg-gradient-to-br from-white to-blue-50/30">
                <div class="flex items-center justify-between text-blue-600 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-blue-700/80">Event Turnout Rate</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
                <p class="text-3xl font-black text-blue-900 leading-tight">{{ $overallTurnoutRate }}%</p>
                <p class="text-xs text-gray-500 mt-1">{{ $totalAttendanceCount }} check-ins across {{ $totalRegistrationsCount }} registrations</p>
            </div>

            <div class="bg-white border border-green-100 rounded-2xl p-5 shadow-xs bg-gradient-to-br from-white to-green-50/30">
                <div class="flex items-center justify-between text-green-600 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-green-700/80">Active Membership</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <p class="text-3xl font-black text-green-900 leading-tight">{{ $totalActiveMembers }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $totalPendingMembers }} applications pending verification</p>
            </div>

            <div class="bg-white border border-amber-100 rounded-2xl p-5 shadow-xs bg-gradient-to-br from-white to-amber-50/30">
                <div class="flex items-center justify-between text-amber-600 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-amber-700/80">Task Velocity</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <p class="text-3xl font-black text-amber-900 leading-tight">{{ $taskVelocity }}%</p>
                <p class="text-xs text-gray-500 mt-1">{{ $completedTasks }} completed of {{ $totalTasks }} total tasks</p>
            </div>

            <div class="bg-white border border-purple-100 rounded-2xl p-5 shadow-xs bg-gradient-to-br from-white to-purple-50/30">
                <div class="flex items-center justify-between text-purple-600 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-purple-700/80">Certificates Issued</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138z"/>
                    </svg>
                </div>
                <p class="text-3xl font-black text-purple-900 leading-tight">{{ $totalCertificatesIssued }}</p>
                <p class="text-xs text-gray-500 mt-1">Verified attendee credentials issued</p>
            </div>
        </div>

        {{-- ===== EVENT PARTICIPATION & ATTENDANCE PERFORMANCE TABLE ===== --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-gray-50/50">
                <div>
                    <h3 class="font-bold text-gray-900 text-base">Event Participation &amp; Attendance Report</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Real-time attendance turnout rates with per-event CSV roster exports.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50/80">
                        <tr>
                            <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Event Title</th>
                            <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Date &amp; Venue</th>
                            <th class="px-5 py-3.5 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Registered</th>
                            <th class="px-5 py-3.5 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Attended</th>
                            <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Turnout</th>
                            <th class="px-5 py-3.5 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Export Roster</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($events as $event)
                            @php
                                $rate = $event->valid_registrations_count > 0
                                    ? round(($event->attendances_count / $event->valid_registrations_count) * 100)
                                    : 0;
                            @endphp
                            <tr class="hover:bg-gray-50/60 transition">
                                <td class="px-5 py-4">
                                    <div class="font-bold text-sm text-gray-900 truncate max-w-xs">{{ $event->title }}</div>
                                    <span class="inline-flex mt-1 px-2 py-0.5 rounded-full text-[10px] font-bold {{ $event->getStatusColor() }}">
                                        {{ $event->getStatusLabel() }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-xs text-gray-500">
                                    <p class="font-bold text-gray-800">{{ $event->event_date->format('M d, Y') }}</p>
                                    <p class="text-gray-400 mt-0.5">{{ $event->venue }}</p>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-center text-sm font-bold text-gray-800">
                                    {{ $event->valid_registrations_count }}
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-center text-sm font-black text-comsoc-700">
                                    {{ $event->attendances_count }}
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="w-20 h-2 bg-gray-100 rounded-full overflow-hidden">
                                            <div class="h-full {{ $rate >= 75 ? 'bg-green-500' : ($rate >= 50 ? 'bg-amber-500' : 'bg-red-400') }} rounded-full" style="width: {{ min(100, $rate) }}%"></div>
                                        </div>
                                        <span class="text-xs font-black text-gray-800">{{ $rate }}%</span>
                                    </div>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-right text-xs">
                                    <a href="{{ route('analytics.export.attendance', $event) }}"
                                       class="px-3 py-1.5 bg-gray-100 hover:bg-comsoc-50 text-gray-700 hover:text-comsoc-800 font-bold rounded-lg border border-gray-200 transition inline-flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                        CSV Roster
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-10 text-center text-gray-400 text-sm">
                                    No event attendance records logged yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($events->hasPages())
                <div class="px-5 py-4 border-t border-gray-100">
                    {{ $events->links() }}
                </div>
            @endif
        </div>

        {{-- ===== TWO-COLUMN: Academic Program Breakdown & Community Engagement ===== --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            {{-- Program Membership Breakdown --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-gray-900 text-base flex items-center gap-2">
                        <svg class="w-4 h-4 text-comsoc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Membership by Academic Program
                    </h3>
                    <span class="text-xs font-bold text-comsoc-600">{{ $totalActiveMembers }} Active Total</span>
                </div>

                <div class="space-y-4">
                    @foreach($programs as $prog)
                        @php
                            $progPercentage = $totalActiveMembers > 0 ? round(($prog->active_members_count / $totalActiveMembers) * 100) : 0;
                        @endphp
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="font-bold text-gray-800">{{ $prog->code }} ({{ $prog->name }})</span>
                                <span class="font-black text-comsoc-800">{{ $prog->active_members_count }} members ({{ $progPercentage }}%)</span>
                            </div>
                            <div class="w-full h-2.5 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-comsoc-600 to-gold-400 rounded-full" style="width: {{ $progPercentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Community & Committee Task Velocity --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm space-y-5">
                <h3 class="font-bold text-gray-900 text-base flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Operations &amp; Engagement Stats
                </h3>

                {{-- Task Status Breakdown --}}
                <div class="p-4 bg-gray-50 rounded-xl space-y-2">
                    <p class="text-xs font-bold text-gray-700 uppercase tracking-wider">Committee Task Distribution</p>
                    <div class="grid grid-cols-3 gap-2 text-center pt-1">
                        <div class="bg-white border border-gray-200 rounded-lg p-2.5">
                            <p class="text-base font-black text-green-700">{{ $completedTasks }}</p>
                            <p class="text-[10px] font-bold text-gray-400 uppercase">Completed</p>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-lg p-2.5">
                            <p class="text-base font-black text-amber-700">{{ $inProgressTasks }}</p>
                            <p class="text-[10px] font-bold text-gray-400 uppercase">In Progress</p>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-lg p-2.5">
                            <p class="text-base font-black text-gray-700">{{ $pendingTasks }}</p>
                            <p class="text-[10px] font-bold text-gray-400 uppercase">Pending</p>
                        </div>
                    </div>
                </div>

                {{-- Community Engagement Totals --}}
                <div class="p-4 bg-comsoc-50/50 border border-comsoc-100 rounded-xl space-y-2">
                    <p class="text-xs font-bold text-comsoc-800 uppercase tracking-wider">Community Engagement</p>
                    <div class="grid grid-cols-3 gap-2 text-center pt-1">
                        <div>
                            <p class="text-lg font-black text-comsoc-900">{{ $totalPosts }}</p>
                            <p class="text-[10px] font-semibold text-comsoc-700">Posts</p>
                        </div>
                        <div>
                            <p class="text-lg font-black text-comsoc-900">{{ $totalComments }}</p>
                            <p class="text-[10px] font-semibold text-comsoc-700">Comments</p>
                        </div>
                        <div>
                            <p class="text-lg font-black text-comsoc-900">{{ $totalReactions }}</p>
                            <p class="text-[10px] font-semibold text-comsoc-700">Reactions</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-dashboard-layout>
