<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Committee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'event_id',
        'name',
        'description',
        'head_user_id',
        'created_by',
    ];

    /** The event this committee belongs to */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /** The appointed head of this committee */
    public function head(): BelongsTo
    {
        return $this->belongsTo(User::class, 'head_user_id');
    }

    /** The officer/admin who created this committee */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** Committee member pivot records */
    public function members(): HasMany
    {
        return $this->hasMany(CommitteeMember::class);
    }

    /** Users assigned to this committee */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'committee_members')
            ->withPivot('role_in_committee')
            ->withTimestamps();
    }

    /** Tasks associated with this committee */
    public function tasks(): HasMany
    {
        return $this->hasMany(EventTask::class);
    }

    /** Percentage of completed tasks (0 to 100) */
    public function taskCompletionPercentage(): int
    {
        $total = $this->tasks()->count();
        if ($total === 0) {
            return 0;
        }

        $completed = $this->tasks()->where('status', EventTask::STATUS_COMPLETED)->count();
        return (int) round(($completed / $total) * 100);
    }

    /** Check if a given user is head of this committee */
    public function isHead(?User $user): bool
    {
        if (!$user) {
            return false;
        }
        return $this->head_user_id === $user->id;
    }

    /** Check if a given user is a member of this committee */
    public function hasMember(?User $user): bool
    {
        if (!$user) {
            return false;
        }
        return $this->head_user_id === $user->id || $this->members()->where('user_id', $user->id)->exists();
    }
}
