<?php

namespace App\Console;

use App\Console\Commands\CleanupQuotaBalances;
use App\Console\Commands\GenerateDashboardDummyData;
use App\Console\Commands\GenerateHistoricalDummyData;
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
        CleanupQuotaBalances::class,
        GenerateDashboardDummyData::class,
        GenerateHistoricalDummyData::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
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