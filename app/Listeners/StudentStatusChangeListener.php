<?php

namespace App\Listeners;

use App\Models\StudentStatusChangeTrack;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class StudentStatusChangeListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        //
        $change_instance = new StudentStatusChangeTrack(['student_id'=>$event->student_id, 'state'=>$event->state, 'user_id'=>$event->user_id, 'reason'=>$event->reason]);
        $change_instance->save();

        $log_text = "STUDENT STATUS CHANGED: student name: {$change_instance->student->name}, current state: {$change_instance->state}, changed by : {$change_instance->user->name}, reason: {$event->reason}";
        $log = "____________________________".json_encode($change_instance->toArray())."______________________________".$log_text."__________________________";
        Log::info($log);

    }
}
