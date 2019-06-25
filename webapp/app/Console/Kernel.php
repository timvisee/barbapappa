<?php

namespace App\Console;

use App\Jobs\ExpireNotifications;
use App\Jobs\ProcessAllBunqAccountEvents;
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
        // Process all pending bunq events twice a day
        $schedule->job(new ProcessAllBunqAccountEvents)
            ->twiceDaily(0, 12);

        // Expire all old notifications
        $schedule->job(new ExpireNotifications)
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
