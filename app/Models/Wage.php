<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wage extends Model
{
    use HasFactory;

    protected $table = 'wages';
    protected $connection = 'mysql';
    protected $fillable = ['level_id', 'teacher_id', 'price'];


    public function level()
    {
        # code...
        return $this->belongsTo(Level::class, 'level_id');
    }

    public function teacher()
    {
        # code...
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
