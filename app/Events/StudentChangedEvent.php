<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StudentChangedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $student, $student_class, $action, $actor;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($student, $student_class, $action, $actor)
    {
        //
        $this->student = $student;
        $this->student_class = $student_class;
        $this->action = $action;
        $this->actor = $actor;
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
