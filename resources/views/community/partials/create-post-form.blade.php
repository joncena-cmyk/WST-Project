{{-- ===== CREATE POST FORM — Facebook-style ===== --}}
<div x-data="{ expanded: false, isOfficial: false, previews: [], charCount: 0 }"
     class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden transition-all duration-200">

    {{-- Collapsed trigger bar --}}
    <div x-show="!expanded"
         @click="expanded = true; $nextTick(() => $el.closest('.bg-white').querySelector('textarea')?.focus())"
         class="flex items-center gap-3 p-3.5 sm:p-4 cursor-pointer hover:bg-gray-50/80 transition-colors">
        <img src="{{ Auth::user()->getProfilePictureUrl() }}"
             class="w-10 h-10 rounded-full object-cover shrink-0 border-2 border-comsoc-100 shadow-sm" alt=""/>
        <div class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 rounded-full text-sm text-gray-400 font-medium select-none transition-colors">
        What's on your mind, {{ explode(' ', Auth::user()->name)[0] }}?
        </div>
    </div>

    {{-- Expanded form --}}
    <div x-show="expanded" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-98" x-transition:enter-end="opacity-100 scale-100">
        <form method="POST"
              action="{{ route('community.store') }}"
              enctype="multipart/form-data">
            @csrf

            {{-- Official post banner toggle (officers+) --}}
            @if(Auth::user()->isOfficerOrAbove())
                <div class="px-4 pt-3 flex items-center gap-2">
                    <button type="button"
                            @click="isOfficial = !isOfficial"
                            :class="isOfficial ? 'bg-comsoc-700 text-white border-comsoc-700 shadow-sm' : 'bg-white text-gray-500 border-gray-200 hover:border-comsoc-300'"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-xs font-semibold transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <span x-text="isOfficial ? '✓ Official Post' : 'Mark as Official'"></span>
                    </button>
                    <input type="hidden" name="is_official" :value="isOfficial ? '1' : '0'">
                </div>
            @endif

            {{-- Official badge preview strip --}}
            <div x-show="isOfficial" x-cloak
                 class="mx-4 mt-3 flex items-center gap-2 px-3 py-2 bg-comsoc-50 border border-comsoc-200 rounded-xl">
                <svg class="w-4 h-4 text-comsoc-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <span class="text-xs font-bold text-comsoc-700 uppercase tracking-wide">COMSOC Official Announcement</span>
            </div>

            {{-- Author row --}}
            <div class="flex items-center gap-3 px-4 pt-3 pb-1">
                <img src="{{ Auth::user()->getProfilePictureUrl() }}"
                     class="w-10 h-10 rounded-full object-cover shrink-0 border-2 border-comsoc-100" alt=""/>
                <div>
                    <p class="text-sm font-bold text-gray-900">{{ Auth::user()->name }}</p>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-gray-100 rounded-full text-[11px] text-gray-600 font-medium">
                            <svg class="w-3 h-3 text-comsoc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 004 0 2 2 0 012-2h1.064"/>
                            </svg>
                            <span>{{ Auth::user()->getRoleLabel() }}</span>
                        </span>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-comsoc-50 text-comsoc-700 rounded-full text-[11px] font-semibold border border-comsoc-100">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            COMSOC
                        </span>
                    </div>
                </div>
            </div>

            {{-- Textarea body --}}
            <div class="px-4 pb-2">
                <textarea name="body"
                          rows="3"
                          required
                          maxlength="5000"
                          x-on:input="charCount = $el.value.length"
                          placeholder="Share something with COMSOC..."
                          class="w-full resize-none border-0 focus:ring-0 text-gray-800 text-base sm:text-sm placeholder-gray-400 p-0 bg-transparent leading-relaxed min-h-[80px]">{{ old('body') }}</textarea>
                @error('body')
                    <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Image previews grid --}}
            <div x-show="previews.length > 0" x-cloak class="px-4 pb-3">
                <div class="rounded-xl overflow-hidden"
                     :class="previews.length === 1 ? '' : 'grid grid-cols-2 gap-1'">
                    <template x-for="(src, idx) in previews" :key="idx">
                        <div class="relative bg-gray-100" :class="previews.length === 1 ? 'rounded-xl' : (idx === 0 && previews.length === 3 ? 'row-span-2' : '')">
                            <img :src="src" class="w-full object-cover" :class="previews.length === 1 ? 'max-h-80 rounded-xl' : 'aspect-square'"/>
                            <button type="button"
                                    @click="previews.splice(idx, 1)"
                                    class="absolute top-1.5 right-1.5 w-6 h-6 rounded-full bg-black/60 text-white flex items-center justify-center hover:bg-black/80 transition text-xs font-bold">
                                ✕
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Toolbar --}}
            <div class="flex items-center justify-between px-4 py-3 border-t border-gray-100">
                <div class="flex items-center gap-1">
                    {{-- Photo upload --}}
                    <label class="flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 hover:text-emerald-600 rounded-xl cursor-pointer transition-colors">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="hidden sm:inline">Photo</span>
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
                    {{-- Char count --}}
                    <span x-show="charCount > 3500"
                          :class="charCount > 4800 ? 'text-red-500' : 'text-amber-500'"
                          class="text-xs font-medium tabular-nums"
                          x-text="(5000 - charCount) + ' left'"></span>

                    <button type="button"
                            @click="expanded = false; previews = []; charCount = 0"
                            class="px-4 py-2 text-sm font-semibold text-gray-500 hover:bg-gray-100 rounded-xl transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-5 py-2 bg-comsoc-600 hover:bg-comsoc-700 active:bg-comsoc-800 text-white text-sm font-bold rounded-xl transition-colors shadow-sm">
                        Post
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
