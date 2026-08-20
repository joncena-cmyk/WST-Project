<x-dashboard-layout>
    <x-slot name="header">
        <h1 class="text-lg font-semibold text-gray-800">Edit Post</h1>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <form method="POST" action="{{ route('community.update', $post) }}">
                @csrf
                @method('PATCH')

                <div class="mb-4">
                    <x-input-label for="body" :value="__('Post content')" />
                    <textarea id="body"
                              name="body"
                              rows="6"
                              required
                              maxlength="5000"
                              class="mt-1 block w-full border-gray-300 focus:border-comsoc-500 focus:ring-comsoc-500 rounded-xl shadow-sm text-sm resize-none">{{ old('body', $post->body) }}</textarea>
                    <x-input-error :messages="$errors->get('body')" class="mt-2" />
                </div>

                <div class="flex items-center justify-between">
                    <a href="{{ route('community.index') }}"
                       class="text-sm text-gray-500 hover:text-gray-700 transition-colors">
                        ← Back to community
                    </a>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('community.index') }}"
                           class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            Cancel
                        </a>
                        <x-primary-button>Save changes</x-primary-button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-dashboard-layout>
