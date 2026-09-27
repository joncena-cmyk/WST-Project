<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PostController extends Controller
{
    /** Community feed */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Post::class);

        $filter = $request->query('filter', 'all');

        $query = Post::with([
                'author.program',
                'images',
                'reactions',
                'allComments',
                'comments.author',
                'comments.replies.author',
            ])
            ->where('is_hidden', false);

        if ($filter === 'official') {
            $query->where('type', Post::TYPE_OFFICIAL);
        } elseif ($filter === 'community') {
            $query->where('type', Post::TYPE_COMMUNITY);
        }

        $posts = $query->latest()->paginate(15)->withQueryString();

        $spotlightEvent = \App\Models\Event::whereIn('status', ['published', 'ongoing'])
            ->where('event_date', '>=', now())
            ->orderBy('event_date')
            ->first();

        $totalMembers = \App\Models\User::count();
        $totalPosts   = Post::where('is_hidden', false)->count();

        return view('community.index', compact('posts', 'filter', 'spotlightEvent', 'totalMembers', 'totalPosts'));
    }

    /** Store a new post */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Post::class);

        $isOfficial = $request->boolean('is_official')
            && $request->user()->isOfficerOrAbove();

        $data = $request->validate([
            'body'    => ['required', 'string', 'max:5000'],
            'images'  => ['nullable', 'array', 'max:4'],
            'images.*'=> ['image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],
        ]);

        if ($isOfficial) {
            $this->authorize('createOfficial', Post::class);
        }

        $post = Post::create([
            'user_id' => Auth::id(),
            'body'    => $data['body'],
            'type'    => $isOfficial ? Post::TYPE_OFFICIAL : Post::TYPE_COMMUNITY,
        ]);

        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $order => $file) {
                $path = $file->store('community/posts', 'public');
                PostImage::create([
                    'post_id' => $post->id,
                    'path'    => $path,
                    'order'   => $order,
                ]);
            }
        }

        return redirect()->route('community.index')
            ->with('success', 'Post shared successfully.');
    }

    /** Show edit form */
    public function edit(Post $post): View
    {
        $this->authorize('update', $post);
        return view('community.edit', compact('post'));
    }

    /** Update a post */
    public function update(Request $request, Post $post): RedirectResponse
    {
        $this->authorize('update', $post);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $post->update(['body' => $data['body']]);

        return redirect()->route('community.index')
            ->with('success', 'Post updated.');
    }

    /** Delete a post */
    public function destroy(Post $post): RedirectResponse
    {
        $this->authorize('delete', $post);

        // Clean up images
        foreach ($post->images as $image) {
            Storage::disk('public')->delete($image->path);
        }

        $post->delete();

        return redirect()->route('community.index')
            ->with('success', 'Post removed.');
    }

    /** Toggle like */
    public function toggleLike(Post $post): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();

        if (!$user->isActive()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $existing = $post->reactions()->where('user_id', $user->id)->first();

        if ($existing) {
            $existing->delete();
            $liked = false;
        } else {
            $post->reactions()->create([
                'user_id' => $user->id,
                'type'    => 'like',
            ]);
            $liked = true;
        }

        return response()->json([
            'liked' => $liked,
            'count' => $post->reactions()->count(),
        ]);
    }
}
