<?php

namespace App\Http\Resources;

use App\Models\Campus;
use App\Models\ProgramLevel;
use Illuminate\Http\Resources\Json\JsonResource;

class PayIncomeResource extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'matric' => $this->matric,
            'class' => ProgramLevel::find($this->program_id)->program()->first()->name .' : LEVEL '.ProgramLevel::find($this->program_id)->level()->first()->level,
            'campus' => Campus::find($this->campus_id)->name,
            'campus_id' => $this->campus_id,
            'gender' => $this->gender,
            'link' => route('admin.income.pay_income.collect', [$this->program_id, $this->id]),
            'link2' => route('admin.scholarship.award', $this->id)
        ];
    }
}
