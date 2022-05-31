<?php

namespace Ceb\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'Ceb\Events\CorrectionStatusChanged' => [
            'Ceb\Listeners\SoftDeleteCancelledLoan',
        ],
        'Ceb\Events\RefundDeleted' => [
        ],
         'Ceb\Events\RefundAdjusted' => [
        ],
        'Ceb\Events\ContributionDeleted' => [
            // Ceb\Listeners\PostingsRemoval::class,
        ]
    ];

    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function boot(DispatcherContract $events)
    {
        parent::boot($events);

        //
    }
}
