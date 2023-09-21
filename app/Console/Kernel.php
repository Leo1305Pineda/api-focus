<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\SeeStatusPendingTask::class,
        \App\Console\Commands\DownloadVehicles::class,
        \App\Console\Commands\EntriesVehicles::class,
        \App\Console\Commands\StockVehicles::class,
        \App\Console\Commands\DeliveryVehicles::class,
        \App\Console\Commands\StatisticsCommand::class,
        \App\Console\Commands\StateChanges::class,
        \App\Console\Commands\AllVehicles::class,
        \App\Console\Commands\ReportsCommand::class,
        \App\Console\Commands\PendingTaskExport::class,
        \App\Console\Commands\PreventiveTask::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('status:pendingtask')->everyMinute();
        // $schedule->command('download:vehicles')->everyMinute();
        $schedule->command('pendingtask:export')->dailyAt('20:00');
        // Informe de stock cada 2 horas
        $schedule->command('stock:vehicles')->dailyAt('07:00');
        $schedule->command('stock:vehicles')->dailyAt('13:00');
        $schedule->command('stock:vehicles')->dailyAt('18:00');
        /*$schedule->command('stock:vehicles')->dailyAt('06:00');
        $schedule->command('stock:vehicles')->dailyAt('08:00');
        $schedule->command('stock:vehicles')->dailyAt('10:00');
        $schedule->command('stock:vehicles')->dailyAt('12:00');
        $schedule->command('stock:vehicles')->dailyAt('14:00');
        $schedule->command('stock:vehicles')->dailyAt('16:00');
        $schedule->command('stock:vehicles')->dailyAt('18:00');*/

        $schedule->command('entry:vehicles')->dailyAt('08:30');
        $schedule->command('delivery:vehicles')->dailyAt('08:30');
        $schedule->command('entry:vehicles')->dailyAt('13:30');
        $schedule->command('delivery:vehicles')->dailyAt('13:30');
        $schedule->command('statistics')->dailyAt('18:00');

        $schedule->command('reports')->everyTwoHours();
    }


}
