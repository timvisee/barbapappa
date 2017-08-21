<?php

namespace App\Mail\Email;

use App\Mail\PersonalizedEmail;
use App\Utils\EmailRecipient;
use Illuminate\Mail\Mailable;

class Verified extends PersonalizedEmail {

    /**
     * Message view.
     */
    const VIEW = 'mail.email.verifiedAndWelcome';

    /**
     * Message subject.
     */
    const SUBJECT = 'mail.email.verified.subject';

    /**
     * Verified constructor.
     *
     * @param EmailRecipient $recipient Email recipient.
     */
    public function __construct(EmailRecipient $recipient) {
        parent::__construct(
            $recipient,
            self::SUBJECT,
            [
                'app' => config('app.name')
            ]
        );
    }

    /**
     * Build the message.
     *
     * @return Mailable
     */
    public function build() {
        return parent::build()->markdown(self::VIEW);
    }
}
