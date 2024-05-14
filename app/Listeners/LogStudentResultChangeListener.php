<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class LogStudentResultChangeListener
{
    

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {

        $_actor = \App\Models\User::find($event->actor);
        $_student = \App\Models\Students::find($event->student_id);
        $_batch = \App\Models\Batch::find($event->year_id);
        $_course = \App\Models\Subjects::find($event->course_id);
        $_semester = \App\Models\Semester::find($event->semester_id);
        // Save log to database
        $data = ['student_id'=>$event->student_id, 'batch_id'=>$event->year_id, 'semester_id'=>$event->semester_id, 'course_id'=>$event->request->course_id, 'action'=>$event->action, 'actor'=>$event->actor, 'data'=>$event->data];
        $description = `{$_semester->name} {$_batch->name} :: {$event->action}:: On [{$_course->code}] {$_course->name} :: To [{$_student->matric}] {$_student->name} :: By {$_actor->name} :: EXTRA-INFO {$event->data}`;
        
        $log_string = now()->toAtomString().`_________________________________________________________________________________`.
            json_encode($data).`-------------------------`.$description.`_______________________________________________________________________`;

        // log to monitoring log
        Log::channel('student_results')->info($log_string);
    }
}
