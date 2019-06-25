<?php

namespace App\Jobs;

use App\Models\BunqAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use bunq\Model\Generated\Endpoint\Payment;
use bunq\Model\Generated\Object\Amount;
use bunq\Model\Generated\Object\Pointer;

class SendBunqPayment implements ShouldQueue {

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $queue = 'low';

    /**
     * The ID of the bunq account, which the money is sent from.
     *
     * @var int
     */
    private $account_id;

    /**
     * The target to send the money to.
     *
     * @var Pointer
     */
    private $to;

    /**
     * The amount of money to send.
     *
     * @var Amount
     */
    private $amount;

    /**
     * The payment description.
     *
     * @var string
     */
    private $description;

    /**
     * Create a new job instance.
     *
     * @param BunqAccount $account The bunq account to send the money from.
     * @param Amount $amount The amount.
     * @param Pointer $to The target to send the money to.
     * @param string $description The description.
     *
     * @return void
     */
    public function __construct(BunqAccount $account, Pointer $to, Amount $amount, string $description) {
        $this->account_id = $account->id;
        $this->to = $to;
        $this->amount = $amount;
        $this->description = $description;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        // Obtain the account ID, load the bunq API context
        // TODO: also find deleted accounts
        $account = BunqAccount::findOrFail($this->account_id);
        $account->loadBunqContext();

        // Send the payment
        Payment::create(
            $this->amount,
            $this->to,
            $this->description,
            $account->monetary_account_id,
            null,
            null,
            null,
            []
        );
    }
}
