<?php

namespace App\Http\Resources;

use App\Models\Campus;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Request;

class PayCashIncomeResource extends JsonResource
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
            'id'=>$this->id,
            'name'=>$this->name,
            'username'=>$this->username,
            'email'=>$this->email,
            // 'campus'=>Campus::find($this->campus_id)->name,
            'type'=>$this->type,
            'link'=>url()->previous().'?us='.$this->id,
            'gender'=>$this->gender
        ];
    }
}
