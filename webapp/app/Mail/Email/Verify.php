<?php

namespace App\Mail\Email;

use App\Mail\PersonalizedEmail;
use App\Models\EmailVerification;
use App\Utils\EmailRecipient;
use Illuminate\Mail\Mailable;

class Verify extends PersonalizedEmail {

    /**
     * Special email subject for when the user has just registered.
     */
    const SUBJECT_REGISTERED = 'mail.email.verify.subjectRegistered';

    /**
     * Email subject normal verification messages.
     */
    const SUBJECT = 'mail.email.verify.subject';

    /**
     * The view to use when users have just registered.
     */
    const VIEW_REGISTERED = 'mail.email.registerAndVerify';

    /**
     * The view to use normally.
     */
    const VIEW = 'mail.email.verify';

    /**
     * The worker queue to put this mailable on.
     */
    const QUEUE = 'high';

    /**
     * Defines whether the user has just registered.
     *
     * @var bool True if just registered, false if not.
     */
    private $justRegistered;

    /**
     * Email verification token.
     * @var string
     */
    public $token;

    /**
     * Verify constructor.
     *
     * @param EmailRecipient $recipient Email recipient.
     * @param \App\Models\EmailVerification $emailVerification Email verification model to use the token from.
     * @param bool $justRegistered=false True if just registered, false if not.
     */
    public function __construct(EmailRecipient $recipient, EmailVerification $emailVerification, $justRegistered = false) {
        // Construct the parent, dynamically figure out the subject
        parent::__construct(
            $recipient,
            $justRegistered ? self::SUBJECT_REGISTERED : self::SUBJECT
        );

        $this->token = $emailVerification->token;
        $this->justRegistered = $justRegistered;
    }

    /**
     * Build the message.
     *
     * @return Mailable
     */
    public function build() {
        return parent::build()->markdown(
            $this->justRegistered ? self::VIEW_REGISTERED : self::VIEW
        );
    }

    /**
     * Get the worker queue to put this mailable on.
     * @return string
     */
    protected function getWorkerQueue() {
        return self::QUEUE;
    }
}
