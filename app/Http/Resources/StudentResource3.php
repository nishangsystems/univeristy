<?php

namespace App\Http\Resources;

use App\Helpers\Helpers;
use App\Models\Students;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource3 extends JsonResource
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
        return [
            'id'=> $this->id,
            'name' => $this->name,
            'matric' => $this->matric,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'parent_phone' => $this->parent_phone_number,
            'campus' => \App\Models\Campus::find($this->campus_id)->name,
            'link' => route('admin.fee.student.payments.create', [$this->id]),
            'rlink' => route('admin.print_fee.student', [$this->id]),
        ];
    }
}
