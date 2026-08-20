<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    const TYPE_COMMUNITY = 'community';
    const TYPE_OFFICIAL  = 'official';

    protected $fillable = [
        'user_id',
        'body',
        'type',
        'event_id',
        'is_pinned',
        'is_hidden',
        'hidden_at',
        'hidden_by',
    ];

    protected function casts(): array
    {
        return [
            'is_pinned'  => 'boolean',
            'is_hidden'  => 'boolean',
            'hidden_at'  => 'datetime',
        ];
    }

    // -----------------------------------------------------------------------
    // Relationships
    // -----------------------------------------------------------------------
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(PostImage::class)->orderBy('order');
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(PostReaction::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)
            ->whereNull('parent_id')
            ->latest();
    }

    public function allComments(): HasMany
    {
        return $this->hasMany(Comment::class)->latest();
    }

    public function reports(): MorphMany
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function hiddenBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hidden_by');
    }

    // -----------------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------------
    public function isOfficial(): bool
    {
        return $this->type === self::TYPE_OFFICIAL;
    }

    public function isLikedBy(User $user): bool
    {
        return $this->reactions()->where('user_id', $user->id)->exists();
    }

    public function likesCount(): int
    {
        return $this->reactions()->count();
    }

    public function commentsCount(): int
    {
        return $this->allComments()->count();
    }
}
