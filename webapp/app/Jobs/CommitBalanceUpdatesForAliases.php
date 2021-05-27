<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Try to commit all balance updates for a list of aliases.
 */
class CommitBalanceUpdatesForAliases implements ShouldQueue {

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Preferred queue constant.
     */
    const QUEUE = 'low';

    /**
     * List of alias IDs.
     */
    private $alias_ids;

    /**
     * Create a new job instance.
     *
     * @param int[] $alias_ids List of alias IDs.
     *
     * @return void
     */
    public function __construct($alias_ids) {
        // Set queue
        $this->onQueue(Self::QUEUE);
        $this->alias_ids = $alias_ids;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        // Schedule a separate job for each alias
        foreach($this->alias_ids as $alias_id)
            CommitBalanceUpdatesForAlias::dispatch($alias_id);
    }
}
