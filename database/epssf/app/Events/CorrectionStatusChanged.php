<?php

namespace Ceb\Events;

use Ceb\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class CorrectionStatusChanged extends Event
{
    use SerializesModels;

    public $approval;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($approval)
    {
        $this->approval = $approval;
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
