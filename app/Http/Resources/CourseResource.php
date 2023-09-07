<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'coef' => $this->coef,
            'cv' => $this->cv,
            'code' => $this->code,
            'date' => $this->created_at->format('Y-m-d'),
            'semester' => $this->semester->name,
            'level' => $this->level->level
        ];
    }
}
