<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public $event;
    public function __construct($event)
    {
        $this->event = $event;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $permittedChars     = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $customUuid         = substr(str_shuffle($permittedChars), 0, 50);
        $email         = $this->event->details['email'];
        $name          = $this->event->details['name'];
        $subject       = $this->event->details['subject'];
        $content       = $this->event->details['content'];
        $title         = $this->event->details['title'];
        $template      = $this->event->details['template'];

        $data = [
            'email'         => $this->event->details['email'],
            'name'          => $this->event->details['name'],
            'business_name' => $this->event->details['name'],
            'content'       => $this->event->details['content'],
            'title'         => $this->event->details['title'],
            'template_used' => $this->event->details['template'],
            'track_id'      => $customUuid,
            'subject'       => $this->event->details['subject'],
        ];

        Mail::send($template, $data, function ($message) use ($email, $name, $subject) {
            $message->to($email, $name);
            $message->subject($subject);
        });
    }
}
