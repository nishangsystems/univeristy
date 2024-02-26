<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Background extends Model
{
    use HasFactory;
    protected $fillable = ['background_name'];
    protected $connection = 'mysql';

    public function semesters()
    {
        # code...
        if(str_contains($this->background_name, 'MASTERS') ){
            // dd(123123);
            return Semester::join('backgrounds', 'backgrounds.id', '=', 'semesters.background_id')
                ->where('background_name', 'LIKE', '%MASTERS%')->select('semesters.*');
        }
        return $this->hasMany(Semester::class, 'background_id');
    }
    public function currentSemesters()
    {
        # code....
        if(str_contains($this->background_name, 'MASTERS') ){
            return Semester::where(['semesters.status'=>1])
                ->join('backgrounds', 'backgrounds.id', '=', 'semesters.background_id')
                ->where('background_name', 'LIKE', '%MASTERS%')->select('semesters.*');
        }
        return $this->hasMany(Semester::class, 'background_id')->where(['semesters.status'=>1]);
    }

    public function resits()
    {
        # code...
        return $this->hasMany(Resit::class, 'background_id');
    }
}
