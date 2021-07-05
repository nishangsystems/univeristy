<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollectBoardingFee extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'batch_id',
        'class_id',

    ];

    public function student()
    {
        return $this->belongsTo(Students::class, 'student_id');
    }

    public function schoolunit()
    {
        return $this->belongsTo(SchoolUnits::class, 'class_id');
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class, 'batch_id');
    }

    public function boardingAmounts()
    {
        return $this->hasMany(BoardingAmount::class, 'collect_boarding_fee_id');
    }
}
