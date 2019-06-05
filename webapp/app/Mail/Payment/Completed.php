<?php

namespace App\Mail\Email\Payment;

use App\Mail\PersonalizedEmail;
use App\Models\EmailVerification;
use App\Utils\EmailRecipient;
use BarPay\Models\Payment;
use Illuminate\Mail\Mailable;

class Completed extends PersonalizedEmail {

    /**
     * Email subject.
     */
    const SUBJECT = 'mail.payment.completed.subject';

    /**
     * The view to use.
     */
    const VIEW = 'mail.email.payment.completed';

    /**
     * The worker queue to put this mailable on.
     */
    const QUEUE = 'normal';

    /**
     * The ID of the payment this is for.
     * @var int ID of the payment.
     */
    private $payment_id;

    /**
     * Constructor.
     *
     * @param EmailRecipient $recipient Email recipient.
     * @param Payment $payment The payment for which to send the message.
     */
    public function __construct(EmailRecipient $recipient, Payment $payment) {
        // Construct the parent
        parent::__construct($recipient, self::SUBJECT);

        $this->payment_id = $payment->id;
    }

    /**
     * Build the message.
     *
     * @return Mailable
     */
    public function build() {
        // Gather details
        // TODO: fix failures when payment is not found
        $payment = Payment::find($this->payment_id);
        $economy = $payment->findEconomy();
        $community = $economy->community;
        $transaction = $payment->findTransaction();
        $wallet = $payment->findWallet();

        // Build the mail
        return parent::build()
            ->with('payment', $payment)
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
}
