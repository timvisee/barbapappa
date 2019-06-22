<?php

namespace App\Jobs;

use App\Models\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Delete all notifications that have expired, based on their `expire_at` time.
 *
 * This does not delete any notifications not linked to anything anymore. A
 * different job is used for that.
 */
// TODO: always use low priority for this job
class ExpireNotifications implements ShouldQueue {

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        // Delete all notifications that readed their expiry time
        Notification::where('expired_at', '<=', now())
            ->delete();
    }
}
