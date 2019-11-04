<?php

namespace App\Jobs;

use App\Models\BalanceImportAlias;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

/**
 * Try to commit all balance updates for a given user.
 */
class CommitBalanceUpdatesForUser implements ShouldQueue {

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Preferred queue constant.
     */
    const QUEUE = 'low';

    /**
     * The ID of the user.
     */
    private $user_id;

    /**
     * Create a new job instance.
     *
     * @param int $user_id The user ID.
     *
     * @return void
     */
    public function __construct($user_id) {
        // Set queue
        $this->onQueue(Self::QUEUE);
        $this->user_id = $user_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        $self = $this;
        DB::transaction(function() use($self) {
            $user = User::find($self->user_id);
            if($user != null)
                BalanceImportAlias::tryCommitForUser($user);
        });
    }
}
