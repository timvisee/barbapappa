<?php

namespace App\Mail\Email;

use App\Mail\PersonalizedEmail;
use App\Utils\EmailRecipient;
use Illuminate\Mail\Mailable;

class Remove extends PersonalizedEmail {

    /**
     * Email subject.
     */
    const SUBJECT = 'mail.email.remove.subject';

    /**
     * Email view.
     */
    const VIEW = 'mail.email.remove';

    /**
     * Reset constructor.
     *
     * @param EmailRecipient $recipient Email recipient.
     */
    public function __construct(EmailRecipient $recipient) {
        // Construct the parent
        parent::__construct($recipient, self::SUBJECT);
    }

    /**
     * Build the message.
     *
     * @return Mailable
     */
    public function build() {
        return parent::build()->markdown(self::VIEW);
    }

    /**
     * Backoff times in seconds.
     *
     * @return array
     */
    public function backoff() {
        // Quickly retry, this email is important, we want it fast
        return [1, 1, 2, 3, 5, 8, 10];
    }
}
