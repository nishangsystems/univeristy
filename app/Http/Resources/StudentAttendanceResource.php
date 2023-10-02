<?php

namespace App\Http\Resources;

use App\Helpers\Helpers;
use App\Models\StudentAttendance;
use App\Models\Students;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentAttendanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $date = $request->get('date', Carbon::now()->format('Y-m-d'));
        $course_id = $request->get('course_id');

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'username' => $this->username,
            'matric' => $this->matric,
            'present'=> StudentAttendance::where(["student_id"=>$this->id,'course_id'=>$course_id])->whereDate('created_at', $date)->count() > 0
        ];
    }
}
