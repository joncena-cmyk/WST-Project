{{-- ===== POST CARD — Facebook-inspired, COMSOC-branded, mobile-first ===== --}}
<article class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-200"
         id="post-{{ $post->id }}">

    {{-- OFFICIAL ANNOUNCEMENT BANNER --}}
    @if ($post->isOfficial())
        <div class="flex items-center gap-2.5 px-4 py-3 bg-gradient-to-r from-comsoc-800 to-comsoc-700 border-b border-comsoc-600">
            <div class="w-7 h-7 rounded-lg bg-gold-400/20 border border-gold-400/40 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-black text-white uppercase tracking-widest">COMSOC Official</p>
                <p class="text-[10px] text-comsoc-300 font-medium">Official Announcement</p>
            </div>
        </div>
    @endif

    {{-- POST HEADER --}}
    <div class="flex items-start justify-between p-3.5 sm:p-4">
        <div class="flex items-start gap-3 min-w-0 flex-1">
            <div class="relative shrink-0">
                <img src="{{ $post->author?->getProfilePictureUrl() ?? asset('images/comsoc_logo.png') }}"
                     class="w-10 h-10 rounded-full object-cover border-2 border-gray-100 shadow-sm" alt=""/>
                {{-- Online dot (visual only) --}}
                @if($post->created_at->gt(now()->subMinutes(30)))
                    <span class="absolute bottom-0 right-0 w-3 h-3 rounded-full bg-emerald-400 border-2 border-white"></span>
                @endif
            </div>
            <div class="min-w-0 flex-1">
                <div class="flex items-center gap-2 flex-wrap">
                    <p class="text-sm font-bold text-gray-900 leading-tight">{{ $post->author?->name ?? 'Deleted User' }}</p>
                    @if($post->author?->isOfficerOrAbove())
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-comsoc-100 text-comsoc-700 border border-comsoc-200">
                            {{ $post->author->getRoleLabel() }}
                        </span>
                    @endif
                </div>
                <div class="flex items-center gap-1.5 mt-0.5 flex-wrap">
                    <span class="text-xs text-gray-400">{{ $post->created_at->diffForHumans() }}</span>
                    @if($post->author?->program)
                        <span class="text-gray-300 text-xs">·</span>
                        <span class="text-xs text-comsoc-600 font-semibold">{{ $post->author->program->code }}</span>
                    @endif
                    {{-- Visibility indicator --}}
                    <span class="text-gray-300 text-xs">·</span>
                    <svg class="w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- 3-dot actions menu --}}
        <div x-data="{ open: false }" class="relative shrink-0 ml-2">
            <button @click="open = !open"
                    class="w-9 h-9 flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-full transition-colors">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <circle cx="12" cy="5" r="1.5"/>
                    <circle cx="12" cy="12" r="1.5"/>
                    <circle cx="12" cy="19" r="1.5"/>
                </svg>
            </button>

            <div x-show="open" x-cloak @click.outside="open = false"
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="absolute right-0 mt-1 w-52 bg-white border border-gray-200 rounded-2xl shadow-xl z-30 py-2 overflow-hidden">

                @can('update', $post)
                    <a href="{{ route('community.edit', $post) }}"
                       class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                        <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-500 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </span>
                        <span class="font-medium">Edit post</span>
                    </a>
                @endcan

                @can('delete', $post)
                    <form method="POST" action="{{ route('community.destroy', $post) }}">
                        @csrf @method('DELETE')
                        <button type="submit"
                                onclick="return confirm('Delete this post permanently?')"
                                class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                            <span class="w-8 h-8 rounded-lg bg-red-50 text-red-500 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </span>
                            <span class="font-medium">Delete post</span>
                        </button>
                    </form>
                @endcan

                @if(Auth::id() !== $post->user_id)
                    <div class="border-t border-gray-100 mt-1 pt-1">
                        <div x-data="{ showReport: false }">
                            <button @click="showReport = !showReport"
                                    class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                <span class="w-8 h-8 rounded-lg bg-amber-50 text-amber-500 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/>
                                    </svg>
                                </span>
                                <span class="font-medium">Report post</span>
                            </button>
                            <div x-show="showReport" x-cloak class="px-4 pb-3 pt-1 border-t border-gray-100">
                                <form method="POST" action="{{ route('reports.store') }}">
                                    @csrf
                                    <input type="hidden" name="reportable_type" value="post">
                                    <input type="hidden" name="reportable_id" value="{{ $post->id }}">
                                    <select name="reason" required
                                            class="w-full text-xs border border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500 focus:border-comsoc-500 mb-2 py-2 px-3">
                                        <option value="">Select reason...</option>
                                        <option value="spam">Spam</option>
                                        <option value="inappropriate">Inappropriate Content</option>
                                        <option value="harassment">Harassment</option>
                                        <option value="misinformation">Misinformation</option>
                                        <option value="other">Other</option>
                                    </select>
                                    <button type="submit"
                                            class="w-full py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-xl transition-colors">
                                        Submit Report
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- POST BODY --}}
    <div class="px-4 sm:px-5 pb-3"
         x-data="{ expanded: false, needsTruncate: {{ strlen($post->body) > 300 ? 'true' : 'false' }} }">
        <p class="text-gray-800 text-sm sm:text-[15px] leading-relaxed whitespace-pre-line break-words"
           :class="!expanded && needsTruncate ? 'line-clamp-4' : ''">{{ $post->body }}</p>
        <button x-show="needsTruncate && !expanded"
                @click="expanded = true; needsTruncate = false"
                class="text-comsoc-600 text-sm font-semibold mt-1 hover:text-comsoc-800 transition-colors">
            See more
        </button>
    </div>

    {{-- IMAGES --}}
    @if ($post->images->isNotEmpty())
        @php $count = $post->images->count(); @endphp
        <div class="mx-0 mb-0">
            @if($count === 1)
                <img src="{{ asset('storage/' . $post->images->first()->path) }}"
                     alt="Post image"
                     class="w-full max-h-[500px] object-cover"
                     loading="lazy"/>
            @elseif($count === 2)
                <div class="grid grid-cols-2 gap-0.5">
                    @foreach($post->images as $image)
                        <img src="{{ asset('storage/' . $image->path) }}"
                             alt="Post image"
                             class="w-full aspect-square object-cover"
                             loading="lazy"/>
                    @endforeach
                </div>
            @elseif($count === 3)
                <div class="grid grid-cols-2 gap-0.5">
                    <img src="{{ asset('storage/' . $post->images[0]->path) }}"
                         alt="Post image"
                         class="w-full row-span-2 object-cover aspect-square"
                         loading="lazy"/>
                    <img src="{{ asset('storage/' . $post->images[1]->path) }}"
                         alt="Post image"
                         class="w-full object-cover aspect-square"
                         loading="lazy"/>
                    <img src="{{ asset('storage/' . $post->images[2]->path) }}"
                         alt="Post image"
                         class="w-full object-cover aspect-square"
                         loading="lazy"/>
                </div>
            @else
                <div class="grid grid-cols-2 gap-0.5">
                    @foreach($post->images->take(3) as $image)
                        <img src="{{ asset('storage/' . $image->path) }}"
                             alt="Post image"
                             class="w-full object-cover aspect-square"
                             loading="lazy"/>
                    @endforeach
                    <div class="relative aspect-square">
                        <img src="{{ asset('storage/' . $post->images[3]->path) }}"
                             alt="Post image"
                             class="w-full h-full object-cover"
                             loading="lazy"/>
                        @if($count > 4)
                            <div class="absolute inset-0 bg-black/60 flex items-center justify-center">
                                <span class="text-white text-2xl font-black">+{{ $count - 4 }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    @endif

    {{-- REACTION + COMMENT COUNTS --}}
    @php
        $likeCount    = $post->reactions->count();
        $commentCount = $post->allComments->count();
        $userLiked    = $post->reactions->contains('user_id', Auth::id());
    @endphp

    @if ($likeCount > 0 || $commentCount > 0)
        <div class="flex items-center justify-between px-4 py-2.5 text-xs text-gray-500 border-t border-gray-100">
            <div class="flex items-center gap-1.5">
                @if ($likeCount > 0)
                    <div class="flex items-center gap-1">
                        <span class="flex items-center justify-center w-5 h-5 rounded-full bg-gradient-to-br from-comsoc-500 to-comsoc-700 text-white text-[10px] shadow-sm">❤</span>
                        <span class="font-medium text-gray-600">{{ $likeCount }}</span>
                    </div>
                @endif
            </div>
            @if ($commentCount > 0)
                <button @click="$dispatch('toggle-comments-{{ $post->id }}')"
                        class="hover:underline text-gray-500 hover:text-gray-700 transition-colors font-medium">
                    {{ $commentCount }} {{ $commentCount === 1 ? 'comment' : 'comments' }}
                </button>
            @endif
        </div>
    @endif

    {{-- ACTION BUTTONS BAR --}}
    <div class="grid grid-cols-2 border-t border-gray-100">
        {{-- Like button --}}
        <button id="like-btn-{{ $post->id }}"
                data-post-id="{{ $post->id }}"
                data-liked="{{ $userLiked ? 'true' : 'false' }}"
                onclick="toggleLike({{ $post->id }})"
                class="like-btn flex items-center justify-center gap-2 py-3 text-sm font-bold transition-all active:scale-95 rounded-bl-2xl
                       {{ $userLiked ? 'text-comsoc-600 bg-comsoc-50/50' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700' }}">
            <svg class="w-5 h-5 transition-transform" id="like-icon-{{ $post->id }}"
                 fill="{{ $userLiked ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
            <span id="like-label-{{ $post->id }}">{{ $userLiked ? 'Liked' : 'Like' }}</span>
        </button>

        {{-- Comment toggle --}}
        <button x-data
                @click="$dispatch('toggle-comments-{{ $post->id }}')"
                class="flex items-center justify-center gap-2 py-3 text-sm font-bold text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition-all active:scale-95 rounded-br-2xl">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            Comment
        </button>
    </div>

    {{-- COMMENTS SECTION --}}
    <div x-data="{ open: {{ $post->allComments->count() > 0 ? 'true' : 'false' }} }"
         @toggle-comments-{{ $post->id }}.window="open = !open">
        <div x-show="open" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="border-t border-gray-100 bg-gray-50/50 px-4 py-4 space-y-4">

            {{-- Existing comments --}}
            @foreach ($post->comments as $comment)
                @include('community.partials.comment', ['comment' => $comment])
            @endforeach

            {{-- Add comment input --}}
            <form method="POST"
                  action="{{ route('community.comments.store', $post) }}"
                  class="flex items-center gap-2.5 sm:gap-3">
                @csrf
                <img src="{{ Auth::user()->getProfilePictureUrl() }}"
                     class="w-9 h-9 rounded-full object-cover shrink-0 border-2 border-white shadow-sm" alt=""/>
                <div class="flex-1 relative">
                    <input type="text"
                           name="body"
                           required
                           maxlength="2000"
                           placeholder="Write a comment..."
                           class="w-full text-sm bg-white border border-gray-200 rounded-full px-4 py-2.5 focus:ring-2 focus:ring-comsoc-400 focus:border-comsoc-400 placeholder-gray-400 pr-10 shadow-sm transition-shadow"/>
                    <button type="submit"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-comsoc-600 hover:text-comsoc-800 transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</article>
