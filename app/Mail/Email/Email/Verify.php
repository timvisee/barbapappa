<?php

namespace App\Mail\Email;

use App\EmailVerification;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Verify extends Mailable {
    use Queueable, SerializesModels;

    /**
     * Defines whether the user has just registered.
     *
     * @var bool True if just registered, false if not.
     */
    private $registered;

    /**
     * Email verification token.
     * @var string
     */
    public $token;

    /**
     * Verify constructor.
     *
     * @param EmailVerification $emailVerification Email verification model to use the token from.
     * @param bool $registered=false True if just registered, false if not.
     */
    public function __construct(EmailVerification $emailVerification, $registered = false) {
        $this->token = $emailVerification->token;
        $this->registered = $registered;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        return $this
            ->markdown($this->registered ? 'email.email.registerAndVerify' : 'email.email.verify')
            ->with('subject', $this->registered ? 'Registration & email verification' : 'Email verification');
    }
}
