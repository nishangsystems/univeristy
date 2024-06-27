<?php

namespace App\Listeners;

use App\Events\ProgramChangeEvent;
use App\Models\ProgramChangeTrack;
use App\Models\ProgramLevel;
use App\Models\Students;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class ProgramChangeListener
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
        $former_class = ProgramLevel::find($event->former_class);
        $current_class = ProgramLevel::find($event->current_class);
        $student = Students::find($event->student_id);
        $user = User::find($event->user_id);

        $time = now();

        $data = ['former_class'=>$event->former_class, 'current_class'=>$event->current_class, 'student_id'=>$event->student_id, 'user_id'=>$event->user_id, 'created_at'=>$time];
        $log_message = "Student program changed from {$former_class->name()} to {$current_class->name()}. Operation initiated by {$user->name}. Student Account : {$student->matric} ({$student->name})"; 

        $log = "______________________".json_encode($data)."_____________________".$log_message."_________________________";

        // update tracking database
        ProgramChangeTrack::create($data);
        // log to monitoring log
        Log::channel('program_change')->info($log);
    }
}
