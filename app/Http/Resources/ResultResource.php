<?php

namespace App\Http\Resources;

use App\Helpers\Helpers;
use App\Models\Batch;
use App\Models\ProgramLevel;
use Illuminate\Http\Resources\Json\JsonResource;

class ResultResource extends JsonResource
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
            'matric'=>$this->matric,
            'year'=> Batch::find($this->year_id)->name,
            'class'=> ProgramLevel::find($this->class_id)->name(),
            'rlink'=>route('admin.result.individual_results.print', [$this->student_id])
        ];
    }
}
