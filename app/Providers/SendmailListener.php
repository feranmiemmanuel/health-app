<?php

namespace App\Providers;

use App\Jobs\SendEmailJob;
use App\Providers\SendmailEvent;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendmailListener
{
    public $email;
    public $name;
    public $subject;
    public $content;
    public $title;
    public $template;
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
        $permittedChars     = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $customUuid         = substr(str_shuffle($permittedChars), 0, 50);
        $this->email         = $event->details['email'];
        $this->name          = $event->details['name'];
        $this->subject       = $event->details['subject'];
        $this->content       = $event->details['content'];
        $this->title         = $event->details['title'];
        $this->template      = $event->details['template'];

        $data = [
            'email'         => $event->details['email'],
            'name'          => $event->details['name'],
            'business_name' => $event->details['name'],
            'content'       => $event->details['content'],
            'title'         => $event->details['title'],
            'template_used' => $event->details['template'],
            'track_id'      => $customUuid,
            'subject'       => $event->details['subject'],
        ];
        try {
            dispatch(new SendEmailJob($event));
        } catch (\Throwable $th) {
            Log::alert($th->getMessage());
        }
    }
}
