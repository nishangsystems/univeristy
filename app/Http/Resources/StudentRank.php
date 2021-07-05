<?php

namespace App\Http\Resources;

use App\Helpers\Helpers;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentRank extends JsonResource
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
            'id'=> $this->id,
            'name'=> $this->name,
            'matricule'=>$this->matric,
            'total'=> $this->totalScore($request->sequence, $request->year),
            'average'=> $this->averageScore($request->sequence, $request->year),
        ];
    }
}
