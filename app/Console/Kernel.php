<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('wedding:reminder')->everyMinute();

        $schedule->command('template:report',['time' => '20'])
            ->dailyAt('20:00')
            ->timezone('Asia/Phnom_Penh');

        $schedule->command('template:report',['time' => '22'])
            ->dailyAt('22:00')
            ->timezone('Asia/Phnom_Penh');

        $schedule->command('template:report',['time' => '0'])
            ->dailyAt('00:00') // safer than 00:00
            ->timezone('Asia/Phnom_Penh');
    }

}