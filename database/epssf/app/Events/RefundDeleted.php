<?php

namespace Ceb\Events;

use Ceb\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class RefundDeleted extends Event
{
    use SerializesModels;

    public $deletedRefunds;
    
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($deletedRefunds)
    {
        $this->deletedRefunds;
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
