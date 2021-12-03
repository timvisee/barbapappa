<?php

namespace App\Jobs;

use App\Models\EmailHistory;
use App\Models\MutationProduct;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

/**
 * Check for each user whether a new receipt should be sent, and in that
 * case, schedule a job to do so.
 */
class SendReceipts implements ShouldQueue {

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Preferred queue constant.
     */
    const QUEUE = 'low';

    /**
     * Delay in seconds after the last purchase after which to sent the receipt.
     */
    const RECEIPT_DELAY = 60 * 60 * 3;

    /**
     * Minimum required time in seconds between receipts.
     */
    const RECEIPT_COOLDOWN = 60 * 60 * 12;

    /**
     * Maximum age of mutations in the receipt.
     *
     * The receipt will never contain items older than this.
     */
    const RECEIPT_MAX_AGE = 60 * 60 * 24 * 2;

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
        // List all users to send a receipt for
        $users = User
            // User must enable
            ::where('mail_receipt', true)

            // User must have purchase before receipt delay time
            ->whereExists(function($query) {
                $query->selectRaw('1')
                    ->from('mutation')
                    ->where('mutation.mutationable_type', MutationProduct::class)
                    ->join('mutation_product', 'mutation_product.id', '=', 'mutation.mutationable_id')
                    ->whereRaw('owner_id = user.id')
                    ->where('mutation.created_at', '>=', now()->subSeconds(Self::RECEIPT_MAX_AGE))
                    ->where('mutation.created_at', '<=', now()->subSeconds(Self::RECEIPT_DELAY))

                    // Mutation must not have been in a receipt mail already
                    ->whereNotExists(function($query) {
                        $query->selectRaw('1')
                            ->from('email_history')
                            ->whereRaw('email_history.user_id = user.id')
                            ->where('email_history.type', EmailHistory::TYPE_RECEIPT)
                            ->whereNotNull('last_at')
                            ->whereRaw('last_at >= mutation.created_at');
                    });
            })

            // User must not have purchase after receipt delay time
            ->whereNotExists(function($query) {
                $query->selectRaw('1')
                    ->from('mutation')
                    ->where('mutation.mutationable_type', MutationProduct::class)
                    ->join('mutation_product', 'mutation_product.id', '=', 'mutation.mutationable_id')
                    ->whereRaw('owner_id = user.id')
                    ->where('mutation.created_at', '>', now()->subSeconds(Self::RECEIPT_DELAY));
            })

            // Must not have sent receipt recently
            ->whereNotExists(function($query) {
                $query->selectRaw('1')
                    ->from('email_history')
                    ->whereRaw('email_history.user_id = user.id')
                    ->where('email_history.type', EmailHistory::TYPE_RECEIPT)
                    ->whereNotNull('last_at')
                    ->where('last_at', '>=', now()->subSecond(Self::RECEIPT_COOLDOWN));
            })
            ->get();

        // Send an update to each listed user
        $users->each(function($user) {
            DB::transaction(function() use($user) {
                // Find the email history entry
                $email_history = EmailHistory::where('user_id', $user->id)
                    ->where('type', EmailHistory::TYPE_RECEIPT)
                    ->first();

                // Create new entry if non existant
                if($email_history == null) {
                    $email_history = new EmailHistory();
                    $email_history->user_id = $user->id;
                    $email_history->type = EmailHistory::TYPE_RECEIPT;
                }

                // Grab period start
                $period_from = ($email_history->last_at ?? now()->subMonth())
                    ->max(now()->subSeconds(Self::RECEIPT_MAX_AGE));

                // Update last time in existing entity
                $email_history->last_at = now();
                $email_history->save();

                // Schedule a job to send the receipt to the user
                SendReceipt::dispatch($user->id, $period_from, now());
            });
        });
    }

    public function retryUntil() {
        // Matches interval in \App\Console\Kernel::schedule
        return now()->addMinutes(15);
    }
}
