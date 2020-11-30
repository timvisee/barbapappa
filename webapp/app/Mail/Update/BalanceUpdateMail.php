<?php

namespace App\Mail\Update;

use App\Mail\PersonalizedEmail;
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
     * A list of wallet/economy data.
     */
    private $data;

    /**
     * Constructor.
     *
     * @param EmailRecipient|EmailRecipient[] $recipient Email recipient.
     * @param array Array with wallet/economy data for the user.
     */
    public function __construct($recipient, $data) {
        parent::__construct($recipient, self::SUBJECT);
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return Mailable
     */
    public function build() {
        return parent::build()
            ->markdown(self::VIEW)
            ->with('data', $this->data);
    }

    /**
     * Get the worker queue to put this mailable on.
     * @return string
     */
    protected function getWorkerQueue() {
        return self::QUEUE;
    }
}
