<?php

namespace App\Mail\Password;

use App\Mail\PersonalizedEmail;
use App\Utils\EmailRecipient;
use Illuminate\Mail\Mailable;

class Reset extends PersonalizedEmail {

    /**
     * Email subject.
     */
    const SUBJECT = 'mail.password.reset.subject';

    /**
     * Email view.
     */
    const VIEW = 'mail.password.reset';

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
}
