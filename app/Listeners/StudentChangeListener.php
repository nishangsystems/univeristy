<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class StudentChangeListener
{
    
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        //

        $data = ['student_id'=>$event->student->id, 'class_id'=>$event->student_class->id??'', 'action'=>$event->action, 'actor'=>$event->actor->id];
        $text = "{$event->action}:: Student: [{$event->student->matric}] {$event->student->name}, Student class: {$event->student_class->name()}, By: {$event->actor->name}";

        $_data = "___________________".json_encode($data)."-------------------".$text.'___________________';
        Log::channel('student_change')->info($_data);
    }
}
