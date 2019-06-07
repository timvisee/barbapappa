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
     * The recipients that will receive the email.
     * This may be multiple recipients with different email addresses for the same user.
     * @var EmailRecipient[]
     */
    public $recipients = [];

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
        $this->recipients = collect($recipients)->toArray();
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
        $locale = LangManager::getUserLocaleSafe($this->recipients[0]->getUser());

        // Set the locale
        LangManager::setLocale($locale, false, false);

        // Determine the subject
        $subject = trans($this->subjectKey, $this->subjectValues);

        // TODO: use separate mailables here for each recipient!

        // Build the mailable
        $mail = $this->to($this->recipients[0]);
        if(count($this->recipients) > 1)
            $mail = $mail->cc(array_slice($this->recipients, 1));
        return $mail
            ->subject($subject)
            ->onQueue($this->getWorkerQueue())
            ->with('recipient', $this->recipients[0])
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
