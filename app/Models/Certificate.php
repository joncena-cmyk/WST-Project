<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_id',
        'attendance_id',
        'certificate_number',
        'status',
        'issued_by',
        'issued_at',
        'revoked_at',
        'revocation_reason',
    ];

    protected function casts(): array
    {
        return [
            'issued_at'   => 'datetime',
            'revoked_at'  => 'datetime',
        ];
    }

    const STATUS_ISSUED  = 'issued';
    const STATUS_REVOKED = 'revoked';

    // -----------------------------------------------------------------------
    // Relationships
    // -----------------------------------------------------------------------
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(EventAttendance::class, 'attendance_id');
    }

    public function issuedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    // -----------------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------------
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ISSUED;
    }

    /**
     * Generate a sequential, unique certificate number.
     * Format: COMSOC-YYYY-NNNN
     */
    public static function generateNumber(): string
    {
        $year  = date('Y');
        $count = static::whereYear('issued_at', $year)->count() + 1;
        return sprintf('COMSOC-%s-%04d', $year, $count);
    }
}
