<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'background_id', 'sem', 'program_id', 'ca_latest_date', 'exam_latest_date'];

    public function background()
    {
        # code...
        return $this->belongsTo(Background::class, 'background_id');
    }

    public function sequences()
    {
        # code...
        return $this->hasMany(Sequence::class, 'term_id');
    }

    
    public function ca_is_late()
    {
        // return false;
        # code...
        if ($this->ca_latest_date != null)
            return strtotime($this->ca_latest_date) <= strtotime(date('Y-m-d'));
        return true;
    }

    public function exam_is_late()
    {
        // return false;
        # code...
        if ($this->exam_latest_date != null)
            return strtotime($this->exam_latest_date) <= strtotime(date('Y-m-d'));
        return true;
    }
}
