<x-dashboard-layout>
    <x-slot name="header">
        <h1 class="text-lg font-semibold text-gray-800">Community</h1>
    </x-slot>

    <div class="max-w-2xl mx-auto space-y-5">

        {{-- Flash messages --}}
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3500)"
                 x-transition
                 class="flex items-center gap-3 p-4 bg-comsoc-50 border border-comsoc-200 rounded-xl text-comsoc-700 text-sm">
                <svg class="w-5 h-5 text-comsoc-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Create post box --}}
        @can('create', App\Models\Post::class)
            @include('community.partials.create-post-form')
        @endcan

        {{-- Feed --}}
        @forelse ($posts as $post)
            @include('community.partials.post-card', compact('post'))
        @empty
            <div class="bg-white border border-gray-200 rounded-xl p-12 text-center shadow-sm">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
                </svg>
                <p class="text-gray-500 font-medium">No posts yet.</p>
                <p class="text-gray-400 text-sm mt-1">Be the first to share something with COMSOC.</p>
            </div>
        @endforelse

        {{-- Pagination --}}
        <div class="pb-4">
            {{ $posts->links() }}
        </div>
    </div>
</x-dashboard-layout>
