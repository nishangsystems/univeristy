<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramSemesterType extends Model
{
    use HasFactory;
    protected $fillable = ['program_id', 'semester_type_id'];
    
}
