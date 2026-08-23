<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventTask extends Model
{
    use HasFactory, SoftDeletes;

    // Status constants
    const STATUS_PENDING     = 'pending';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED   = 'completed';
    const STATUS_CANCELLED   = 'cancelled';

    // Priority constants
    const PRIORITY_LOW    = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH   = 'high';
    const PRIORITY_URGENT = 'urgent';

    protected $fillable = [
        'event_id',
        'committee_id',
        'title',
        'description',
        'assigned_to',
        'created_by',
        'due_date',
        'priority',
        'status',
        'completed_at',
        'notes',
    ];

    protected $casts = [
        'due_date'     => 'date',
        'completed_at' => 'datetime',
    ];

    /** The event this task belongs to */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /** The committee this task belongs to */
    public function committee(): BelongsTo
    {
        return $this->belongsTo(Committee::class);
    }

    /** The assigned member */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /** The creator of this task */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** Check if task is overdue */
    public function isOverdue(): bool
    {
        if ($this->status === self::STATUS_COMPLETED || $this->status === self::STATUS_CANCELLED) {
            return false;
        }

        if (!$this->due_date) {
            return false;
        }

        return $this->due_date->isPast() && !$this->due_date->isToday();
    }

    /** Check if task is completed */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /** Tailwind badge colors for priority */
    public function getPriorityBadgeClasses(): string
    {
        return match ($this->priority) {
            self::PRIORITY_URGENT => 'bg-red-100 text-red-700 border-red-200',
            self::PRIORITY_HIGH   => 'bg-amber-100 text-amber-700 border-amber-200',
            self::PRIORITY_MEDIUM => 'bg-blue-100 text-blue-700 border-blue-200',
            self::PRIORITY_LOW    => 'bg-gray-100 text-gray-700 border-gray-200',
            default               => 'bg-gray-100 text-gray-700 border-gray-200',
        };
    }

    /** Tailwind badge colors for status */
    public function getStatusBadgeClasses(): string
    {
        return match ($this->status) {
            self::STATUS_COMPLETED   => 'bg-emerald-100 text-emerald-700 border-emerald-200',
            self::STATUS_IN_PROGRESS => 'bg-comsoc-100 text-comsoc-700 border-comsoc-200',
            self::STATUS_PENDING     => 'bg-amber-50 text-amber-600 border-amber-200',
            self::STATUS_CANCELLED   => 'bg-gray-100 text-gray-500 border-gray-200',
            default                  => 'bg-gray-100 text-gray-600 border-gray-200',
        };
    }

    /** Human readable label for status */
    public function getStatusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_COMPLETED   => 'Completed',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_PENDING     => 'Pending',
            self::STATUS_CANCELLED   => 'Cancelled',
            default                  => ucfirst($this->status),
        };
    }
}
