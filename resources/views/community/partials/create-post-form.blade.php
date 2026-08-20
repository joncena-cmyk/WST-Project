<div x-data="{ expanded: false, isOfficial: false, previews: [] }" class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

    {{-- Trigger bar --}}
    <div x-show="!expanded"
         @click="expanded = true"
         class="flex items-center gap-3 p-4 cursor-pointer hover:bg-gray-50 transition-colors">
        <img src="{{ Auth::user()->getProfilePictureUrl() }}"
             class="w-10 h-10 rounded-full object-cover shrink-0 border border-gray-200" alt=""/>
        <div class="flex-1 px-4 py-2.5 bg-gray-100 rounded-full text-sm text-gray-400 select-none">
            What's on your mind, {{ Auth::user()->name }}?
        </div>
    </div>

    {{-- Expanded form --}}
    <div x-show="expanded" x-cloak>
        <form method="POST"
              action="{{ route('community.store') }}"
              enctype="multipart/form-data"
              x-data>
            @csrf

            {{-- Official post toggle (officers+) --}}
            @if(Auth::user()->isOfficerOrAbove())
                <div class="flex items-center gap-3 px-4 pt-4">
                    <button type="button"
                            @click="isOfficial = !isOfficial"
                            :class="isOfficial
                                ? 'bg-comsoc-600 text-white border-comsoc-600'
                                : 'bg-white text-gray-600 border-gray-300'"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-xs font-semibold transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <span x-text="isOfficial ? 'Official Post' : 'Mark as Official'"></span>
                    </button>
                    <input type="hidden" name="is_official" :value="isOfficial ? '1' : '0'">
                </div>
            @endif

            {{-- Official badge preview --}}
            <div x-show="isOfficial" class="mx-4 mt-3 flex items-center gap-2 px-3 py-2 bg-comsoc-50 border border-comsoc-200 rounded-lg">
                <svg class="w-4 h-4 text-comsoc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <span class="text-xs font-bold text-comsoc-700 uppercase tracking-wide">COMSOC Official</span>
            </div>

            {{-- Author row --}}
            <div class="flex items-center gap-3 p-4">
                <img src="{{ Auth::user()->getProfilePictureUrl() }}"
                     class="w-10 h-10 rounded-full object-cover shrink-0 border border-gray-200" alt=""/>
                <div>
                    <p class="text-sm font-semibold text-gray-900">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-400">{{ Auth::user()->getRoleLabel() }}</p>
                </div>
            </div>

            {{-- Body --}}
            <div class="px-4 pb-3">
                <textarea name="body"
                          rows="3"
                          required
                          maxlength="5000"
                          placeholder="Share something with COMSOC..."
                          class="w-full resize-none border-0 focus:ring-0 text-gray-800 text-sm placeholder-gray-400 p-0 bg-transparent">{{ old('body') }}</textarea>
                @error('body')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Image previews --}}
            <div x-show="previews.length > 0" class="px-4 pb-3">
                <div class="grid grid-cols-2 gap-2">
                    <template x-for="(src, idx) in previews" :key="idx">
                        <div class="relative rounded-lg overflow-hidden aspect-video bg-gray-100">
                            <img :src="src" class="w-full h-full object-cover"/>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Toolbar --}}
            <div class="flex items-center justify-between px-4 py-3 border-t border-gray-100">
                <div class="flex items-center gap-1">
                    {{-- Image upload --}}
                    <label class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-500 hover:bg-gray-100 rounded-lg cursor-pointer transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Photo
                        <input type="file"
                               name="images[]"
                               multiple
                               accept="image/*"
                               class="hidden"
                               @change="
                                   previews = [];
                                   Array.from($event.target.files).slice(0,4).forEach(f => {
                                       const r = new FileReader();
                                       r.onload = e => previews.push(e.target.result);
                                       r.readAsDataURL(f);
                                   })
                               "/>
                    </label>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button"
                            @click="expanded = false; previews = []"
                            class="px-4 py-1.5 text-xs font-medium text-gray-500 hover:bg-gray-100 rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-5 py-1.5 bg-comsoc-600 text-white text-xs font-semibold rounded-lg hover:bg-comsoc-700 transition-colors">
                        Post
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
