<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Membership extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'program_id',
        'academic_year',
        'status',
        'start_date',
        'end_date',
        'notes',
        'approved_by',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'start_date'  => 'date',
            'end_date'    => 'date',
            'approved_at' => 'datetime',
        ];
    }

    // Valid statuses
    const STATUS_PENDING   = 'pending';
    const STATUS_ACTIVE    = 'active';
    const STATUS_EXPIRED   = 'expired';
    const STATUS_SUSPENDED = 'suspended';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }
}
