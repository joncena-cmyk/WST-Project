<x-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('events.index') }}" class="hover:text-gray-900 transition-colors">Events</a>
            <span>/</span>
            <span class="text-gray-800 font-semibold truncate">{{ $event->title }}</span>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-5">

        {{-- Flash --}}
        @foreach(['success','error','info'] as $type)
            @if(session($type))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition
                     class="flex items-center gap-3 p-4 rounded-xl text-sm border
                            {{ $type === 'success' ? 'bg-comsoc-50 border-comsoc-200 text-comsoc-700' : ($type === 'error' ? 'bg-red-50 border-red-200 text-red-700' : 'bg-blue-50 border-blue-200 text-blue-700') }}">
                    {{ session($type) }}
                </div>
            @endif
        @endforeach

        {{-- Event header --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            @if($event->getImageUrl())
                <img src="{{ $event->getImageUrl() }}" alt="{{ $event->title }}"
                     class="w-full h-56 object-cover"/>
            @else
                <div class="w-full h-32 bg-gradient-to-br from-comsoc-700 to-comsoc-900 flex items-center justify-center">
                    <svg class="w-16 h-16 text-comsoc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            @endif

            <div class="p-6">
                <div class="flex items-start justify-between gap-4 mb-4">
                    <h1 class="text-2xl font-black text-gray-900 leading-tight">{{ $event->title }}</h1>
                    <span class="inline-flex shrink-0 px-3 py-1 rounded-full text-sm font-semibold {{ $event->getStatusColor() }}">
                        {{ $event->getStatusLabel() }}
                    </span>
                </div>

                {{-- Meta grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                    @foreach([
                        ['M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'Date & Time', $event->event_date->format('l, F d, Y') . ' · ' . \Carbon\Carbon::parse($event->start_time)->format('g:i A') . ($event->end_time ? ' – ' . \Carbon\Carbon::parse($event->end_time)->format('g:i A') : '')],
                        ['M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z', 'Venue', $event->venue],
                    ] as [$icon, $label, $value])
                        <div class="flex items-start gap-3">
                            <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-comsoc-50 shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-comsoc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">{{ $label }}</p>
                                <p class="text-sm text-gray-800 font-medium mt-0.5">{{ $value }}</p>
                            </div>
                        </div>
                    @endforeach
                    @if($event->registration_deadline)
                        <div class="flex items-start gap-3">
                            <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-orange-50 shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Registration Deadline</p>
                                <p class="text-sm font-medium mt-0.5 {{ now()->isAfter($event->registration_deadline) ? 'text-red-600' : 'text-gray-800' }}">
                                    {{ $event->registration_deadline->format('M d, Y g:i A') }}
                                    @if(now()->isAfter($event->registration_deadline))
                                        <span class="ml-1 text-xs text-red-500">(Closed)</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endif
                    @if($event->participant_limit)
                        <div class="flex items-start gap-3">
                            <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Slots</p>
                                <p class="text-sm text-gray-800 font-medium mt-0.5">
                                    {{ $event->registeredCount() }} / {{ $event->participant_limit }}
                                    @if($event->isFull()) <span class="text-red-500 text-xs ml-1">(Full)</span> @endif
                                </p>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Description --}}
                <div class="prose prose-sm max-w-none text-gray-700 border-t border-gray-100 pt-5">
                    {!! nl2br(e($event->description)) !!}
                </div>

                {{-- Requirements --}}
                @if($event->requirements->isNotEmpty())
                    <div class="mt-5 pt-5 border-t border-gray-100">
                        <p class="text-sm font-bold text-gray-800 mb-3">Requirements</p>
                        <ul class="space-y-1.5">
                            @foreach($event->requirements as $req)
                                <li class="flex items-start gap-2 text-sm text-gray-600">
                                    <svg class="w-4 h-4 text-comsoc-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ $req->description }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>

        {{-- Registration action --}}
        @if($event->isPublic())
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
                @if($userRegistration && $userRegistration->status === 'registered')
                    <div class="flex items-center justify-between flex-wrap gap-4">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-comsoc-50">
                                <svg class="w-5 h-5 text-comsoc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">You're registered!</p>
                                <p class="text-xs text-gray-500">Registered {{ $userRegistration->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('events.registration.cancel', $userRegistration) }}">
                            @csrf
                            <button type="submit"
                                    onclick="return confirm('Cancel your registration?')"
                                    class="text-sm text-red-600 hover:text-red-800 font-medium transition-colors">
                                Cancel Registration
                            </button>
                        </form>
                    </div>
                @elseif($event->isRegistrationOpen())
                    <div class="flex items-center justify-between flex-wrap gap-4">
                        <div>
                            <p class="font-semibold text-gray-900 text-sm">Register for this event</p>
                            <p class="text-xs text-gray-500 mt-0.5">
                                @if($event->participant_limit)
                                    {{ $event->participant_limit - $event->registeredCount() }} slots remaining
                                @else
                                    Open registration
                                @endif
                            </p>
                        </div>
                        <form method="POST" action="{{ route('events.register', $event) }}">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-comsoc-600 text-white text-sm font-semibold rounded-lg hover:bg-comsoc-700 transition-colors shadow-sm">
                                Register Now
                            </button>
                        </form>
                    </div>
                @else
                    <div class="flex items-center gap-3 text-sm text-gray-500">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Registration is closed for this event.
                    </div>
                @endif
            </div>
        @endif

        {{-- Officer actions --}}
        @can('update', $event)
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
                <p class="text-sm font-bold text-gray-800 mb-3">Officer Actions</p>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('events.edit', $event) }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2 border border-gray-300 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </a>
                    @can('submit', $event)
                        <form method="POST" action="{{ route('events.submit', $event) }}">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                                Submit for Review
                            </button>
                        </form>
                    @endcan
                    @can('publish', $event)
                        <form method="POST" action="{{ route('events.publish', $event) }}">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-comsoc-600 text-white text-sm font-semibold rounded-lg hover:bg-comsoc-700 transition-colors">
                                Publish Event
                            </button>
                        </form>
                    @endcan
                    @can('viewParticipants', $event)
                        <a href="{{ route('events.committees.index', $event) }}"
                           class="inline-flex items-center gap-1.5 px-4 py-2 bg-comsoc-50 border border-comsoc-200 text-sm font-semibold text-comsoc-800 rounded-lg hover:bg-comsoc-100 transition-colors">
                            <svg class="w-4 h-4 text-comsoc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Committees &amp; Tasks
                        </a>
                        <a href="{{ route('events.participants', $event) }}"
                           class="inline-flex items-center gap-1.5 px-4 py-2 border border-gray-300 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                            Participants ({{ $event->registeredCount() }})
                        </a>

                        @if(in_array($event->status, [\App\Models\Event::STATUS_PUBLISHED, \App\Models\Event::STATUS_ONGOING, \App\Models\Event::STATUS_COMPLETED]))
                            <a href="{{ route('attendance.scanner', $event) }}"
                               class="inline-flex items-center gap-1.5 px-4 py-2 bg-yellow-500 text-white text-sm font-semibold rounded-lg hover:bg-yellow-600 transition-colors shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                </svg>
                                QR Scanner
                            </a>
                            <a href="{{ route('attendance.index', $event) }}"
                               class="inline-flex items-center gap-1.5 px-4 py-2 border border-gray-300 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                                Attendance List
                            </a>
                            <a href="{{ route('certificates.manage', $event) }}"
                               class="inline-flex items-center gap-1.5 px-4 py-2 border border-comsoc-300 text-sm font-medium text-comsoc-700 rounded-lg hover:bg-comsoc-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                </svg>
                                Certificates
                            </a>
                        @endif
                    @endcan
                </div>
            </div>
        @endcan

        {{-- Approval history --}}
        @if($event->approvals->isNotEmpty())
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <p class="text-sm font-bold text-gray-800">Approval History</p>
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach($event->approvals as $approval)
                        <div class="px-5 py-3 flex items-start gap-3">
                            <span class="inline-flex shrink-0 px-2 py-0.5 rounded-full text-xs font-semibold mt-0.5
                                         {{ $approval->action === 'approved' ? 'bg-comsoc-100 text-comsoc-700' : ($approval->action === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-orange-100 text-orange-700') }}">
                                {{ ucfirst($approval->action) }}
                            </span>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs text-gray-500">
                                    By <strong>{{ $approval->reviewer->name }}</strong>
                                    · {{ $approval->created_at->format('M d, Y g:i A') }}
                                </p>
                                @if($approval->remarks)
                                    <p class="text-sm text-gray-700 mt-1">{{ $approval->remarks }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-dashboard-layout>
