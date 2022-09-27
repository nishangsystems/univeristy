<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramLevel extends Model
{
    use HasFactory;
    
    protected $fillable = ['program_id', 'level_id'];
    
    public function program()
    {
        return $this->belongsTo(SchoolUnits::class, 'program_id');
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }
}
