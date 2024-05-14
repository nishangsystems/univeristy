<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StudentResultChangedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $student_id, $year_id, $semester_id,
            $course_id, $action, $actor, $data;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($student_id, $year_id, $semester_id, $course_id, $action, $actor, $data=null)
    {
        //
        $this->student_id = $student_id;
        $this->year_id = $year_id;
        $this->semester_id = $semester_id;
        $this->course_id = $course_id;
        $this->action = $action;
        $this->actor = $actor;
        $this->data = $data;
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
