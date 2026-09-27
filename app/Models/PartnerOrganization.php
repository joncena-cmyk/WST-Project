<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PartnerOrganization extends Model
{
    use HasFactory;

    const TYPE_INDUSTRY    = 'industry';
    const TYPE_ACADEMIC    = 'academic';
    const TYPE_STUDENT_ORG = 'student_org';
    const TYPE_GOVERNMENT  = 'government';
    const TYPE_NGO         = 'ngo';

    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';

    protected $fillable = [
        'name',
        'type',
        'contact_person',
        'contact_email',
        'contact_phone',
        'website',
        'description',
        'assigned_officer_id',
        'status',
    ];

    public function assignedOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_officer_id');
    }

    public function partnerships(): HasMany
    {
        return $this->hasMany(Partnership::class, 'partner_id');
    }

    public function getTypeLabel(): string
    {
        return match ($this->type) {
            self::TYPE_INDUSTRY    => 'Industry / Tech Partner',
            self::TYPE_ACADEMIC    => 'Academic Institution',
            self::TYPE_STUDENT_ORG => 'Student Organization',
            self::TYPE_GOVERNMENT  => 'Government Agency',
            self::TYPE_NGO         => 'Non-Profit / NGO',
            default                => ucfirst($this->type),
        };
    }

    public function getTypeBadgeColor(): string
    {
        return match ($this->type) {
            self::TYPE_INDUSTRY    => 'bg-blue-50 text-blue-700 border-blue-200',
            self::TYPE_ACADEMIC    => 'bg-purple-50 text-purple-700 border-purple-200',
            self::TYPE_STUDENT_ORG => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            self::TYPE_GOVERNMENT  => 'bg-amber-50 text-amber-700 border-amber-200',
            self::TYPE_NGO         => 'bg-rose-50 text-rose-700 border-rose-200',
            default                => 'bg-gray-50 text-gray-700 border-gray-200',
        };
    }
}
