<?php

namespace App\Jobs;

use App\Models\Economy;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

/**
 * Update the exhausted state for all products.
 */
class UpdateProductExhaustedAll implements ShouldQueue {

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Preferred queue constant.
     */
    const QUEUE = 'low';

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
     * Get the middleware the job should pass through.
     *
     * @return array
     */
    public function middleware() {
        return [
            (new WithoutOverlapping())
                // Release exclusive lock after a day (failure)
                ->expireAfter(24 * 60 * 60)
                ->dontRelease()
        ];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        // For each economy, update the exhausted state
        $economy_ids = Economy::pluck('id');

        DB::transaction(function() use($economy_ids) {
            $economy_ids->each(function($id) {
                UpdateProductExhaustedEconomy::dispatch($id);
            });
        });
    }

    public function retryUntil() {
        // Matches interval in \App\Console\Kernel::schedule
        return now()->addHour();
    }
}
