<?php

namespace Ceb\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel {
	/**
	 * The application's global HTTP middleware stack.
	 *
	 * @var array
	 */
	protected $middleware = [
		\Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
		\Ceb\Http\Middleware\EncryptCookies::class,
		\Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
		\Illuminate\Session\Middleware\StartSession::class,
		\Illuminate\View\Middleware\ShareErrorsFromSession::class,
		\Ceb\Http\Middleware\VerifyCsrfToken::class,
		\Ceb\Http\Middleware\LocaleMiddleware::class,

	];

	/**
	 * The application's route middleware.
	 *
	 * @var array
	 */
	protected $routeMiddleware = [
		'auth' => \Ceb\Http\Middleware\Authenticate::class,
		'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
		'guest' => \Ceb\Http\Middleware\RedirectIfAuthenticated::class,
		// ..
		'sentry.auth' => 'Sentinel\Middleware\SentryAuth',
		'sentry.admin' => 'Sentinel\Middleware\SentryAdminAccess',
		'sentry.member' => 'Sentinel\Middleware\SentryMember',
	];
	/**
	 * List of command to execute within Ceb system
	 * @var [type]
	 */
	protected $commands = [
		\Ceb\Console\Commands\FreshCommand::class,
	];
}
