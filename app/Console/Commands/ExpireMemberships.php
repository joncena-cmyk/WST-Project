<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\Membership;
use Illuminate\Console\Command;

class ExpireMemberships extends Command
{
    protected $signature = 'memberships:expire';
    protected $description = 'Automatically expire memberships whose end date has passed';

    public function handle(): int
    {
        $expiredMemberships = Membership::where('status', Membership::STATUS_ACTIVE)
            ->whereNotNull('end_date')
            ->where('end_date', '<', now())
            ->get();

        foreach ($expiredMemberships as $membership) {
            $membership->update(['status' => Membership::STATUS_EXPIRED]);
            AuditLog::record('membership_auto_expired', "Membership ID {$membership->id} for user {$membership->user_id} auto-expired.", $membership);
            $this->info("Membership [{$membership->id}] for user [{$membership->user_id}] has expired.");
        }

        $this->info("Expired {$expiredMemberships->count()} memberships.");

        return self::SUCCESS;
    }
}
