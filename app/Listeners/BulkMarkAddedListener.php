<?php

namespace App\Listeners;

use App\Models\Background;
use App\Models\Batch;
use App\Models\BulkMarkChange;
use App\Models\ProgramLevel;
use App\Models\Semester;
use App\Models\Subjects;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class BulkMarkAddedListener
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
        $year = Batch::find($event->year_id);
        $semester = Semester::find($event->semester_id);
        $course = $event->course_id == null ? null : Subjects::find($event->course_id);
        $class = $event->class_id == null ? null : ProgramLevel::find($event->class_id);
        $background = $event->background_id == null ? null : Background::find($event->background_id);
        $data = ['year_id'=>$event->year_id, 'semester_id'=>$event->semester_id, 'course_id'=>$event->course_id, 'class_id'=>$event->class_id, 'background_id'=>$event->background_id, 'action'=>$event->action, 'additional_mark'=>$event->additional_mark, 'actor'=>$event->actor->id, 'interval'=>$event->range];
        $description = $event->course_id == null ?
            ($background == null ? '' : 'BACKGROUND: '.$background->background_name).":: {$event->action}: {$event->additional_mark} ADDED FOR RANGE: {$event->range}, ".($background != null ? null : $class->name()??null)." :: {$semester->name} {$year->name}, BY: {$event->actor->name}" :
            ($background == null ? '' : 'BACKGROUND: '.$background->background_name).":: {$event->action}: {$event->additional_mark} ADDED FOR [{($course->code)}] {$course->name}, RANGE: {$event->range}, ".($background != null ? null : $class->name()??null)." :: {$semester->name} {$year->name}, BY: {$event->actor->name}";

        $log = "{$event->action}_________________".json_encode($data).'----------------'.$description.'__________________';

        // save track record to DB
        BulkMarkChange::create($data);
        // log track record
        Log::channel('bulk_mark_change')->info($log);
    }
}
