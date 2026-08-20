<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class EventRegistration extends Model
{
    protected $fillable = [
        'event_id', 'user_id', 'status',
        'qr_code', 'cancelled_at', 'cancellation_reason',
    ];

    protected function casts(): array
    {
        return [
            'cancelled_at' => 'datetime',
        ];
    }

    const STATUS_REGISTERED = 'registered';
    const STATUS_CANCELLED  = 'cancelled';
    const STATUS_ATTENDED   = 'attended';

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate a unique QR token for this registration.
     */
    public static function generateQrCode(): string
    {
        do {
            $token = Str::random(32);
        } while (static::where('qr_code', $token)->exists());

        return $token;
    }
}
