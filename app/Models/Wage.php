<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wage extends Model
{
    use HasFactory;

    protected $table = 'wages';
    protected $fillable = ['background_id', 'level_id', 'teacher_id', 'price'];

    public function background()
    {
        # code...
        return $this->belongsTo(Background::class, 'background_id');
    }

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
