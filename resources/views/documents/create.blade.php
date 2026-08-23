<x-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('documents.index') }}" class="hover:text-gray-900 transition-colors">Documents</a>
            <span>/</span>
            <span class="text-gray-800 font-semibold">Upload Document</span>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            {{-- Header --}}
            <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-comsoc-900 to-comsoc-950 text-white">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center">
                        <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold">Upload Organization Document</h2>
                        <p class="text-xs text-comsoc-300">Submit proposals, letters, and forms for Adviser review and recordkeeping.</p>
                    </div>
                </div>
            </div>

            {{-- Form --}}
            <form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data" class="p-6 space-y-5">
                @csrf

                {{-- Document Title --}}
                <div>
                    <label for="title" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Document Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="title"
                           name="title"
                           value="{{ old('title') }}"
                           required
                           placeholder="e.g., Venue Reservation Letter for Tech Summit 2026"
                           class="w-full border-gray-300 focus:border-comsoc-500 focus:ring-comsoc-500 rounded-xl text-sm @error('title') border-red-500 @enderror"/>
                    @error('title')
                        <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Category & Event association in 2-col --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Category --}}
                    <div>
                        <label for="category" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                            Document Category <span class="text-red-500">*</span>
                        </label>
                        <select id="category"
                                name="category"
                                required
                                class="w-full border-gray-300 focus:border-comsoc-500 focus:ring-comsoc-500 rounded-xl text-sm @error('category') border-red-500 @enderror">
                            <option value="">Select Category...</option>
                            @foreach($categories as $key => $label)
                                <option value="{{ $key }}" {{ old('category') === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('category')
                            <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Linked Event --}}
                    <div>
                        <label for="event_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                            Associated Event <span class="text-gray-400 font-normal">(Optional)</span>
                        </label>
                        <select id="event_id"
                                name="event_id"
                                class="w-full border-gray-300 focus:border-comsoc-500 focus:ring-comsoc-500 rounded-xl text-sm @error('event_id') border-red-500 @enderror">
                            <option value="">None (Organization General)</option>
                            @foreach($events as $event)
                                <option value="{{ $event->id }}"
                                    {{ (old('event_id') == $event->id || (isset($selectedEvent) && $selectedEvent->id == $event->id)) ? 'selected' : '' }}>
                                    {{ $event->title }} ({{ $event->event_date->format('M d, Y') }})
                                </option>
                            @endforeach
                        </select>
                        @error('event_id')
                            <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- File Upload Dropzone --}}
                <div x-data="{ fileName: '', fileSize: '' }">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Attachment File <span class="text-red-500">*</span>
                    </label>
                    <div onclick="document.getElementById('file').click()"
                         class="border-2 border-dashed border-gray-300 hover:border-comsoc-500 bg-gray-50 hover:bg-comsoc-50/20 rounded-2xl p-6 text-center cursor-pointer transition">
                        <svg class="w-10 h-10 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <p class="text-sm font-bold text-gray-700">Click to browse file</p>
                        <p class="text-xs text-gray-400 mt-1">Supported: PDF, DOCX, XLSX, PPTX, ZIP, Images (Max 15MB)</p>
                        
                        <input type="file"
                               id="file"
                               name="file"
                               required
                               @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''; fileSize = $event.target.files[0] ? ($event.target.files[0].size / 1024 / 1024).toFixed(2) + ' MB' : ''"
                               class="hidden"/>

                        <div x-show="fileName" class="mt-3 inline-flex items-center gap-2 px-3 py-1.5 bg-comsoc-100 text-comsoc-900 rounded-lg text-xs font-bold">
                            <svg class="w-4 h-4 text-comsoc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span x-text="fileName"></span> (<span x-text="fileSize"></span>)
                        </div>
                    </div>
                    @error('file')
                        <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Notes / Description --}}
                <div>
                    <label for="notes" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Submitter Notes / Background <span class="text-gray-400 font-normal">(Optional)</span>
                    </label>
                    <textarea id="notes"
                              name="notes"
                              rows="3"
                              placeholder="Add any context, deadlines, or instructions for the adviser..."
                              class="w-full border-gray-300 focus:border-comsoc-500 focus:ring-comsoc-500 rounded-xl text-sm">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Security Notice --}}
                <div class="p-3.5 bg-gray-50 border border-gray-200 rounded-xl flex items-start gap-2.5 text-xs text-gray-600">
                    <svg class="w-4 h-4 text-gray-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <span><strong>Private Storage:</strong> This document is encrypted and stored in private organization storage. It can only be downloaded by authorized officers and advisers.</span>
                </div>

                {{-- Buttons --}}
                <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                    <a href="{{ route('documents.index') }}"
                       class="px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-800 transition">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-2.5 bg-comsoc-700 hover:bg-comsoc-800 text-white text-sm font-bold rounded-xl transition shadow-sm">
                        Save as Draft
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-dashboard-layout>
