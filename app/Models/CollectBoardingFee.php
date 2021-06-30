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
        'status'
    ];

    public function student()
    {
        return $this->belongsTo(Students::class, 'student_id');
    }
}
