<?php

namespace App\Mail\Update;

use App\Mail\PersonalizedEmail;
use App\Models\EmailVerification;
use App\Models\SessionLink;
use App\Utils\EmailRecipient;
use Illuminate\Mail\Mailable;

class BalanceUpdateMail extends PersonalizedEmail {

    /**
     * Email subject.
     */
    const SUBJECT = 'mail.update.balance.subject';

    /**
     * Email view.
     */
    const VIEW = 'mail.update.balance';

    /**
     * The worker queue to put this mailable on.
     */
    const QUEUE = 'low';

    /**
     * Constructor.
     *
     * @param EmailRecipient|EmailRecipient[] $recipient Email recipient.
     */
    public function __construct($recipient) {
        parent::__construct($recipient, self::SUBJECT);
    }

    /**
     * Build the message.
     *
     * @return Mailable
     */
    public function build() {
        // TODO: parse wallet data

        return parent::build()
            ->markdown(self::VIEW);
    }

    /**
     * Get the worker queue to put this mailable on.
     * @return string
     */
    protected function getWorkerQueue() {
        return self::QUEUE;
    }
}
