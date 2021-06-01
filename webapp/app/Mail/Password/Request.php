<?php

namespace App\Mail\Password;

use App\Mail\PersonalizedEmail;
use App\Managers\PasswordResetManager;
use App\Models\PasswordReset;
use App\Utils\EmailRecipient;
use Illuminate\Mail\Mailable;

class Request extends PersonalizedEmail {

    /**
     * Email subject.
     */
    const SUBJECT = 'mail.password.request.subject';

    /**
     * Email view.
     */
    const VIEW = 'mail.password.request';

    /**
     * The worker queue to put this mailable on.
     */
    const QUEUE = 'high';

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
        // Localize expire time, force set correct locale for this
        $this->configureLocale();
        $expire = now()
            ->addSeconds(PasswordResetManager::EXPIRE_AFTER + 1)
            ->longAbsoluteDiffForHumans();

        return parent::build()
            ->markdown(self::VIEW)
            ->with('expire', $expire);
    }

    /**
     * Get the worker queue to put this mailable on.
     * @return string
     */
    protected function getWorkerQueue() {
        return self::QUEUE;
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

    public function retryUntil() {
        // It does not make sense to send when it has already expired,
        // require at least a minute left
        return now()->addSeconds(PasswordResetManager::EXPIRE_AFTER)->subMinute();
    }
}
