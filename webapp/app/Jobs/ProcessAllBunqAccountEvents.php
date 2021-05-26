<?php

namespace App\Jobs;

use App\Models\BunqAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Process all events for each linked bunq account, that have not yet been
 * processed.
 */
class ProcessAllBunqAccountEvents implements ShouldQueue {

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
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct() {
        // Set queue
        $this->onQueue(Self::QUEUE);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        // Get all accounts, spawn a event processing job
        // TODO: include hidden
        BunqAccount::all()
            ->each(function($account, $i) {
                ProcessBunqAccountEvents::dispatch($account)
                    ->delay(now()->addMinutes($i));
            });
    }

    public function retryUntil() {
        // Matches interval in \App\Console\Kernel::schedule
        return now()->addHours(12);
    }
}
