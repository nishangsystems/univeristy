<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FeeChangedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public $student, $payment, $year, $reason;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($student, $payment, $year, $reason)
    {
        //
        $this->student = $student;
        $this->payment = $payment;
        $this->year = $year;
        $this->reason = $reason;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
