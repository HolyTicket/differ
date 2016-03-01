<?php

namespace App\Listeners;

use App\Events\UserWasRegistered;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Mail\Mailer;
use App\Jobs\Job;

class EmailUserRegistration implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create theww event listener.
     *
     * @return void
     */
    public $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Handle the event.
     *
     * @param  SomeEvent  $event
     * @return void
     */
    public function handle(UserWasRegistered $event)
    {
        print('GOGO');
        $this->mailer->send('emails.welcome', ['name' => $event->user->name], function ($message) {
            $message->from('contact@holyticket.com', 'Differ');
            $message->subject('Welcome to differ!');
            $message->to('thomaslith@gmail.com');
        });
    }
}
