<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;

/**
 * An hourly job to start other expiration jobs.
 */
class ExpireHourly implements ShouldQueue {

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
            // Release exclusive lock after a day (failure)
            (new WithoutOverlapping())
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
        ExpireEmailVerifications::dispatch();
        ExpireKioskSessions::dispatch();
        ExpireNotifications::dispatch();
        ExpirePasswordResets::dispatch();
        ExpirePayments::dispatch();
        ExpireSessionLinks::dispatch();
        ExpireSessions::dispatch();
    }

    public function retryUntil() {
        // Matches interval in \App\Console\Kernel::schedule for ExpireHourly
        return now()->addHour();
    }
}
