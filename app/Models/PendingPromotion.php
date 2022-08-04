<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PendingPromotion extends Model
{
    use HasFactory;

    protected $table = 'pending_promotions';
    protected $fillable = [
        'from_year', 'to_year', 'from_class', 'to_class', 'type', 'students'
    ];

    function fromYear()
    {
        # code...
        return $this->hasOne(Batch::class, 'from_year');
    }
    function toYear()
    {
        # code...
        return $this->hasOne(Batch::class, 'to_year');
    }
    function fromClass(){
        return $this->hasOne(StudentClass::class, 'from_class');
    }
    function toClass(){
        return $this->hasOne(StudentClass::class, 'to_class');
    }

    // function students(){
    //     return $this->has;
    // }
}
