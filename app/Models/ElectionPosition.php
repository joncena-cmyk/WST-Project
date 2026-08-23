<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ElectionPosition extends Model
{
    use HasFactory;

    protected $fillable = [
        'election_id',
        'title',
        'description',
        'order',
        'max_winners',
    ];

    public function election(): BelongsTo
    {
        return $this->belongsTo(Election::class);
    }

    public function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class, 'position_id');
    }

    public function approvedCandidates(): HasMany
    {
        return $this->hasMany(Candidate::class, 'position_id')->where('status', Candidate::STATUS_APPROVED);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class, 'position_id');
    }
}
