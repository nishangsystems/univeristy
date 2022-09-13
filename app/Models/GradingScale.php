<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradingScale extends Model
{
    use HasFactory;

    protected $fillable = [
        'min', 'max', 'grade', 'weight', 'grading_id', 'remark', 'status'
    ];

    public function grading()
    {
        return $this->belongsTo(Grading::class);
    }
}
