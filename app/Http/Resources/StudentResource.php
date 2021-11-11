<?php

namespace App\Http\Resources;

use App\Helpers\Helpers;
use App\Models\Students;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
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
            'name' => $this->name,
            'link' => route('admin.fee.student.payments.index', [$this->id]),
            'rlink' => route('admin.print_fee.student', [$this->id]),
            'bal' => $stud->bal([$this->id]),
            'total' => $stud->total($stud->id),
            'class' => $stud->class(Helpers::instance()->getYear())->name,
            'paid' => $stud->paid(),
            'scholarship' => Helpers::instance()->getStudentScholarshipAmount($this->id)
        ];
    }
}
