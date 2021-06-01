<?php

namespace App\Mail\Password;

use App\Mail\PersonalizedEmail;
use App\Utils\EmailRecipient;
use Illuminate\Mail\Mailable;

class Disabled extends PersonalizedEmail {

    /**
     * Email subject.
     */
    const SUBJECT = 'mail.password.disabled.subject';

    /**
     * Email view.
     */
    const VIEW = 'mail.password.disabled';

    /**
     * Password reset token if there is any.
     * @var string|null
     */
    public $token;

    /**
     * Reset constructor.
     *
     * @param EmailRecipient $recipient Email recipient.
     * @param \App\Models\PasswordReset|null $passwordReset Password reset model to use the token from if there is any.
     */
    public function __construct(EmailRecipient $recipient, $passwordReset = null) {
        // Construct the parent
        parent::__construct($recipient, self::SUBJECT);

        $this->token = $passwordReset->token;
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
