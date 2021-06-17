<?php

namespace App\Jobs;

use App\Models\BunqAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\Middleware\WithoutOverlapping;
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
            new RateLimited('bunq-api'),
            (new WithoutOverlapping())
                // Release exclusive lock after a day (failure)
                ->expireAfter(24 * 60 * 60)
                ->dontRelease(),
        ];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        // Get all accounts, spawn a event processing job
        BunqAccount::withoutGlobalScope()
            ->checksEnabled()
            ->get()
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
