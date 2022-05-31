<?php

namespace Ceb\Providers;

use Ceb\Services\EmailNotificationSender;
use Fenos\Notifynder\Facades\Notifynder;
use Illuminate\Support\ServiceProvider;

class NotificationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
         Notifynder::extend('sendWithEmail', function($notifications,$app) {
             return new EmailNotificationSender($notifications,$app['mailer']);
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
