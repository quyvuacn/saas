<?php

namespace App\Console;

use App\Console\Commands\ClearCoinRequestCommand;
use App\Console\Commands\PushNotifyFirebaseCommand;
use App\Console\Commands\ReportMachineCommand;
use App\Console\Commands\ReportUserDebtCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'App\Console\Commands\ReportMachineCommand',
        'App\Console\Commands\ReportUserDebtCommand',
        'App\Console\Commands\PushNotifyFirebaseCommand',
        'App\Console\Commands\ClearCoinRequestCommand',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
         $schedule->command(ReportMachineCommand::class)->weekly()->withoutOverlapping();
         $schedule->command(ReportUserDebtCommand::class)->monthlyOn(1,'00:00')->withoutOverlapping();
         $schedule->command(PushNotifyFirebaseCommand::class)->everyFifteenMinutes()->withoutOverlapping();
         $schedule->command(ClearCoinRequestCommand::class)->everyFifteenMinutes()->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
