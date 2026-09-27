<?php

namespace App\Providers;

use App\Models\Comment;
use App\Models\Event;
use App\Models\Post;
use App\Policies\CommentPolicy;
use App\Policies\EventPolicy;
use App\Policies\PostPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        if (request()->isSecure() || request()->header('X-Forwarded-Proto') === 'https') {
            URL::forceScheme('https');
        }

        Gate::policy(Post::class,    PostPolicy::class);
        Gate::policy(Comment::class, CommentPolicy::class);
        Gate::policy(Event::class,   EventPolicy::class);
    }
}
