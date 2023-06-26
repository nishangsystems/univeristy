<?php

namespace App\Http\Resources;

use App\Helpers\Helpers;
use App\Models\ProgramLevel;
use App\Models\Students;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResourceMain extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $stud = Students::find($this->id);
        $class_name = ProgramLevel::find($this->class_id)->name();
        $campus_name = $stud->campus->name;
        return [
            'id' => $this->id,
            'name' => $this->name,
            'matric' => $this->matric,
            'active'=>$this->active,
            'show_link' => route('admin.student.show',[$this->id]),
            'edit_link' => route('admin.student.edit', [$this->id]),
            'delete_link' => route('admin.student.destroy',[$this->id]),
            'password_reset' => route('admin.student.password.reset',[$this->id]),
            'activate_link' => route('admin.student.change_status', $this->id),
            'class_name' => $class_name,
            'campus_name' => $campus_name
        ];
    }
}
