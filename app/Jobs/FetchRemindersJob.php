<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Models\Reminder;
use Illuminate\Bus\Queueable;
use App\Jobs\ProcessRemindersJob;
use App\Models\ReminderHistory;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class FetchRemindersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // process only active reminders
        //put in pending once processed
        $reminders = Reminder::where('next_reminder_at', '<=', strtotime(now()))->where('status', 'ACTIVE')->chunk(100);
        foreach ($reminders as $reminder) {
            $reminder->status = 'PENDING';
            $reminder->save();
            $history = ReminderHistory::where('reminder_id', $reminder->id)->first();
            $diffInMinutes = 0;
            Log::alert($history);
            if ($history && $history->status == 'PENDING') {
                $creationTime = Carbon::createFromTimestamp($history->reminded_at);
                $currentTime = Carbon::now();
                $diffInMinutes = $currentTime->diffInMinutes($creationTime);
            }
            if ($diffInMinutes > 3) {
                dispatch(new UpdatePendingReminderJob($reminder));
            }
            dispatch(new ProcessRemindersJob($reminder));
        }
    }
}
