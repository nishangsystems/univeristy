<?php

namespace App\Http\Resources;

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
            'class_name' => $this->class_name,
            'class_id' => $this->class_id,
            'gender' => $this->gender,
            'link' => route('admin.income.pay_income.collect', [$this->class_id, $this->id]),
            'link2' => route('admin.scholarship.award', $this->id)
        ];
    }
}
