<?php

namespace App\Console;

use App\Jobs\ExpireNotifications;
use App\Jobs\ProcessAllBunqAccountEvents;
use App\Jobs\SendBalanceUpdates;
use App\Jobs\UpdatePaymentStates;
use App\Jobs\ExpirePayments;
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
        $schedule->job(new UpdatePaymentStates)
            ->everyFifteenMinutes();

        // Expire all old notifications
        $schedule->job(new ExpireNotifications)
            ->hourly();

        // Send balance updates
        $schedule->job(new SendBalanceUpdates)
            ->hourly();

        // Process all pending bunq events twice a day
        $schedule->job(new ProcessAllBunqAccountEvents)
            ->twiceDaily(0, 12);

        // Expire payments
        $schedule->job(new ExpirePayments)
            ->hourly();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands() {
        require base_path('routes/console.php');

        // Laravel 5.5
        $this->load(__DIR__.'/Commands');
    }
}
