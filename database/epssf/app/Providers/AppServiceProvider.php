<?php

namespace Ceb\Providers;

use Event;
use Cartalyst\Sentry\Facades\Laravel\Sentry;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {
	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot() {

		if (Sentry::check()) 
		{
		   App::setLocale(Sentry::getUser()->language);
		}

		$this->app->bind('\Ceb\Repositories\Member\MemberRepositoryInterface',
			'\Ceb\Repositories\Member\MemberRepository');

		/** When a user password is changed then active the user */
		Event::listen('sentinel.user.passwordchange', function($user)
		{
			$user->activated = 1;
			$user->save();
		}, 10);

	}

	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register() {
		//
	}
}
