<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    // Status constants
    const STATUS_DRAFT       = 'draft';
    const STATUS_SUBMITTED   = 'submitted';
    const STATUS_UNDER_REVIEW= 'under_review';
    const STATUS_RETURNED    = 'returned';
    const STATUS_APPROVED    = 'approved';
    const STATUS_PUBLISHED   = 'published';
    const STATUS_ONGOING     = 'ongoing';
    const STATUS_COMPLETED   = 'completed';
    const STATUS_ARCHIVED    = 'archived';
    const STATUS_CANCELLED   = 'cancelled';

    const STATUSES = [
        self::STATUS_DRAFT        => 'Draft',
        self::STATUS_SUBMITTED    => 'Submitted',
        self::STATUS_UNDER_REVIEW => 'Under Review',
        self::STATUS_RETURNED     => 'Returned for Revision',
        self::STATUS_APPROVED     => 'Approved',
        self::STATUS_PUBLISHED    => 'Published',
        self::STATUS_ONGOING      => 'Ongoing',
        self::STATUS_COMPLETED    => 'Completed',
        self::STATUS_ARCHIVED     => 'Archived',
        self::STATUS_CANCELLED    => 'Cancelled',
    ];

    // Status badge colours for views
    const STATUS_COLORS = [
        self::STATUS_DRAFT        => 'bg-gray-100 text-gray-600',
        self::STATUS_SUBMITTED    => 'bg-blue-100 text-blue-700',
        self::STATUS_UNDER_REVIEW => 'bg-yellow-100 text-yellow-700',
        self::STATUS_RETURNED     => 'bg-orange-100 text-orange-700',
        self::STATUS_APPROVED     => 'bg-comsoc-100 text-comsoc-700',
        self::STATUS_PUBLISHED    => 'bg-comsoc-600 text-white',
        self::STATUS_ONGOING      => 'bg-blue-600 text-white',
        self::STATUS_COMPLETED    => 'bg-emerald-700 text-white',
        self::STATUS_ARCHIVED     => 'bg-slate-700 text-white',
        self::STATUS_CANCELLED    => 'bg-red-100 text-red-700',
    ];

    protected $fillable = [
        'created_by', 'partnership_id', 'partner_id', 'is_collaboration',
        'title', 'description', 'event_date', 'start_time',
        'end_time', 'venue', 'registration_deadline', 'participant_limit',
        'image', 'status', 'published_at', 'cancelled_at', 'cancellation_reason',
    ];

    protected function casts(): array
    {
        return [
            'event_date'            => 'date',
            'registration_deadline' => 'datetime',
            'published_at'          => 'datetime',
            'cancelled_at'          => 'datetime',
            'is_collaboration'      => 'boolean',
        ];
    }

    // -----------------------------------------------------------------------
    // Relationships
    // -----------------------------------------------------------------------
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function partnership(): BelongsTo
    {
        return $this->belongsTo(Partnership::class, 'partnership_id');
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(PartnerOrganization::class, 'partner_id');
    }

    public function requirements(): HasMany
    {
        return $this->hasMany(EventRequirement::class);
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(EventApproval::class)->latest();
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function activeRegistrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class)->where('status', 'registered');
    }

    public function attendance(): HasMany
    {
        return $this->hasMany(EventAttendance::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(EventAttendance::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function committees(): HasMany
    {
        return $this->hasMany(Committee::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(EventTask::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    // -----------------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------------
    public function getStatusLabel(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    public function getStatusColor(): string
    {
        return self::STATUS_COLORS[$this->status] ?? 'bg-gray-100 text-gray-600';
    }

    public function isPublic(): bool
    {
        return in_array($this->status, [self::STATUS_PUBLISHED, self::STATUS_ONGOING]);
    }

    public function isRegistrationOpen(): bool
    {
        if (!$this->isPublic()) return false;
        if ($this->registration_deadline && now()->isAfter($this->registration_deadline->endOfDay())) return false;
        if ($this->participant_limit && $this->activeRegistrations()->count() >= $this->participant_limit) return false;
        return true;
    }

    public function registeredCount(): int
    {
        return $this->activeRegistrations()->count();
    }

    public function isFull(): bool
    {
        return $this->participant_limit
            && $this->registeredCount() >= $this->participant_limit;
    }

    public function isRegisteredBy(User $user): bool
    {
        return $this->registrations()
            ->where('user_id', $user->id)
            ->where('status', 'registered')
            ->exists();
    }

    public function isCompleted(): bool
    {
        return in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_ARCHIVED]);
    }

    public function isArchived(): bool
    {
        return $this->status === self::STATUS_ARCHIVED;
    }

    public function attendedCount(): int
    {
        return $this->attendance()->count();
    }

    public function certificatesCount(): int
    {
        return $this->certificates()->count();
    }

    public function turnoutPercentage(): int
    {
        $reg = $this->registeredCount();
        if ($reg === 0) {
            return $this->attendedCount() > 0 ? 100 : 0;
        }
        return (int) round(($this->attendedCount() / $reg) * 100);
    }

    public function scopeUpcoming($query)
    {
        return $query->whereIn('status', [self::STATUS_PUBLISHED, self::STATUS_ONGOING])
                     ->where('event_date', '>=', now()->toDateString());
    }

    public function scopeArchived($query)
    {
        return $query->where(function ($q) {
            $q->whereIn('status', [self::STATUS_COMPLETED, self::STATUS_ARCHIVED])
              ->orWhere(function ($past) {
                  $past->whereIn('status', [self::STATUS_PUBLISHED, self::STATUS_ONGOING])
                       ->where('event_date', '<', now()->toDateString());
              });
        });
    }

    public function getImageUrl(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }
}
