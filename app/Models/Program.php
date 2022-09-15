<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'name',
        'duration',
        'degree_id',
    ];

    public function paymentTypes()
    {
        return $this->hasMany(PaymentType::class);
    }

    public function courses()
    {
        return $this->hasManyThrough(Course::class, ProgramCourse::class);
    }

    public function degree()
    {
        return $this->belongsTo(Degree::class);
    }
}
