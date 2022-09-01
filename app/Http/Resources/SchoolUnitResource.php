<?php

namespace App\Http\Resources;

use App\Helpers\Helpers;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolUnitResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'prefix'        => $this->prefix,
            'suffix'        => $this->suffix,
            'unit_id'       => $this->unit_id,
            'parent_id'     => $this->parent_id,
            'base_class'    => $this->base_class,
            'target_class'  => $this->target_class,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
            'sub_units'     => $this->unit,
        ];
    }
}
