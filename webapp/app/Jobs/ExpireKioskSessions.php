<?php

namespace App\Jobs;

use App\Models\KioskSession;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;

/**
 * Delete all expired kiosk sessions, based on their `expire_at` time.
 */
class ExpireKioskSessions implements ShouldQueue {

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Aditional period to wait before deleting session after it expired.
     * This is useful so users can still see expired sessions for a short while,
     * which is good from a security point of view.
     */
    const DELETE_DELAY = '1 month';

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
        // Determine what time to delete sessions after
        $delete_after = now()->sub(self::DELETE_DELAY);

        // Delete all kiosk sessions that reached their expiry time
        KioskSession::withoutGlobalScopes()
            ->where(function($query) use($delete_after) {
                $query->whereNull('expire_at')
                    ->orWhere('expire_at', '<=', $delete_after);
            })
            ->delete();
    }

    public function retryUntil() {
        // Matches interval in \App\Console\Kernel::schedule
        return now()->addHour();
    }
}
