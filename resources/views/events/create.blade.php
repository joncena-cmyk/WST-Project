<x-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('events.manage') }}" class="hover:text-gray-900">Events</a>
            <span>/</span>
            <span class="text-gray-800 font-semibold">Create Event</span>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 sm:p-8">
            <h2 class="text-lg font-bold text-gray-900 mb-6">New Event</h2>

            <form method="POST" action="{{ route('events.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                {{-- Title --}}
                <div>
                    <x-input-label for="title" value="Event Title" />
                    <x-text-input id="title" name="title" type="text"
                                  :value="old('title')" required
                                  placeholder="e.g. Cybersecurity Seminar 2025"
                                  class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('title')" class="mt-1" />
                </div>

                {{-- Description --}}
                <div>
                    <x-input-label for="description" value="Description" />
                    <textarea id="description" name="description" rows="5" required
                              class="mt-1 block w-full border-gray-300 focus:border-comsoc-500 focus:ring-comsoc-500 rounded-md shadow-sm text-sm resize-none"
                              placeholder="Describe the event...">{{ old('description') }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-1" />
                </div>

                {{-- Date / Time --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <x-input-label for="event_date" value="Event Date" />
                        <x-text-input id="event_date" name="event_date" type="date"
                                      :value="old('event_date')" required class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('event_date')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="start_time" value="Start Time" />
                        <x-text-input id="start_time" name="start_time" type="time"
                                      :value="old('start_time')" required class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('start_time')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="end_time" value="End Time (optional)" />
                        <x-text-input id="end_time" name="end_time" type="time"
                                      :value="old('end_time')" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('end_time')" class="mt-1" />
                    </div>
                </div>

                {{-- Venue --}}
                <div>
                    <x-input-label for="venue" value="Venue" />
                    <x-text-input id="venue" name="venue" type="text"
                                  :value="old('venue')" required
                                  placeholder="e.g. CCIS Auditorium"
                                  class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('venue')" class="mt-1" />
                </div>

                {{-- Registration settings --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="registration_deadline" value="Registration Deadline (optional)" />
                        <x-text-input id="registration_deadline" name="registration_deadline" type="datetime-local"
                                      :value="old('registration_deadline')" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('registration_deadline')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="participant_limit" value="Participant Limit (optional)" />
                        <x-text-input id="participant_limit" name="participant_limit" type="number"
                                      :value="old('participant_limit')" min="1"
                                      placeholder="Leave blank for unlimited"
                                      class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('participant_limit')" class="mt-1" />
                    </div>
                </div>

                {{-- Event image --}}
                <div>
                    <x-input-label for="image" value="Event Image (optional)" />
                    <input id="image" name="image" type="file"
                           accept="image/jpeg,image/png,image/webp"
                           class="mt-1 block w-full text-sm text-gray-500
                                  file:mr-4 file:py-2 file:px-3 file:rounded-md file:border-0
                                  file:text-xs file:font-medium file:bg-comsoc-50 file:text-comsoc-700
                                  hover:file:bg-comsoc-100 cursor-pointer" />
                    <p class="text-xs text-gray-400 mt-1">JPG, PNG or WebP. Max 4MB.</p>
                    <x-input-error :messages="$errors->get('image')" class="mt-1" />
                </div>

                {{-- Requirements --}}
                <div x-data="{ reqs: {{ json_encode(old('requirements', [''])) }} }">
                    <x-input-label value="Requirements (optional)" />
                    <p class="text-xs text-gray-400 mt-0.5 mb-2">List any requirements participants need to bring or fulfil.</p>
                    <div class="space-y-2">
                        <template x-for="(req, idx) in reqs" :key="idx">
                            <div class="flex gap-2">
                                <x-text-input type="text" name="requirements[]"
                                              x-model="reqs[idx]"
                                              placeholder="e.g. Student ID required"
                                              class="flex-1 text-sm" />
                                <button type="button"
                                        @click="reqs.splice(idx, 1)"
                                        class="px-2 text-gray-400 hover:text-red-500 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </template>
                    </div>
                    <button type="button"
                            @click="reqs.push('')"
                            class="mt-2 inline-flex items-center gap-1.5 text-sm text-comsoc-600 hover:text-comsoc-800 font-medium transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add requirement
                    </button>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-3 pt-2 border-t border-gray-100">
                    <x-primary-button>Save as Draft</x-primary-button>
                    <a href="{{ route('events.manage') }}"
                       class="text-sm text-gray-500 hover:text-gray-700 transition-colors">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-dashboard-layout>
