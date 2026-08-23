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
        $currentTime = $now->format('H:i');

        // 1. Transition published events to ongoing
        $publishedEvents = Event::where('status', Event::STATUS_PUBLISHED)->get();
        $toOngoing = $publishedEvents->filter(function (Event $event) use ($today, $currentTime) {
            $eventDate = $event->event_date->format('Y-m-d');
            if ($eventDate < $today) {
                return true;
            }
            if ($eventDate === $today) {
                $start = substr($event->start_time, 0, 5);
                return $start <= $currentTime;
            }
            return false;
        });

        foreach ($toOngoing as $event) {
            $event->update(['status' => Event::STATUS_ONGOING]);
            AuditLog::record('event_status_auto_ongoing', "Event '{$event->title}' auto-transitioned to ongoing.", $event);
            $this->info("Event [{$event->id}] {$event->title} transitioned to ONGOING.");
        }

        // 2. Transition ongoing events to completed
        $ongoingEvents = Event::where('status', Event::STATUS_ONGOING)->get();
        $toCompleted = $ongoingEvents->filter(function (Event $event) use ($today, $currentTime) {
            $eventDate = $event->event_date->format('Y-m-d');
            if ($eventDate < $today) {
                return true;
            }
            if ($eventDate === $today) {
                $end = substr($event->end_time, 0, 5);
                return $end <= $currentTime;
            }
            return false;
        });

        foreach ($toCompleted as $event) {
            $event->update(['status' => Event::STATUS_COMPLETED]);
            AuditLog::record('event_status_auto_completed', "Event '{$event->title}' auto-transitioned to completed.", $event);
            $this->info("Event [{$event->id}] {$event->title} transitioned to COMPLETED.");
        }

        $this->info("Event status transitions completed. Ongoing: {$toOngoing->count()}, Completed: {$toCompleted->count()}.");

        return self::SUCCESS;
    }
}
