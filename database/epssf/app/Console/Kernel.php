<?php

namespace Ceb\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel {
	/**
	 * The Artisan commands provided by your application.
	 *
	 * @var array
	 */
	protected $commands = [
		\Ceb\Console\Commands\Inspire::class,
		\Ceb\Console\Commands\FreshCommand::class,
		\Ceb\Console\Commands\GuarantorReleaser::class,
		\Ceb\Console\Commands\MarkInactiveMember::class,
	];

	/**
	 * Define the application's command schedule.
	 *
	 * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
	 * @return void
	 */
	protected function schedule(Schedule $schedule) {
		 
		 $filePath = storage_path('app/jobscheduler-output.txt');
        /////////////////////
        // BACKUP COMMANDS //
        /////////////////////
        $schedule->command('backup:clean')
                 ->daily()->at('20:00')
                 ->timezone(env('TIME_ZONE' , 'Africa/Kigali'))
                 ->sendOutputTo($filePath)->emailOutputTo(env('SUPPORT_EMAIL','ictceb@gmail.com'));
       
        $schedule->command('backup:run')
                 ->daily()->at('20:30')
                 ->timezone(env('TIME_ZONE' , 'Africa/Kigali'))
                 ->sendOutputTo($filePath)
                 ->emailOutputTo(env('SUPPORT_EMAIL','ictceb@gmail.com'));

        // Setting inactive members 
        $schedule->command('ceb:mark-member:inactive')
                 ->daily()->at('01:30')
                 ->timezone(env('TIME_ZONE' , 'Africa/Kigali'))
                 ->sendOutputTo($filePath)
                 ->emailOutputTo(env('SUPPORT_EMAIL','olivierbite@gmail.com'));

        // Release cautionneur / guarantors every day at 10PM 
	    $schedule->command('ceb:release:guarantor')
                 ->daily()->at('22:00')
                 ->timezone(env('TIME_ZONE' , 'Africa/Kigali'))
                 ->sendOutputTo($filePath)
                 ->emailOutputTo(env('SUPPORT_EMAIL','olivierbite@gmail.com'));
	}
}
