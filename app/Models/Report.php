<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Report extends Model
{
    use HasFactory;

    const REASON_SPAM          = 'spam';
    const REASON_INAPPROPRIATE = 'inappropriate';
    const REASON_HARASSMENT    = 'harassment';
    const REASON_OTHER         = 'other';

    const REASONS = [
        self::REASON_SPAM          => 'Spam',
        self::REASON_INAPPROPRIATE => 'Inappropriate Content',
        self::REASON_HARASSMENT    => 'Harassment',
        self::REASON_OTHER         => 'Other',
    ];

    const STATUS_PENDING   = 'pending';
    const STATUS_REVIEWED  = 'reviewed';
    const STATUS_RESOLVED  = 'resolved';
    const STATUS_DISMISSED = 'dismissed';

    protected $fillable = [
        'reporter_id',
        'reportable_type',
        'reportable_id',
        'reason',
        'notes',
        'status',
        'reviewed_by',
        'reviewed_at',
        'resolution_notes',
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
        ];
    }

    public function reportable(): MorphTo
    {
        return $this->morphTo();
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
