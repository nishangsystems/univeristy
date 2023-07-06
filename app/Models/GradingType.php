<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradingType extends Model
{
    use HasFactory;

    protected $table = 'grading_types';
    protected $connection = 'mysql';
    protected $fillable = ['name'];

    public function grading()
    {
        # code...
        return $this->hasMany(Grading::class, 'grading_type_id');
    }
}
