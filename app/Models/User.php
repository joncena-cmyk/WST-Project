<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    // ---------------------------------------------------------------------------
    // Roles
    // ---------------------------------------------------------------------------
    const ROLE_STUDENT      = 'student';
    const ROLE_MEMBER       = 'member';
    const ROLE_PROGRAM_REP  = 'program_rep';
    const ROLE_OFFICER      = 'officer';
    const ROLE_PRESIDENT    = 'president';
    const ROLE_ADVISER      = 'adviser';
    const ROLE_ADMIN        = 'admin';

    const ROLES = [
        self::ROLE_STUDENT     => 'Student',
        self::ROLE_MEMBER      => 'Member',
        self::ROLE_PROGRAM_REP => 'Program Representative',
        self::ROLE_OFFICER     => 'Officer',
        self::ROLE_PRESIDENT   => 'President',
        self::ROLE_ADVISER     => 'Adviser',
        self::ROLE_ADMIN       => 'System Administrator',
    ];

    // Account statuses
    const STATUS_ACTIVE    = 'active';
    const STATUS_SUSPENDED = 'suspended';
    const STATUS_INACTIVE  = 'inactive';

    // ---------------------------------------------------------------------------
    // Mass assignment
    // ---------------------------------------------------------------------------
    protected $fillable = [
        'name',
        'email',
        'google_id',
        'avatar',
        'password',
        'student_id',
        'program_id',
        'role',
        'profile_picture',
        'account_status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ---------------------------------------------------------------------------
    // Relationships
    // ---------------------------------------------------------------------------
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function assignedTasks(): HasMany
    {
        return $this->hasMany(EventTask::class, 'assigned_to');
    }

    public function committeeMemberships(): HasMany
    {
        return $this->hasMany(CommitteeMember::class);
    }

    public function headedCommittees(): HasMany
    {
        return $this->hasMany(Committee::class, 'head_user_id');
    }

    public function committees()
    {
        return $this->belongsToMany(Committee::class, 'committee_members')
            ->withPivot('role_in_committee')
            ->withTimestamps();
    }

    public function activeMembership(): ?Membership
    {
        return $this->memberships()
            ->where('status', Membership::STATUS_ACTIVE)
            ->latest()
            ->first();
    }

    // ---------------------------------------------------------------------------
    // Role helpers
    // ---------------------------------------------------------------------------
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles);
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isAdviser(): bool
    {
        return $this->role === self::ROLE_ADVISER;
    }

    public function isOfficerOrAbove(): bool
    {
        return in_array($this->role, [
            self::ROLE_OFFICER,
            self::ROLE_PRESIDENT,
            self::ROLE_ADVISER,
            self::ROLE_ADMIN,
        ]);
    }

    public function isMemberOrAbove(): bool
    {
        return in_array($this->role, [
            self::ROLE_MEMBER,
            self::ROLE_PROGRAM_REP,
            self::ROLE_OFFICER,
            self::ROLE_PRESIDENT,
            self::ROLE_ADVISER,
            self::ROLE_ADMIN,
        ]);
    }

    public function isActive(): bool
    {
        return $this->account_status === self::STATUS_ACTIVE;
    }

    public function getRoleLabel(): string
    {
        return self::ROLES[$this->role] ?? ucfirst($this->role);
    }

    // ---------------------------------------------------------------------------
    // Profile picture helper
    // ---------------------------------------------------------------------------
    public function getProfilePictureUrl(): string
    {
        if ($this->profile_picture) {
            return asset('storage/' . $this->profile_picture);
        }

        if ($this->avatar) {
            return $this->avatar;
        }

        // Generate a consistent avatar using initials via UI Avatars
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name)
            . '&background=166534&color=ffffff&size=128&bold=true';
    }
}
