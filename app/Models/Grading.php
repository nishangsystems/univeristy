<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grading extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'num'];

    public function programs()
    {
        return $this->hasManyThrough(Program::class, ProgramGrading::class);
    }
}
