<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampusProgram extends Model
{
    use HasFactory;

    protected $fillable = [
        'campus_id',
        'program_id'
    ];

    public function campus()
    {
        return $this->belongsToMany(Campus::class);
    }

    public function programs()
    {
        return $this->belongsToMany(Program::class);
    }
}
