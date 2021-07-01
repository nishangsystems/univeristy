<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollectBoardingFee extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'amount_payable',
        'batch_id',
        'status',
        'class_id'
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
}
