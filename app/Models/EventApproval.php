<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventApproval extends Model
{
    protected $fillable = ['event_id', 'reviewed_by', 'action', 'remarks'];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
