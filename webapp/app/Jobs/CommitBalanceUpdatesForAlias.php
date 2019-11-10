<?php

namespace App\Jobs;

use App\Models\BalanceImportAlias;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

/**
 * Try to commit all balance updates for a given balance update alias.
 */
class CommitBalanceUpdatesForAlias implements ShouldQueue {

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Preferred queue constant.
     */
    const QUEUE = 'low';

    /**
     * The ID of the alias.
     */
    private $alias_id;

    /**
     * Create a new job instance.
     *
     * @param int $alias_id The alias ID.
     *
     * @return void
     */
    public function __construct($alias_id) {
        // Set queue
        $this->onQueue(Self::QUEUE);
        $this->alias_id = $alias_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        $self = $this;
        DB::transaction(function() use($self) {
            BalanceImportAlias::tryCommitForAliases([$self->alias_id]);
        });
    }
}
