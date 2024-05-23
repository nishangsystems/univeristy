<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BulkMarkAddedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $year_id, $semester_id, $course_id, $action, $actor, $additional_mark, $class_id, $range, $background_id;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($year_id, $semester_id, $course_id, $action, $actor, $additional_mark, $background_id=null, $class_id=null, $range = null)
    {
        //
        $this->year_id = $year_id;
        $this->semester_id = $semester_id;
        $this->course_id = $course_id;
        $this->action = $action;
        $this->actor = $actor;
        $this->additional_mark = $additional_mark;
        $this->class_id = $class_id;
        $this->background_id = $background_id;
        $this->range = is_array($range) ? json_encode($range) : $range;
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
