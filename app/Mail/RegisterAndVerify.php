<?php

namespace App\Mail;

use App\EmailVerification;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegisterAndVerify extends Mailable {
    use Queueable, SerializesModels;

    /**
     * Email verification token.
     * @var string
     */
    public $token;

    /**
     * RegisterAndVerify constructor.
     *
     * @param EmailVerification $emailVerification Email verification model to use the token from.
     */
    public function __construct(EmailVerification $emailVerification) {
        $this->token = $emailVerification->token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        return $this
            ->markdown('email.registerAndVerify')
            ->with('subject', 'Registration & email verification');
    }
}
