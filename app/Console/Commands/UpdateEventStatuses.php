<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\Event;
use Illuminate\Console\Command;

class UpdateEventStatuses extends Command
{
    protected $signature = 'events:update-statuses';
    protected $description = 'Automatically transition published events to ongoing and ongoing events to completed based on event schedule';

    public function handle(): int
    {
        $now = now();
        $today = $now->toDateString();

        // 1. Transition past events directly to COMPLETED
        $pastEvents = Event::whereIn('status', [Event::STATUS_PUBLISHED, Event::STATUS_ONGOING])
            ->whereDate('event_date', '<', $today)
            ->get();

        foreach ($pastEvents as $event) {
            $event->update(['status' => Event::STATUS_COMPLETED]);
            AuditLog::record('event_status_auto_completed', "Event '{$event->title}' ended and was auto-completed.", $event);
            $this->info("Event [{$event->id}] {$event->title} transitioned to COMPLETED.");
        }

        // 2. Transition today's events based on time
        $todayEvents = Event::whereIn('status', [Event::STATUS_PUBLISHED, Event::STATUS_ONGOING])
            ->whereDate('event_date', $today)
            ->get();

        $toOngoing = 0;
        $toCompleted = $pastEvents->count();

        foreach ($todayEvents as $event) {
            $start = $event->start_time ?? '00:00:00';
            $end   = $event->end_time;
            $fullCurrent = $now->format('H:i:s');

            if ($end && $end < $fullCurrent) {
                if ($event->status !== Event::STATUS_COMPLETED) {
                    $event->update(['status' => Event::STATUS_COMPLETED]);
                    AuditLog::record('event_status_auto_completed', "Event '{$event->title}' concluded and was auto-completed.", $event);
                    $this->info("Event [{$event->id}] {$event->title} concluded and transitioned to COMPLETED.");
                    $toCompleted++;
                }
            } elseif ($start <= $fullCurrent) {
                if ($event->status !== Event::STATUS_ONGOING) {
                    $event->update(['status' => Event::STATUS_ONGOING]);
                    AuditLog::record('event_status_auto_ongoing', "Event '{$event->title}' is now live and auto-transitioned to ongoing.", $event);
                    $this->info("Event [{$event->id}] {$event->title} is LIVE and transitioned to ONGOING.");
                    $toOngoing++;
                }
            }
        }

        $this->info("Event status transitions completed. Ongoing: {$toOngoing}, Completed: {$toCompleted}.");

        return self::SUCCESS;
    }
}
