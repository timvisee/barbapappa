<?php

namespace App\Console;

use App\Jobs\ExpireEmailVerifications;
use App\Jobs\ExpireKioskSessions;
use App\Jobs\ExpireNotifications;
use App\Jobs\ExpirePasswordResets;
use App\Jobs\ExpirePayments;
use App\Jobs\ExpireSessionLinks;
use App\Jobs\ExpireSessions;
use App\Jobs\ProcessAllBunqAccountEvents;
use App\Jobs\SendBalanceUpdates;
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

        // Expire all old notifications
        // Interval also defined in: ExpireNotifications::retryUntil
        $schedule->job(new ExpireNotifications)
            ->hourly();

        // Expire all old sessions
        // Interval also defined in: ExpireSessions::retryUntil
        $schedule->job(new ExpireSessions)
            ->hourly();

        // Expire all old kiosk sessions
        // Interval also defined in: ExpireKioskSessions::retryUntil
        $schedule->job(new ExpireKioskSessions)
            ->hourly();

        // Expire all old session links
        // Interval also defined in: ExpireSessionLinks::retryUntil
        $schedule->job(new ExpireSessionLinks)
            ->hourly();

        // Expire all old password reset tokens
        // Interval also defined in: ExpirePasswordResets::retryUntil
        $schedule->job(new ExpirePasswordResets)
            ->hourly();

        // Expire all old email verification tokens
        // Interval also defined in: ExpireEmailVerifications::retryUntil
        $schedule->job(new ExpireEmailVerifications)
            ->hourly();

        // Send balance updates
        // Interval also defined in: SendBalanceUpdates::retryUntil
        $schedule->job(new SendBalanceUpdates)
            ->hourly();

        // Process all pending bunq events twice a day
        // Interval also defined in: ProcessAllBunqAccountEvents::retryUntil
        $schedule->job(new ProcessAllBunqAccountEvents)
            ->twiceDaily(0, 12);

        // Expire payments
        // Interval also defined in: ExpirePayments::retryUntil
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
