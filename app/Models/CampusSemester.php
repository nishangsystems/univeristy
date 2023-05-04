<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;

class CampusSemester extends Model
{
    use HasFactory;
     protected $table  = 'campus_semesters';
     protected $fillable = ['campus_id', 'semester_id', 'ca_date_line', 'exam_date_line'];

     public function campus()
     {
        # code...
        return $this->belongsTo(Campus::class, 'campus_id');
     }

     public function semester()
     {
        # code...
        return $this->belongsTo(Semester::class, 'semester_id');
     }

     public function ca_date_line()
     {
        # code...
        return Date::parse($this->ca_date_line());
     }

     public function exam_date_line()
     {
        # code...
        return Date::parse($this->exam_date_line());
     }
     
    
    public function ca_is_late()
    {
        // return false;
        # code...
        if ($this->ca_date_line != null)
            return now()->isAfter(Date::parse($this->ca_date_line));
        return false;
    }

    public function exam_is_late()
    {
        // return false;
        # code...
        if ($this->exam_date_line != null)
            return now()->isAfter(Date::parse($this->exam_date_line));
        return false;
    }

}
