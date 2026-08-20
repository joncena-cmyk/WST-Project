<div class="flex items-start gap-2.5" id="comment-{{ $comment->id }}">
    <img src="{{ $comment->author?->getProfilePictureUrl() ?? asset('images/comsoc_logo.png') }}"
         class="w-8 h-8 rounded-full object-cover shrink-0 border border-gray-200 mt-0.5" alt=""/>
    <div class="flex-1 min-w-0">
        <div class="bg-gray-100 rounded-2xl px-3.5 py-2.5 text-sm">
            <p class="font-semibold text-gray-900 text-xs mb-0.5">{{ $comment->author?->name ?? 'Deleted User' }}</p>
            <p class="text-gray-700 leading-snug break-words">{{ $comment->body }}</p>
        </div>
        <div class="flex items-center gap-3 mt-1 px-1">
            <span class="text-[11px] text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>

            {{-- Reply toggle --}}
            @if (!$comment->isReply())
                <button x-data
                        @click="$dispatch('toggle-reply-{{ $comment->id }}')"
                        class="text-[11px] font-semibold text-gray-500 hover:text-comsoc-600 transition-colors">
                    Reply
                </button>
            @endif

            @can('delete', $comment)
                <form method="POST" action="{{ route('community.comments.destroy', $comment) }}">
                    @csrf @method('DELETE')
                    <button type="submit"
                            onclick="return confirm('Delete comment?')"
                            class="text-[11px] font-semibold text-red-400 hover:text-red-600 transition-colors">
                        Delete
                    </button>
                </form>
            @endcan
        </div>

        {{-- Replies --}}
        @if ($comment->replies->isNotEmpty())
            <div class="mt-2 space-y-2 pl-2 border-l-2 border-gray-200">
                @foreach ($comment->replies as $reply)
                    @include('community.partials.comment', ['comment' => $reply])
                @endforeach
            </div>
        @endif

        {{-- Reply form --}}
        @if (!$comment->isReply())
            <div x-data="{ open: false }"
                 @toggle-reply-{{ $comment->id }}.window="open = !open">
                <div x-show="open" x-cloak class="mt-2 flex items-start gap-2.5">
                    <img src="{{ Auth::user()->getProfilePictureUrl() }}"
                         class="w-7 h-7 rounded-full object-cover shrink-0 border border-gray-200" alt=""/>
                    <form method="POST"
                          action="{{ route('community.comments.store', $comment->post_id) }}"
                          class="flex-1">
                        @csrf
                        <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                        <input type="text"
                               name="body"
                               required
                               maxlength="2000"
                               placeholder="Write a reply..."
                               class="w-full text-sm bg-gray-100 border-0 rounded-full px-4 py-2 focus:ring-2 focus:ring-comsoc-500 placeholder-gray-400"/>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>
