<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampusSemesterConfig extends Model
{
    use HasFactory;

    protected $fillable = ['campus_id','semester_id', 'courses_date_line'];
    protected $table =  'campus_semester_config';
    protected $connection = 'mysql';

    public function campus(){
        return $this->belongsTo(Campus::class, 'campus_id');
    }

    public function semester(){
        return $this->belongsTo(Semester::class, 'semester_id');
    }
}
