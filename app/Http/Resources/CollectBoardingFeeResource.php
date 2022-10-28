<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CollectBoardingFeeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return  [
            'id' => $this->id,
            'name' => $this->name, 
            'matric' => $this->matric, 
            'class_name' => $this->class_name ,
            'class_id' => $this->class_id,
            'link' =>  route('admin.collect_boarding_fee.collect', [$this->class_id, $this->id])
        ];
    }
}
