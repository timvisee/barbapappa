<?php

namespace App\Mail\Payment;

use App\Mail\PersonalizedEmail;
use App\Utils\EmailRecipient;
use BarPay\Models\Payment;
use Illuminate\Mail\Mailable;

class Failed extends PersonalizedEmail {

    /**
     * Email subject.
     */
    const SUBJECT = 'mail.payment.failed.subject';

    /**
     * The view to use.
     */
    const VIEW = 'mail.email.payment.failed';

    /**
     * The worker queue to put this mailable on.
     */
    const QUEUE = 'normal';

    /**
     * The ID of the payment this is for.
     * @var int ID of the payment.
     */
    private $payment;

    /**
     * Constructor.
     *
     * @param EmailRecipient[] $recipients A list of email recipients.
     * @param Payment $payment The payment for which to send the message.
     */
    public function __construct($recipients, Payment $payment) {
        // Construct the parent
        parent::__construct($recipients, self::SUBJECT);

        $this->payment = $payment;
    }

    /**
     * Build the message.
     *
     * @return Mailable
     */
    public function build() {
        // Gather details
        $economy = $this->payment->findEconomy();
        $community = $economy->community;
        $transaction = $this->payment->findTransaction();
        $wallet = $this->payment->findWallet();

        // Build the mail
        return parent::build()
            ->with('payment', $this->payment)
            ->with('community', $community)
            ->with('transaction', $transaction)
            ->with('wallet', $wallet)
            ->markdown(self::VIEW);
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
        // Somewhat important, retry somewhat quickly
        return [1, 3, 5, 8, 10];
    }
}
