<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, Post $post): RedirectResponse
    {
        $this->authorize('create', Comment::class);

        $data = $request->validate([
            'body'      => ['required', 'string', 'max:2000'],
            'parent_id' => ['nullable', 'exists:comments,id'],
        ]);

        // Verify parent belongs to this post if provided
        if (!empty($data['parent_id'])) {
            $parent = Comment::findOrFail($data['parent_id']);
            if ($parent->post_id !== $post->id) {
                abort(422, 'Invalid parent comment.');
            }
        }

        $comment = Comment::create([
            'post_id'   => $post->id,
            'user_id'   => Auth::id(),
            'parent_id' => $data['parent_id'] ?? null,
            'body'      => $data['body'],
        ]);

        // Notify post author if not the commenter
        if ($post->author && $post->user_id !== Auth::id()) {
            $post->author->notify(new \App\Notifications\GeneralComsocNotification(
                title: Auth::user()->name . " commented on your post",
                message: '"' . \Illuminate\Support\Str::limit($comment->body, 80) . '"',
                actionUrl: route('community.index') . '#post-' . $post->id,
                type: 'info'
            ));
        }

        return back()->with('success', 'Comment posted.');
    }

    public function update(Request $request, Comment $comment): RedirectResponse
    {
        $this->authorize('update', $comment);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $comment->update(['body' => $data['body']]);

        return back()->with('success', 'Comment updated.');
    }

    public function destroy(Comment $comment): RedirectResponse
    {
        $this->authorize('delete', $comment);
        $comment->delete();
        return back()->with('success', 'Comment removed.');
    }
}
