<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Background extends Model
{
    use HasFactory;
    protected $fillable = ['background_name'];

    public function semesters()
    {
        # code...
        return $this->hasMany(Semester::class, 'background_id');
    }
    public function currentSemesters()
    {
        # code...
        return $this->hasMany(Semester::class, 'background_id')->where(['semesters.status'=>1]);
    }
}
