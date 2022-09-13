<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramGrading extends Model
{
    use HasFactory;

    protected $fillable = ['program_id', 'grading_id'];

    public function programs()
    {
        return $this->belongsTo(Program::class);
    }

    public function grading()
    {
        return $this->belongsTo(Grading::class);
    }
}
