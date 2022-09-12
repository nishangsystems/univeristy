<?php

namespace App\Http\Resources;

use App\Helpers\Helpers;
use Illuminate\Http\Resources\Json\JsonResource;

class Fee extends JsonResource
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
            'total' => $request->get('type', 'completed') == 'completed' ? $this->paid() : $this->bal($this->id),
            'class' => $this->class(Helpers::instance()->getYear())->name,
        ];
    }
}
