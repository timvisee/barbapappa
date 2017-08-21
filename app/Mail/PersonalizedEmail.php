<?php

namespace App\Mail;

use App\Utils\EmailRecipient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

abstract class PersonalizedEmail extends Mailable implements ShouldQueue {

    use Queueable, SerializesModels;

    /**
     * The default queue to put this mailable on.
     */
    const QUEUE_DEFAULT = 'normal';

    /**
     * The recipient that will receive the email.
     * @var EmailRecipient
     */
    public $recipient;

    /**
     * The subject of the message.
     * @var string
     */
    public $subject = null;

    /**
     * Constructor.
     *
     * @param EmailRecipient $recipient Email recipient.
     * @param string $subjectKey Message subject language key.
     * @param array $replace Fields to replace in the subject language value.
     */
    public function __construct(EmailRecipient $recipient, $subjectKey, array $replace = []) {
        $this->recipient = $recipient;
        $this->subject = trans($subjectKey, $replace);
    }

    /**
     * Build the message.
     *
     * @return Mailable
     */
    public function build() {
        // TODO: Get the user's locale here!
//        $locale = 'en';
//
//        // Set the locale for the mail
//        /** @noinspection PhpUndefinedMethodInspection */
//        App::setLocale($locale);

        // Build the mailable
        return $this
            ->to($this->recipient)
            ->subject($this->subject)
            ->onQueue($this->getWorkerQueue());
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
