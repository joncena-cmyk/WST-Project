<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'action',
        'description',
        'ip_address',
        'user_agent',
        'auditable_type',
        'auditable_id',
        'properties',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'properties' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Record an audit event into the database.
     */
    public static function record(string $action, string $description, ?Model $auditable = null, array $properties = [], ?User $user = null): self
    {
        $actor = $user ?? Auth::user();

        return self::create([
            'user_id'        => $actor?->id,
            'action'         => $action,
            'description'    => $description,
            'ip_address'     => Request::ip(),
            'user_agent'     => Request::userAgent(),
            'auditable_type' => $auditable ? get_class($auditable) : null,
            'auditable_id'   => $auditable?->getKey(),
            'properties'     => empty($properties) ? null : $properties,
            'created_at'     => now(),
        ]);
    }
}
