<?php

namespace App\Providers;

use App\Jobs\SendEmailJob;
use App\Providers\SendmailEvent;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendmailListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(SendmailEvent $event)
    {
        try {
            dispatch(new SendEmailJob($event));
        } catch (\Throwable $th) {
            Log::alert($th->getMessage());
        }
    }
}
