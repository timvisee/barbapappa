<?php

namespace App\Mail;

use App\Facades\LangManager;
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
     * @param EmailRecipient $recipient Email recipient.
     * @param string $subjectKey Message subject language key.
     * @param array $subjectValues Fields to replace in the subject language value.
     */
    public function __construct(EmailRecipient $recipient, $subjectKey, array $subjectValues = []) {
        $this->recipient = $recipient;
        $this->subjectKey = $subjectKey;
        $this->subjectValues = $subjectValues;
    }

    /**
     * Build the message.
     *
     * @return Mailable
     */
    public function build() {
        // Get the user's locale
        $locale = LangManager::getUserLocaleSafe($this->recipient->getUser());

        // Set the locale
        LangManager::setLocale($locale, false, false);

        // Determine the subject
        $subject = trans($this->subjectKey, $this->subjectValues);

        // Build the mailable
        return $this
            ->to($this->recipient)
            ->subject($subject)
            ->onQueue($this->getWorkerQueue())
            ->with('subject', $subject)
            ->with('locale', $locale);
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
