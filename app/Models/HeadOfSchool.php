<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeadOfSchool extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'school_unit_id', 'status'];

    public function user()
    {
        # code...
        return $this->belongsTo(User::class, 'user_id');
    }

    public function school()
    {
        # code...
        return $this->belongsTo(SchoolUnits::class, 'school_unit_id');
    }

}
