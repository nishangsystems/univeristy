<?php

namespace App\Http\Resources;

use App\Helpers\Helpers;
use App\Models\Campus;
use App\Models\ProgramLevel;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentFee extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'link' => route('admin.fee.student.payments.index', [$this->id]),
            'rlink' => route('admin.print_fee.student', [$this->id]),
            'bal' => $this->bal($this->id),
            'campus' => Campus::find($this->campus_id)->name,
            'total' => $this->total(),
            'class' => ProgramLevel::find($this->program_id)->program()->first()->name . ' : Level '. ProgramLevel::find($this->program_id)->level()->first()->level,
            'paid' => $this->paid(),
            //
        ];
    }
}
