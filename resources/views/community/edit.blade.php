<x-dashboard-layout>
    <x-slot name="header">
        <h1 class="text-base font-bold text-gray-900">Edit Post</h1>
    </x-slot>

    <div class="max-w-2xl mx-auto px-0 sm:px-2">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

            {{-- Header --}}
            <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-3">
                <img src="{{ Auth::user()->getProfilePictureUrl() }}"
                     class="w-10 h-10 rounded-full object-cover border-2 border-comsoc-100" alt=""/>
                <div>
                    <p class="text-sm font-bold text-gray-900">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-400 font-medium">{{ Auth::user()->getRoleLabel() }}</p>
                </div>
            </div>

            {{-- Form --}}
            <form method="POST" action="{{ route('community.update', $post) }}">
                @csrf
                @method('PATCH')

                <div class="px-5 py-4">
                    <textarea id="body"
                              name="body"
                              rows="6"
                              required
                              maxlength="5000"
                              placeholder="What's on your mind?"
                              class="w-full resize-none border-0 focus:ring-0 text-gray-800 text-sm sm:text-base placeholder-gray-400 bg-transparent leading-relaxed">{{ old('body', $post->body) }}</textarea>
                    <x-input-error :messages="$errors->get('body')" class="mt-2"/>
                </div>

                {{-- Footer actions --}}
                <div class="flex items-center justify-between px-5 py-3 border-t border-gray-100 bg-gray-50/50 flex-wrap gap-3">
                    <a href="{{ route('community.index') }}"
                       class="flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 font-semibold transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Back to Community
                    </a>
                    <div class="flex items-center gap-2.5">
                        <a href="{{ route('community.index') }}"
                           class="px-4 py-2 text-sm font-semibold text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                            Cancel
                        </a>
                        <button type="submit"
                                class="px-5 py-2 bg-comsoc-600 hover:bg-comsoc-700 text-white text-sm font-bold rounded-xl transition-colors shadow-sm">
                            Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-dashboard-layout>
