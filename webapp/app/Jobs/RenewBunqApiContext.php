<?php

namespace App\Jobs;

use App\Models\BunqAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RenewBunqApiContext implements ShouldQueue {

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Preferred queue constant.
     */
    const QUEUE = 'high';

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
     * Create a new job instance.
     *
     * @param BunqAccount $account The bunq account to renew the API context
     *      session for.
     *
     * @return void
     */
    public function __construct(BunqAccount $account) {
        // Set queue
        $this->onQueue(Self::QUEUE);

        $this->account_id = $account->id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        // Obtain the account ID, get the API context
        // TODO: also find deleted accounts
        $account = BunqAccount::findOrFail($this->account_id);
        $apiContext = $account->api_context;

        // Renew the context, update it in the database
        $apiContext->resetSession();
        $account->api_context = $apiContext;
        $account->renewed_at = now();
        $account->save();
    }
}
