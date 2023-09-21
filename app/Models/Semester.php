<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    use HasFactory;
    protected $connection = 'mysql';

    protected $fillable = ['name', 'background_id', 'sem', 'program_id', 'ca_latest_date', 'exam_latest_date', 'result_charges', 'user_id'];

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
            return now()->isAfter(Carbon::createFromDate($this->ca_latest_date));
        return false;
    }

    public function exam_is_late()
    {
        // return false;
        # code...
        if ($this->exam_latest_date != null)
            return now()->isAfter(Carbon::createFromDate($this->exam_latest_date));
        return false;
    }

    public function result_is_published($year, $student_id = null){
        if (Result::where(['batch_id'=>$year, 'semester_id'=>$this->id, 'student_id'=>$student_id != null ? $student_id : auth('student')->id(), 'published'=>1])->count() > 0) {
            # code...
            return true;
        }
        return false;
    }
}
