<?php

namespace App\Mail\Auth;

use App\Mail\PersonalizedEmail;
use App\Models\SessionLink;
use App\Utils\EmailRecipient;
use Illuminate\Mail\Mailable;

class SessionLinkMail extends PersonalizedEmail {

    /**
     * Email subject.
     */
    const SUBJECT = 'mail.auth.sessionLink.subject';

    /**
     * Email view.
     */
    const VIEW = 'mail.auth.sessionLink';

    /**
     * The worker queue to put this mailable on.
     */
    const QUEUE = 'high';

    /**
     * Token to authenticate the session with.
     * @var string
     */
    public $token;

    /**
     * Constructor.
     *
     * @param EmailRecipient|EmailRecipient[] $recipient Email recipient.
     * @param \App\Models\SessionLink $sessionLink Session link object to use
     *      the token from.
     */
    public function __construct($recipient, SessionLink $sessionLink) {
        parent::__construct($recipient, self::SUBJECT);
        $this->token = $sessionLink->token;
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
            ->addSeconds(config('app.auth_session_link_expire') + 1)
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
        return now()->addSeconds(config('app.auth_session_link_expire'))->subMinute();
    }
}
