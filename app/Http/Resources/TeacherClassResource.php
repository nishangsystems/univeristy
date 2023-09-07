<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TeacherClassResource extends JsonResource
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
            'id'=> $this->id,
            "level_id"=>$this->level_id,
            "campus_id"=>$this->campus_id,
            'name' => $this->program()->first()->name.' : LEVEL '.$this->level()->first()->level,
            'campus' => \App\Models\Campus::find($this->campus_id)->name,
        ];
    }
}
