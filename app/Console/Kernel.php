<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();

        //Run the task every minute
        $schedule->command('app:verify-payment-status')
                ->everyFiveMinutes();
        $schedule->command('app:common-files-remove')
                ->dailyAt('23:30'); //dailyAt('13:00');
        $schedule->command('app:event-book-ticket')
                ->dailyAt('23:30'); //dailyAt('13:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
