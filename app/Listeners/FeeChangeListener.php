<?php

namespace App\Listeners;

use App\Models\FeeTrack;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class FeeChangeListener
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
        $class = $event->student->_class($event->year->id);
        $data = ['payment_id'=>$event->payment->id, 'amount'=>$event->payment->amount, 'class_id'=>$class->id??null, 'batch_id'=>$event->year->id, 'matric'=>$event->student->name, 'student'=>$event->student->name, 'student_id'=>$event->student->id, 'action'=>$event->action, 'actor'=>$event->actor->id, 'reason'=>$event->reason];
        $description = "{$event->action}:: Fee Amount: {$event->payment->amount} CFA deleted for {$class->name()} {$event->year->name}, Student: [{$event->student->matric}] {$event->student->name}, Reason: {$event->reason}, Done by: {$event->actor->name}";
        $_data = $event->action.'____________________'.json_encode($data).'----------------------------'.$description.'______________________';

        FeeTrack::create($data);
        Log::channel('fee_change')->info($_data);
    }
}
