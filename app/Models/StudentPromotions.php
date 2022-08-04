<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentPromotions extends Model
{
    use HasFactory;

    protected $fillable = ['student_id', 'promotion_id'];

    protected $table = 'student_promotions';

    function student()
    {
        # code...
        return $this->hasOne(Students::class, 'student_id');
    }

    function promotion()
    {
        # code...
        return $this->hasOne(Promotion::class, 'promotion_id');
    }
}
