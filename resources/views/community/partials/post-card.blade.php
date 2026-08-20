<div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden" id="post-{{ $post->id }}">

    {{-- Official badge --}}
    @if ($post->isOfficial())
        <div class="flex items-center gap-2 px-4 py-2.5 bg-comsoc-700 border-b border-comsoc-600">
            <svg class="w-4 h-4 text-comsoc-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            <span class="text-xs font-bold text-comsoc-100 uppercase tracking-widest">COMSOC Official</span>
        </div>
    @endif

    {{-- Post header --}}
    <div class="flex items-start justify-between p-4 pb-0">
        <div class="flex items-center gap-3">
            <img src="{{ $post->author?->getProfilePictureUrl() ?? asset('images/comsoc_logo.png') }}"
                 class="w-10 h-10 rounded-full object-cover border border-gray-200 shrink-0" alt=""/>
            <div>
                <p class="text-sm font-semibold text-gray-900">{{ $post->author?->name ?? 'Deleted User' }}</p>
                <div class="flex items-center gap-1.5 mt-0.5">
                    <span class="text-xs text-gray-400">{{ $post->created_at->diffForHumans() }}</span>
                    @if($post->author?->program)
                        <span class="text-gray-300">·</span>
                        <span class="text-xs text-comsoc-600 font-medium">{{ $post->author->program->code }}</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Actions dropdown --}}
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open"
                    class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <circle cx="12" cy="5" r="1.5"/>
                    <circle cx="12" cy="12" r="1.5"/>
                    <circle cx="12" cy="19" r="1.5"/>
                </svg>
            </button>
            <div x-show="open" x-cloak @click.outside="open = false"
                 class="absolute right-0 mt-1 w-44 bg-white border border-gray-200 rounded-xl shadow-lg z-20 py-1 text-sm">

                @can('update', $post)
                    <a href="{{ route('community.edit', $post) }}"
                       class="flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-gray-50">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit post
                    </a>
                @endcan

                @can('delete', $post)
                    <form method="POST" action="{{ route('community.destroy', $post) }}">
                        @csrf @method('DELETE')
                        <button type="submit"
                                onclick="return confirm('Delete this post?')"
                                class="w-full flex items-center gap-2 px-4 py-2 text-red-600 hover:bg-red-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Delete post
                        </button>
                    </form>
                @endcan

                @if(Auth::id() !== $post->user_id)
                    {{-- Report --}}
                    <div x-data="{ showReport: false }">
                        <button @click="showReport = !showReport"
                                class="w-full flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-gray-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/>
                            </svg>
                            Report
                        </button>
                        <div x-show="showReport" x-cloak class="px-4 pb-3 pt-1">
                            <form method="POST" action="{{ route('reports.store') }}">
                                @csrf
                                <input type="hidden" name="reportable_type" value="post">
                                <input type="hidden" name="reportable_id" value="{{ $post->id }}">
                                <select name="reason" required
                                        class="w-full text-xs border-gray-300 rounded-lg focus:ring-comsoc-500 focus:border-comsoc-500 mb-2">
                                    <option value="">Select reason...</option>
                                    <option value="spam">Spam</option>
                                    <option value="inappropriate">Inappropriate</option>
                                    <option value="harassment">Harassment</option>
                                    <option value="other">Other</option>
                                </select>
                                <button type="submit"
                                        class="w-full py-1.5 bg-red-600 text-white text-xs font-semibold rounded-lg hover:bg-red-700">
                                    Submit Report
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Post body --}}
    <div class="px-4 py-3">
        <p class="text-gray-800 text-sm leading-relaxed whitespace-pre-line">{{ $post->body }}</p>
    </div>

    {{-- Images --}}
    @if ($post->images->isNotEmpty())
        <div class="px-4 pb-3">
            <div class="{{ $post->images->count() === 1 ? '' : 'grid grid-cols-2 gap-1' }} rounded-xl overflow-hidden">
                @foreach ($post->images as $image)
                    <img src="{{ asset('storage/' . $image->path) }}"
                         alt="Post image"
                         class="{{ $post->images->count() === 1 ? 'w-full rounded-xl max-h-96 object-cover' : 'w-full aspect-square object-cover' }}"
                         loading="lazy"/>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Reaction / comment counts --}}
    @php
        $likeCount    = $post->reactions->count();
        $commentCount = $post->allComments->count();
        $userLiked    = $post->reactions->contains('user_id', Auth::id());
    @endphp

    @if ($likeCount > 0 || $commentCount > 0)
        <div class="flex items-center gap-4 px-4 py-2 border-t border-gray-100 text-xs text-gray-400">
            @if ($likeCount > 0)
                <span class="flex items-center gap-1">
                    <span class="flex items-center justify-center w-4 h-4 rounded-full bg-comsoc-600 text-white text-[10px]">♥</span>
                    {{ $likeCount }}
                </span>
            @endif
            @if ($commentCount > 0)
                <span>{{ $commentCount }} {{ $commentCount === 1 ? 'comment' : 'comments' }}</span>
            @endif
        </div>
    @endif

    {{-- Like / Comment actions --}}
    <div class="flex items-center border-t border-gray-100 divide-x divide-gray-100">
        {{-- Like button --}}
        <button
            id="like-btn-{{ $post->id }}"
            data-post-id="{{ $post->id }}"
            data-liked="{{ $userLiked ? 'true' : 'false' }}"
            onclick="toggleLike({{ $post->id }})"
            class="like-btn flex-1 flex items-center justify-center gap-2 py-2.5 text-sm font-medium transition-colors
                   {{ $userLiked ? 'text-comsoc-600' : 'text-gray-500 hover:bg-gray-50' }}">
            <svg class="w-4 h-4" fill="{{ $userLiked ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
            <span id="like-label-{{ $post->id }}">{{ $userLiked ? 'Liked' : 'Like' }}</span>
        </button>

        {{-- Comment toggle --}}
        <button
            x-data
            @click="$dispatch('toggle-comments-{{ $post->id }}')"
            class="flex-1 flex items-center justify-center gap-2 py-2.5 text-sm font-medium text-gray-500 hover:bg-gray-50 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            Comment
        </button>
    </div>

    {{-- Comments section --}}
    <div x-data="{ open: {{ $post->allComments->count() > 0 ? 'true' : 'false' }} }"
         @toggle-comments-{{ $post->id }}.window="open = !open">
        <div x-show="open" x-cloak class="border-t border-gray-100 px-4 py-3 space-y-3">

            {{-- Existing comments --}}
            @foreach ($post->comments as $comment)
                @include('community.partials.comment', ['comment' => $comment])
            @endforeach

            {{-- Add comment --}}
            <form method="POST"
                  action="{{ route('community.comments.store', $post) }}"
                  class="flex items-start gap-3 pt-2">
                @csrf
                <img src="{{ Auth::user()->getProfilePictureUrl() }}"
                     class="w-8 h-8 rounded-full object-cover shrink-0 border border-gray-200 mt-1" alt=""/>
                <div class="flex-1">
                    <input type="text"
                           name="body"
                           required
                           maxlength="2000"
                           placeholder="Write a comment..."
                           class="w-full text-sm bg-gray-100 border-0 rounded-full px-4 py-2 focus:ring-2 focus:ring-comsoc-500 placeholder-gray-400"/>
                </div>
            </form>
        </div>
    </div>
</div>
