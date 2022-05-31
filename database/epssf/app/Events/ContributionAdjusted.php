<?php

namespace Ceb\Events;

use Ceb\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ContributionAdjusted extends Event
{
    use SerializesModels;

    public $contributions;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($contributions)
    {
        $this->contributions = $contributions;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
