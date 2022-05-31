<?php

namespace Ceb\Events;

use Ceb\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class RefundAdjusted extends Event
{
    use SerializesModels;

    public $adjustedRefund;
    
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($adjustedRefund)
    {
        $this->adjustedRefund = $adjustedRefund;
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
