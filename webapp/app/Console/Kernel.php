<?php

namespace App\Console;

use App\Jobs\ExpireHourly;
use App\Jobs\ProcessAllBunqAccountEvents;
use App\Jobs\SendBalanceUpdates;
use App\Jobs\SendReceipts;
use App\Jobs\UpdatePaymentStates;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel {

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule) {
        // Update payment states every 15 minutes
        // Interval also defined in: UpdatePaymentStates::retryUntil
        $schedule->job(new UpdatePaymentStates)
            ->everyFifteenMinutes();

        // Expire hourly job, invokes other expiration jobs
        // Interval also defined in: ExpireHourly::retryUntil
        $schedule->job(new ExpireHourly)
            ->hourly();

        // Send balance updates
        // Interval also defined in: SendBalanceUpdates::retryUntil
        $schedule->job(new SendBalanceUpdates)
            ->hourly();

        // Sent receipts
        // Interval also defined in: SendReceipts::retryUntil
        $schedule->job(new SendReceipts)
            ->everyFifteenMinutes();

        // Process all pending bunq events twice a day
        // Interval also defined in: ProcessAllBunqAccountEvents::retryUntil
        $schedule->job(new ProcessAllBunqAccountEvents)
            ->twiceDaily(0, 12);
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands() {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
