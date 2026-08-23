{{-- ===== COMMENT BUBBLE — Facebook-style, mobile-first ===== --}}
<div class="flex items-start gap-2.5" id="comment-{{ $comment->id }}">
    <img src="{{ $comment->author?->getProfilePictureUrl() ?? asset('images/comsoc_logo.png') }}"
         class="w-8 h-8 rounded-full object-cover shrink-0 border-2 border-white shadow-sm mt-0.5" alt=""/>

    <div class="flex-1 min-w-0">
        {{-- Bubble --}}
        <div class="inline-block max-w-full bg-white rounded-2xl rounded-tl-sm px-3.5 py-2.5 shadow-sm border border-gray-100">
            <div class="flex items-center gap-1.5 flex-wrap mb-0.5">
                <span class="text-xs font-bold text-gray-900">{{ $comment->author?->name ?? 'Deleted User' }}</span>
                @if($comment->author?->isOfficerOrAbove())
                    <span class="inline-flex items-center px-1.5 py-0 rounded text-[10px] font-bold bg-comsoc-100 text-comsoc-700">
                        {{ $comment->author->getRoleLabel() }}
                    </span>
                @endif
            </div>
            <p class="text-sm text-gray-700 leading-snug break-words whitespace-pre-line">{{ $comment->body }}</p>
        </div>

        {{-- Timestamp + actions --}}
        <div class="flex items-center gap-3 mt-1.5 px-1 flex-wrap">
            <span class="text-[11px] text-gray-400 font-medium">{{ $comment->created_at->diffForHumans() }}</span>

            @if (!$comment->isReply())
                <button x-data
                        @click="$dispatch('toggle-reply-{{ $comment->id }}')"
                        class="text-[11px] font-bold text-gray-500 hover:text-comsoc-600 transition-colors">
                    Reply
                </button>
            @endif

            @can('delete', $comment)
                <form method="POST" action="{{ route('community.comments.destroy', $comment) }}">
                    @csrf @method('DELETE')
                    <button type="submit"
                            onclick="return confirm('Delete this comment?')"
                            class="text-[11px] font-bold text-red-400 hover:text-red-600 transition-colors">
                        Delete
                    </button>
                </form>
            @endcan
        </div>

        {{-- Nested replies --}}
        @if ($comment->replies->isNotEmpty())
            <div class="mt-3 space-y-3 pl-2 border-l-2 border-comsoc-100 ml-1">
                @foreach ($comment->replies as $reply)
                    @include('community.partials.comment', ['comment' => $reply])
                @endforeach
            </div>
        @endif

        {{-- Reply form --}}
        @if (!$comment->isReply())
            <div x-data="{ open: false }"
                 @toggle-reply-{{ $comment->id }}.window="open = !open">
                <div x-show="open" x-cloak
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="mt-2.5 flex items-center gap-2">
                    <img src="{{ Auth::user()->getProfilePictureUrl() }}"
                         class="w-7 h-7 rounded-full object-cover shrink-0 border-2 border-white shadow-sm" alt=""/>
                    <form method="POST"
                          action="{{ route('community.comments.store', $comment->post_id) }}"
                          class="flex-1 relative">
                        @csrf
                        <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                        <input type="text"
                               name="body"
                               required
                               maxlength="2000"
                               placeholder="Reply to {{ $comment->author?->name }}..."
                               class="w-full text-sm bg-white border border-gray-200 rounded-full px-4 py-2 focus:ring-2 focus:ring-comsoc-400 focus:border-comsoc-400 placeholder-gray-400 pr-10 shadow-sm"/>
                        <button type="submit"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-comsoc-600 hover:text-comsoc-800 transition-colors">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>
