<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'background_id', 'sem', 'program_id'];

    public function backgroound()
    {
        # code...
        return $this->belongsTo(SchoolUnits::class, 'background_id');
    }
}
