<?php

namespace Ceb\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputOption;

class FreshCommand extends Command {
	use ConfirmableTrait;

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'fresh';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Remove auth included with the framework';

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire() {
		if (!$this->confirmToProceed()) {
			return;
		}

		$files = new Filesystem;

		$files->deleteDirectory(app_path('Http/Controllers/Auth'));

		$files->delete(base_path('database/migrations/2014_10_12_000000_create_users_table.php'));
		$files->delete(base_path('database/migrations/2014_10_12_100000_create_password_resets_table.php'));

		$this->info('Auth removed! Enjoy your fresh start.');
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions() {
		return [
			['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production.'],
		];
	}
}
