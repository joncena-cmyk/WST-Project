<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommitteeMember extends Model
{
    use HasFactory;

    const ROLE_HEAD      = 'head';
    const ROLE_ASSISTANT = 'assistant';
    const ROLE_MEMBER    = 'member';

    protected $fillable = [
        'committee_id',
        'user_id',
        'role_in_committee',
    ];

    public function committee(): BelongsTo
    {
        return $this->belongsTo(Committee::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
