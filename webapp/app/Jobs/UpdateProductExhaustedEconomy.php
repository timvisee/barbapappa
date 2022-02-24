<?php

namespace App\Jobs;

use App\Models\Economy;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;

/**
 * Update the exhausted state for all products in a economy.
 */
class UpdateProductExhaustedEconomy implements ShouldQueue {

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Preferred queue constant.
     */
    const QUEUE = 'low';

    /**
     * Economy ID to update the exhausted states for.
     */
    private int $economy_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $economy_id) {
        // Set queue
        $this->onQueue(Self::QUEUE);
        $this->economy_id = $economy_id;
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
        // Get economy
        $economy = Economy::find($this->economy_id);
        if($economy == null)
            return;

        $products = $economy->products;

        // Find all inventories to base the exhausted state on
        $inventories = $economy
            ->bars
            ->map(function($bar) {
                return $bar->inventory;
            })
            ->filter(function($inventory) {
                return $inventory != null;
            })
            ->unique('id');

        // Update state for each product
        foreach($products as $p) {
            // Determine exhausted state.
            // - Never exhausted if no active inventory.
            // - Otherwise exhausted if exhausted in all active inventories.
            if($inventories->isEmpty())
                $exhausted = false;
            else {
                $exhausted = true;
                foreach($inventories as $inventory)
                    $exhausted = $exhausted && $inventory->getItem($p)?->isExhausted(true) ?? true;
            }

            // Update state in database, without affecting last updated at
            $p->exhausted = $exhausted;
            $p->timestamps = false;
            $p->save();
        }
    }

    public function retryUntil() {
        // Matches interval in \App\Console\Kernel::schedule
        return now()->addHour();
    }
}
