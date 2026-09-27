<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CollaborationActivity extends Model
{
    use HasFactory;

    const TYPE_WORKSHOP          = 'workshop';
    const TYPE_HACKATHON         = 'hackathon';
    const TYPE_SEMINAR           = 'seminar';
    const TYPE_COMPETITION       = 'competition';
    const TYPE_PROJECT           = 'project';
    const TYPE_TRAINING          = 'training';
    const TYPE_COMMUNITY         = 'community';
    const TYPE_TECHNICAL_SUPPORT = 'technical_support';
    const TYPE_SPEAKER_PANEL     = 'speaker_panel';

    const STATUS_PLANNED   = 'planned';
    const STATUS_ONGOING   = 'ongoing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'partnership_id',
        'event_id',
        'title',
        'activity_type',
        'activity_date',
        'venue',
        'description',
        'status',
        'lead_officer_id',
    ];

    protected function casts(): array
    {
        return [
            'activity_date' => 'date',
        ];
    }

    public function partnership(): BelongsTo
    {
        return $this->belongsTo(Partnership::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function leadOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'lead_officer_id');
    }

    public function getTypeLabel(): string
    {
        return match ($this->activity_type) {
            self::TYPE_WORKSHOP          => 'Technical Workshop',
            self::TYPE_HACKATHON         => 'Hackathon & Challenge',
            self::TYPE_SEMINAR           => 'Tech Seminar & Talk',
            self::TYPE_COMPETITION       => 'Coding Competition',
            self::TYPE_PROJECT           => 'Software Project',
            self::TYPE_TRAINING          => 'Industry Training',
            self::TYPE_COMMUNITY         => 'Community Outreach',
            self::TYPE_TECHNICAL_SUPPORT => 'Technical Support & AV Ops',
            self::TYPE_SPEAKER_PANEL     => 'Resource Speakers & Judges',
            default                      => ucfirst(str_replace('_', ' ', $this->activity_type)),
        };
    }
}
