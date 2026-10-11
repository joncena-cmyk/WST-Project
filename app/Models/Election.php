<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Election extends Model
{
    use HasFactory;

    const STATUS_DRAFT             = 'draft';
    const STATUS_FILING_OPEN       = 'filing_open';
    const STATUS_VOTING_OPEN       = 'voting_open';
    const STATUS_CLOSED            = 'closed';
    const STATUS_RESULTS_PUBLISHED = 'results_published';

    /**
     * Allowed forward transitions for the election status state machine.
     * Each key is the current status; the value is the set of reachable next statuses.
     */
    const VALID_TRANSITIONS = [
        self::STATUS_DRAFT             => [self::STATUS_FILING_OPEN],
        self::STATUS_FILING_OPEN       => [self::STATUS_VOTING_OPEN, self::STATUS_DRAFT],
        self::STATUS_VOTING_OPEN       => [self::STATUS_CLOSED],
        self::STATUS_CLOSED            => [self::STATUS_RESULTS_PUBLISHED],
        self::STATUS_RESULTS_PUBLISHED => [],
    ];

    public function canTransitionTo(string $newStatus): bool
    {
        return in_array($newStatus, self::VALID_TRANSITIONS[$this->status] ?? [], true);
    }

    /**
     * Returns the list of statuses reachable from the current state,
     * used to build a filtered dropdown in the UI.
     */
    public function allowedNextStatuses(): array
    {
        return self::VALID_TRANSITIONS[$this->status] ?? [];
    }


    protected $fillable = [
        'title',
        'description',
        'academic_year',
        'filing_start_at',
        'filing_end_at',
        'voting_start_at',
        'voting_end_at',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'filing_start_at' => 'datetime',
            'filing_end_at'   => 'datetime',
            'voting_start_at' => 'datetime',
            'voting_end_at'   => 'datetime',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function positions(): HasMany
    {
        return $this->hasMany(ElectionPosition::class)->orderBy('order');
    }

    public function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class);
    }

    public function approvedCandidates(): HasMany
    {
        return $this->hasMany(Candidate::class)->where('status', Candidate::STATUS_APPROVED);
    }

    public function voterParticipations(): HasMany
    {
        return $this->hasMany(VoterParticipation::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function isFilingOpen(): bool
    {
        return $this->status === self::STATUS_FILING_OPEN;
    }

    public function isVotingOpen(): bool
    {
        return $this->status === self::STATUS_VOTING_OPEN;
    }

    public function areResultsPublished(): bool
    {
        return $this->status === self::STATUS_RESULTS_PUBLISHED;
    }

    public function hasUserVoted(User $user): bool
    {
        return $this->voterParticipations()->where('user_id', $user->id)->exists();
    }

    public function hasUserApplied(User $user): bool
    {
        return $this->candidates()->where('user_id', $user->id)->exists();
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT             => 'Draft',
            self::STATUS_FILING_OPEN       => 'Filing Open',
            self::STATUS_VOTING_OPEN       => 'Voting Live',
            self::STATUS_CLOSED            => 'Voting Closed',
            self::STATUS_RESULTS_PUBLISHED => 'Results Official',
            default                        => ucfirst($this->status),
        };
    }

    public function getStatusColor(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT             => 'bg-gray-100 text-gray-700',
            self::STATUS_FILING_OPEN       => 'bg-blue-100 text-blue-800',
            self::STATUS_VOTING_OPEN       => 'bg-emerald-100 text-emerald-800',
            self::STATUS_CLOSED            => 'bg-amber-100 text-amber-800',
            self::STATUS_RESULTS_PUBLISHED => 'bg-purple-100 text-purple-800',
            default                        => 'bg-gray-100 text-gray-700',
        };
    }
}
