<x-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('documents.index') }}" class="hover:text-gray-900 transition-colors">Documents</a>
            <span>/</span>
            <a href="{{ route('documents.show', $document) }}" class="hover:text-gray-900 transition-colors">{{ Str::limit($document->title, 24) }}</a>
            <span>/</span>
            <span class="text-gray-800 font-semibold">Edit & Revise</span>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            {{-- Header --}}
            <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-comsoc-900 to-comsoc-950 text-white">
                <h2 class="text-lg font-bold">Edit / Revise Document</h2>
                <p class="text-xs text-comsoc-300">Update metadata or replace the file attachment with a revised version.</p>
            </div>

            {{-- Form --}}
            <form method="POST" action="{{ route('documents.update', $document) }}" enctype="multipart/form-data" class="p-6 space-y-5">
                @csrf
                @method('PUT')

                {{-- Title --}}
                <div>
                    <label for="title" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Document Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="title"
                           name="title"
                           value="{{ old('title', $document->title) }}"
                           required
                           class="w-full border-gray-300 focus:border-comsoc-500 focus:ring-comsoc-500 rounded-xl text-sm @error('title') border-red-500 @enderror"/>
                    @error('title')
                        <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Category & Event --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="category" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                            Document Category <span class="text-red-500">*</span>
                        </label>
                        <select id="category"
                                name="category"
                                required
                                class="w-full border-gray-300 focus:border-comsoc-500 focus:ring-comsoc-500 rounded-xl text-sm @error('category') border-red-500 @enderror">
                            @foreach($categories as $key => $label)
                                <option value="{{ $key }}" {{ old('category', $document->category) === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="event_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                            Associated Event
                        </label>
                        <select id="event_id"
                                name="event_id"
                                class="w-full border-gray-300 focus:border-comsoc-500 focus:ring-comsoc-500 rounded-xl text-sm">
                            <option value="">None (Organization General)</option>
                            @foreach($events as $event)
                                <option value="{{ $event->id }}" {{ old('event_id', $document->event_id) == $event->id ? 'selected' : '' }}>
                                    {{ $event->title }} ({{ $event->event_date->format('M d, Y') }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Replace File Dropzone --}}
                <div x-data="{ fileName: '', fileSize: '' }">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Replace Attachment File <span class="text-gray-400 font-normal">(Leave blank to keep current: {{ $document->file_name }})</span>
                    </label>
                    <div onclick="document.getElementById('file').click()"
                         class="border-2 border-dashed border-gray-300 hover:border-comsoc-500 bg-gray-50 hover:bg-comsoc-50/20 rounded-2xl p-6 text-center cursor-pointer transition">
                        <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <p class="text-xs font-bold text-gray-700">Click to replace file (bumps version to v{{ $document->version + 1 }})</p>
                        <p class="text-[10px] text-gray-400 mt-1">PDF, DOCX, XLSX, PPTX, ZIP, Images (Max 15MB)</p>
                        
                        <input type="file"
                               id="file"
                               name="file"
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

                {{-- Notes --}}
                <div>
                    <label for="notes" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Revision Notes / Description
                    </label>
                    <textarea id="notes"
                              name="notes"
                              rows="3"
                              class="w-full border-gray-300 focus:border-comsoc-500 focus:ring-comsoc-500 rounded-xl text-sm">{{ old('notes', $document->notes) }}</textarea>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                    <a href="{{ route('documents.show', $document) }}"
                       class="px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-800 transition">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-2.5 bg-comsoc-700 hover:bg-comsoc-800 text-white text-sm font-bold rounded-xl transition shadow-sm">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-dashboard-layout>
