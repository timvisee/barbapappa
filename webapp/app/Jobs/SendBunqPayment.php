<?php

namespace App\Jobs;

use App\Models\BunqAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\SerializesModels;
use bunq\Model\Generated\Endpoint\Payment;
use bunq\Model\Generated\Object\Amount;
use bunq\Model\Generated\Object\Pointer;

class SendBunqPayment implements ShouldQueue {

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Preferred queue constant.
     */
    const QUEUE = 'low';

    /**
     * The number of seconds to wait before retrying the job.
     * The bunq API has a 30-second cooldown when throttling.
     *
     * @var int
     */
    public $backoff = 32;

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
        // Set queue
        $this->onQueue(Self::QUEUE);

        $this->account_id = $account->id;
        $this->to = $to;
        $this->amount = $amount;
        $this->description = $description;
    }

    /**
     * Get the middleware the job should pass through.
     *
     * @return array
     */
    public function middleware() {
        return [new RateLimited('bunq-api')];
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
