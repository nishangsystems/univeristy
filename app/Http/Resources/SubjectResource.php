<?php

namespace App\Http\Resources;

use App\Helpers\Helpers;
use App\Models\Students;
use App\Models\Subjects;
use Illuminate\Http\Resources\Json\JsonResource;

class SubjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $course = Subjects::find($this->id);
        return [
            'id'=> $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'semester' => $course->semester->name,
        ];
    }
}
