<x-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('events.manage') }}" class="hover:text-gray-900">Events</a>
            <span>/</span>
            <a href="{{ route('events.show', $event) }}" class="hover:text-gray-900">{{ $event->title }}</a>
            <span>/</span>
            <span class="text-gray-800 font-semibold">Edit</span>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 sm:p-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-bold text-gray-900">Edit Event</h2>
                <span class="inline-flex px-3 py-1 rounded-full text-sm font-semibold {{ $event->getStatusColor() }}">
                    {{ $event->getStatusLabel() }}
                </span>
            </div>

            @if($event->approvals->isNotEmpty())
                @php $latest = $event->approvals->first(); @endphp
                @if($latest->action === 'returned')
                    <div class="mb-5 p-4 bg-orange-50 border border-orange-200 rounded-xl">
                        <p class="text-sm font-semibold text-orange-800">Returned for Revision</p>
                        <p class="text-sm text-orange-700 mt-1">{{ $latest->remarks ?? 'No specific remarks provided.' }}</p>
                        <p class="text-xs text-orange-500 mt-1">— {{ $latest->reviewer->name }}, {{ $latest->created_at->format('M d, Y') }}</p>
                    </div>
                @endif
            @endif

            <form method="POST" action="{{ route('events.update', $event) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PATCH')

                <div>
                    <x-input-label for="title" value="Event Title" />
                    <x-text-input id="title" name="title" type="text"
                                  :value="old('title', $event->title)" required class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('title')" class="mt-1" />
                </div>

                <div>
                    <x-input-label for="description" value="Description" />
                    <textarea id="description" name="description" rows="5" required
                              class="mt-1 block w-full border-gray-300 focus:border-comsoc-500 focus:ring-comsoc-500 rounded-md shadow-sm text-sm resize-none">{{ old('description', $event->description) }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-1" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <x-input-label for="event_date" value="Event Date" />
                        <x-text-input id="event_date" name="event_date" type="date"
                                      :value="old('event_date', $event->event_date->format('Y-m-d'))"
                                      required class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('event_date')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="start_time" value="Start Time" />
                        <x-text-input id="start_time" name="start_time" type="time"
                                      :value="old('start_time', substr($event->start_time, 0, 5))"
                                      required class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('start_time')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="end_time" value="End Time (optional)" />
                        <x-text-input id="end_time" name="end_time" type="time"
                                      :value="old('end_time', $event->end_time ? substr($event->end_time, 0, 5) : '')"
                                      class="mt-1 block w-full" />
                    </div>
                </div>

                <div>
                    <x-input-label for="venue" value="Venue" />
                    <x-text-input id="venue" name="venue" type="text"
                                  :value="old('venue', $event->venue)" required class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('venue')" class="mt-1" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="registration_deadline" value="Registration Deadline" />
                        <x-text-input id="registration_deadline" name="registration_deadline" type="datetime-local"
                                      :value="old('registration_deadline', $event->registration_deadline?->format('Y-m-d\TH:i'))"
                                      class="mt-1 block w-full" />
                    </div>
                    <div>
                        <x-input-label for="participant_limit" value="Participant Limit" />
                        <x-text-input id="participant_limit" name="participant_limit" type="number"
                                      :value="old('participant_limit', $event->participant_limit)"
                                      min="1" class="mt-1 block w-full" />
                    </div>
                </div>

                <div>
                    <x-input-label for="image" value="Event Image" />
                    @if($event->getImageUrl())
                        <img src="{{ $event->getImageUrl() }}" class="mt-2 h-28 rounded-lg object-cover mb-2"/>
                    @endif
                    <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp"
                           class="mt-1 block w-full text-sm text-gray-500
                                  file:mr-4 file:py-2 file:px-3 file:rounded-md file:border-0
                                  file:text-xs file:font-medium file:bg-comsoc-50 file:text-comsoc-700
                                  hover:file:bg-comsoc-100 cursor-pointer" />
                    <x-input-error :messages="$errors->get('image')" class="mt-1" />
                </div>

                <div x-data="{ reqs: {{ json_encode(old('requirements', $event->requirements->pluck('description')->toArray() ?: [''])) }} }">
                    <x-input-label value="Requirements" />
                    <div class="space-y-2 mt-2">
                        <template x-for="(req, idx) in reqs" :key="idx">
                            <div class="flex gap-2">
                                <x-text-input type="text" name="requirements[]" x-model="reqs[idx]"
                                              placeholder="Requirement..." class="flex-1 text-sm" />
                                <button type="button" @click="reqs.splice(idx, 1)"
                                        class="px-2 text-gray-400 hover:text-red-500 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </template>
                    </div>
                    <button type="button" @click="reqs.push('')"
                            class="mt-2 inline-flex items-center gap-1.5 text-sm text-comsoc-600 hover:text-comsoc-800 font-medium transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add requirement
                    </button>
                </div>

                <div class="flex items-center gap-3 pt-2 border-t border-gray-100">
                    <x-primary-button>Save Changes</x-primary-button>
                    <a href="{{ route('events.show', $event) }}"
                       class="text-sm text-gray-500 hover:text-gray-700 transition-colors">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-dashboard-layout>
