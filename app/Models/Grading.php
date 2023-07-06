<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grading extends Model
{
    use HasFactory;

    protected $table = 'grading';
    protected $connection = 'mysql';
    protected $fillable = ['lower', 'upper', 'weight', 'grade', 'status', 'remark', 'grading_type_id'];

    public function grading_type()
    {
        # code...
        return $this->belongsTo(GradingType::class, 'grading_type_id');
    }
}
