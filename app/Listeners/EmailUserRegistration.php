<?php

namespace App\Listeners;

use App\Events\UserWasRegistered;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Mail\Mailer;

/**
 * Class EmailUserRegistration
 * Listener that waits for registered users. This Listener queues using Amazon SQS Message Queuing Service
 * @package App\Listeners
 */
class EmailUserRegistration implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public $mailer;

    /**
     * Construct the Listener and add the mailer contract
     * @param Mailer $mailer
     */
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Handle the event and send a welcome mail
     *
     * @param  SomeEvent  $event
     * @return void
     */
    public function handle(UserWasRegistered $event)
    {

        // Send the message using the mailer contract (Amazon SES)
        $this->mailer->send('emails.welcome', ['name' => $event->user->name], function ($message) use ($event) {
            $message->from('contact@holyticket.com', 'Differ');
            $message->subject(sprintf('Welcome to differ, %s!', $event->user->name));
            $message->to($event->user->email);
        });
    }
}
