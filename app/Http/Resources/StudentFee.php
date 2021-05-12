<?php

namespace App\Http\Resources;

use App\Helpers\Helpers;
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
            'name'=> $this->name,
            'link'=>route('admin.fee.student.payments.index',[$this->id]),
            'bal'=>$this->bal(),
            'total'=>$this->total(),
            'class'=>$this->class(Helpers::instance()->getYear())->name,
            'paid'=>$this->paid(),
        ];
    }
}
