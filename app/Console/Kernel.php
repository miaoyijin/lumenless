<?php

namespace App\Console;

use Exception;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;
use App\Console\Commands\web\ConsumeRecordCommand;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        ConsumeRecordCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule schedule
     * @return void
     * @throws Exception
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('consume:record')
            ->name('consume:record')
            ->everyMinute()
            ->runInBackground();
    }
}
