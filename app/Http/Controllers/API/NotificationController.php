<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\Students;
use App\Http\Resources\NotificationResource;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function notifications(Request $request){
        $notifications  = new Notification();
        $notifications = $notifications->where('visibility', $request->get('visibility','students')); //use teachers when making call for teachers

        if(isset($request->student_id)){
            $student = Students::find($request->student_id);
           if(isset($student)){
               $notifications = $notifications->where('school_unit_id', $student->program_id);
           }
        } else {
            $student = Auth::guard('student_api')->user();
            if(isset($student)){
                $notifications = $notifications->where('school_unit_id', $student->program_id);
            }
        }

        $notifications->orderBy('updated_at', 'DESC');
        return response([
            'status' => 200,
            'notifications' => NotificationResource::collection($notifications->get())
        ]);
    }
}
