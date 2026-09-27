<x-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('events.approval.index') }}" class="hover:text-gray-900">Approval Queue</a>
            <span>/</span>
            <span class="text-gray-800 font-semibold truncate">{{ $event->title }}</span>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-5">

        {{-- Event details card --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            @if($event->getImageUrl())
                <img src="{{ $event->getImageUrl() }}" class="w-full h-48 object-cover"/>
            @endif
            <div class="p-6">
                <div class="flex items-start justify-between gap-4 mb-5">
                    <div>
                        <h1 class="text-xl font-black text-gray-900">{{ $event->title }}</h1>
                        <p class="text-sm text-gray-400 mt-1">
                            Submitted by <strong class="text-gray-600">{{ $event->creator->name }}</strong>
                            · {{ $event->updated_at->diffForHumans() }}
                        </p>
                    </div>
                    <span class="inline-flex shrink-0 px-3 py-1 rounded-full text-sm font-semibold {{ $event->getStatusColor() }}">
                        {{ $event->getStatusLabel() }}
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-5 text-sm">
                    <div><p class="text-xs text-gray-400 font-semibold uppercase">Date</p><p class="mt-0.5 font-medium text-gray-800">{{ $event->event_date->format('l, F d, Y') }}</p></div>
                    <div><p class="text-xs text-gray-400 font-semibold uppercase">Time</p><p class="mt-0.5 font-medium text-gray-800">{{ \Carbon\Carbon::parse($event->start_time)->format('g:i A') }}{{ $event->end_time ? ' – '.\Carbon\Carbon::parse($event->end_time)->format('g:i A') : '' }}</p></div>
                    <div><p class="text-xs text-gray-400 font-semibold uppercase">Venue</p><p class="mt-0.5 font-medium text-gray-800">{{ $event->venue }}</p></div>
                    <div><p class="text-xs text-gray-400 font-semibold uppercase">Participants</p><p class="mt-0.5 font-medium text-gray-800">{{ $event->participant_limit ? 'Max '.$event->participant_limit : 'Unlimited' }}</p></div>
                </div>

                <div class="mb-5 pt-5 border-t border-gray-100">
                    <p class="text-xs font-semibold text-gray-400 uppercase mb-2">Description</p>
                    <div class="text-sm text-gray-700 leading-relaxed">{!! nl2br(e($event->description)) !!}</div>
                </div>

                @if($event->requirements->isNotEmpty())
                    <div class="pt-5 border-t border-gray-100">
                        <p class="text-xs font-semibold text-gray-400 uppercase mb-2">Requirements</p>
                        <ul class="space-y-1.5">
                            @foreach($event->requirements as $req)
                                <li class="flex items-center gap-2 text-sm text-gray-600">
                                    <svg class="w-4 h-4 text-comsoc-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

        {{-- Review form or status card --}}
        @if(in_array($event->status, [\App\Models\Event::STATUS_SUBMITTED, \App\Models\Event::STATUS_UNDER_REVIEW]))
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6" x-data="{ action: 'approved' }">
                <h3 class="text-base font-bold text-gray-800 mb-5">Your Decision</h3>

                <form method="POST" action="{{ route('events.approval.review', $event) }}" class="space-y-5">
                    @csrf

                    {{-- Action selector --}}
                    <div class="grid grid-cols-3 gap-3">
                        @foreach([
                            ['approved', 'Approve', 'border-comsoc-500 bg-comsoc-50 text-comsoc-700', 'border-gray-200 bg-white text-gray-600'],
                            ['returned', 'Return for Revision', 'border-orange-400 bg-orange-50 text-orange-700', 'border-gray-200 bg-white text-gray-600'],
                            ['rejected', 'Reject', 'border-red-400 bg-red-50 text-red-700', 'border-gray-200 bg-white text-gray-600'],
                        ] as [$val, $label, $activeClass, $inactiveClass])
                            <label class="relative cursor-pointer">
                                <input type="radio" name="action" value="{{ $val }}"
                                       x-model="action" class="sr-only" {{ $val === 'approved' ? 'checked' : '' }}>
                                <div :class="action === '{{ $val }}' ? '{{ $activeClass }}' : '{{ $inactiveClass }}'"
                                     class="block text-center px-3 py-3 border-2 rounded-xl text-sm font-semibold transition-all">
                                    {{ $label }}
                                </div>
                            </label>
                        @endforeach
                    </div>

                    {{-- Remarks --}}
                    <div>
                        <x-input-label for="remarks" value="Remarks" />
                        <p class="text-xs text-gray-400 mt-0.5 mb-1">
                            Required when returning or rejecting. Optional for approval.
                        </p>
                        <textarea id="remarks" name="remarks" rows="4"
                                  :required="action !== 'approved'"
                                  placeholder="Provide your remarks or feedback..."
                                  class="mt-1 block w-full border-gray-300 focus:border-comsoc-500 focus:ring-comsoc-500 rounded-md shadow-sm text-sm resize-none">{{ old('remarks') }}</textarea>
                        <x-input-error :messages="$errors->get('remarks')" class="mt-1" />
                    </div>

                    {{-- Submit --}}
                    <div class="flex items-center gap-3">
                        <button type="submit"
                                :class="action === 'approved' ? 'bg-comsoc-600 hover:bg-comsoc-700' : (action === 'returned' ? 'bg-orange-600 hover:bg-orange-700' : 'bg-red-600 hover:bg-red-700')"
                                class="px-6 py-2.5 text-white text-sm font-bold rounded-lg transition-colors shadow-sm">
                            <span x-text="action === 'approved' ? 'Approve Event' : (action === 'returned' ? 'Return for Revision' : 'Reject Event')"></span>
                        </button>
                        <a href="{{ route('events.approval.index') }}"
                           class="text-sm text-gray-500 hover:text-gray-700 transition-colors">Cancel</a>
                    </div>
                </form>
            </div>
        @else
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full mb-3 {{ $event->status === \App\Models\Event::STATUS_APPROVED ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-600' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-gray-800">Event {{ $event->getStatusLabel() }}</h3>
                <p class="text-sm text-gray-500 mt-1 max-w-md mx-auto">
                    This proposal is marked as <strong>{{ $event->getStatusLabel() }}</strong> and does not require further review.
                </p>
                <div class="mt-4 flex items-center justify-center gap-3">
                    <a href="{{ route('events.approval.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition-colors">
                        ← Back to Approval Queue
                    </a>
                    <a href="{{ route('events.show', $event) }}" class="px-4 py-2 bg-comsoc-600 hover:bg-comsoc-700 text-white rounded-lg text-sm font-medium transition-colors">
                        View Event Page →
                    </a>
                </div>
            </div>
        @endif

        {{-- Previous approvals --}}
        @if($event->approvals->isNotEmpty())
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
                <p class="text-sm font-bold text-gray-800 mb-3">Previous Decisions</p>
                <div class="space-y-3">
                    @foreach($event->approvals as $a)
                        <div class="flex items-start gap-3">
                            <span class="inline-flex shrink-0 px-2 py-0.5 rounded-full text-xs font-semibold mt-0.5
                                         {{ $a->action === 'approved' ? 'bg-comsoc-100 text-comsoc-700' : ($a->action === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-orange-100 text-orange-700') }}">
                                {{ ucfirst($a->action) }}
                            </span>
                            <div>
                                <p class="text-xs text-gray-400">{{ $a->reviewer->name }} · {{ $a->created_at->format('M d, Y') }}</p>
                                @if($a->remarks)<p class="text-sm text-gray-600 mt-0.5">{{ $a->remarks }}</p>@endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-dashboard-layout>
