<?php

namespace App\Mail;

use App\Utils\EmailRecipient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;

abstract class PersonalizedEmail extends Mailable implements ShouldQueue {

    use Queueable, SerializesModels;

    /**
     * The default queue to put this mailable on.
     */
    const QUEUE_DEFAULT = 'normal';

    /**
     * The recipients that will receive the email.
     * This may be multiple recipients with different email addresses for the same user.
     * @var EmailRecipient[]
     */
    public $recipients;

    /**
     * The language key for the subject of the message.
     * @var string
     */
    public $subjectKey = null;

    /**
     * The language values for the subject of the message.
     * @var string
     */
    public $subjectValues = null;

    /**
     * Constructor.
     *
     * @param EmailRecipient[] $recipients Email recipients, may be a single one.
     * @param string $subjectKey Message subject language key.
     * @param array $subjectValues Fields to replace in the subject language value.
     */
    public function __construct($recipients, $subjectKey, array $subjectValues = []) {
        $this->recipients = collect([$recipients])->flatten();
        $this->subjectKey = $subjectKey;
        $this->subjectValues = $subjectValues;
    }

    /**
     * Build the message.
     *
     * @return Mailable
     */
    public function build() {
        // There must be at least one recipient
        if($this->recipients->isEmpty())
            throw new \Exception('No recipients specified for mailable');

        // All email recipients must have the same target user
        if($this->recipients->pluck('user')->unique()->count() > 1)
            throw new \Exception('Failed to send mailable, sending to recipients being different users, should send separately');

        // Set recipient
        $this->to($this->recipients);

        // Gather recipient user, force set application locale for email
        $user = $this->recipients->first()->getUser();
        $locale = $user->preferredLocale();
        if(isset($locale))
            App::setLocale($locale);

        // Format and set subject
        $subject = trans($this->subjectKey, $this->subjectValues);
        $this->subject($subject);

        // Add unsubscribe link
        $this->withSwiftMessage(function($message) {
            $message->getHeaders()
                ->addTextHeader('List-Unsubscribe', '<' . route('email.preferences') . '>');
        });

        // Finalize building mailable
        return $this
            ->onQueue($this->getWorkerQueue())
            ->locale($user)
            ->with('user', $user)
            ->with('subject', $subject);
    }

    /**
     * Get the worker queue to put this mailable on.
     *
     * @return string|null The name of the queue, or null for default.
     */
    protected function getWorkerQueue() {
        return self::QUEUE_DEFAULT;
    }
}
