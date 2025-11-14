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
        // Daily database backup at 2 AM
        $schedule->command('db:backup --compress')
                 ->dailyAt('02:00')
                 ->withoutOverlapping()
                 ->runInBackground();

        // Weekly cleanup of old backups
        $schedule->command('db:backup --compress')
                 ->weekly()
                 ->withoutOverlapping();

        // Send reminder emails every 15 minutes for timely notifications
        $schedule->command('notifications:send-dynamic')
                 ->everyFifteenMinutes()
                 ->withoutOverlapping()
                 ->runInBackground();

        // Update booking statuses every 5 minutes for automatic completion
        $schedule->command('bookings:update-statuses')
                 ->everyFiveMinutes()
                 ->withoutOverlapping()
                 ->runInBackground();
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