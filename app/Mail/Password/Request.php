<?php

namespace App\Mail\Password;

use App\Mail\PersonalizedEmail;
use App\Models\PasswordReset;
use App\Utils\EmailRecipient;
use Illuminate\Mail\Mailable;

class Request extends PersonalizedEmail {

    /**
     * Email subject.
     */
    const SUBJECT = 'Password reset request';

    /**
     * Email view.
     */
    const VIEW = 'mail.password.request';

    /**
     * Password reset token.
     * @var string
     */
    public $token;

    /**
     * Reset constructor.
     *
     * @param EmailRecipient $recipient Email recipient.
     * @param \App\Models\PasswordReset $passwordReset Password reset model to use the token from.
     */
    public function __construct(EmailRecipient $recipient, PasswordReset $passwordReset) {
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
}
