<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BulkMarkChange extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = "tracking_db";
    protected $table = "bulk_mark_changes";
    protected $fillable = ['year_id', 'semester_id', 'course_id', 'background_id', 'class_id', 'action', 'additional_mark', 'actor', 'interval', 'deleted_at'];
    protected $dates = ['created_at'];

    public function year(){
        return $this->belongsTo(Batch::class, 'year_id');
    }

    public function semester(){
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    public function course(){
        return $this->belongsTo(Subjects::class, 'course_id');
    }

    public function class(){
        return $this->belongsTo(ProgramLevel::class, 'class_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'actor');
    }
    
    public function background(){
        return $this->belongsTo(Background::class, 'background_id');
    }

    public function interval(){
        return json_decode($this->interval);
    }

    public function records(){
        // dd($this->interval());
        switch($this->action){
            case "BULK_MARK_ADDED":
                if(($class = $this->class) != null){
                    return $class->_students($this->year_id)->join('results', ['results.student_id'=>'students.id'])
                        ->where('results.batch_id', $this->year_id)->whereNotNull('results.exam_score')
                        ->where('results.semester_id', $this->semester_id)
                        ->where('results.subject_id', $this->course_id)
                        ->pluck('results.id')->toArray();
                }
                elseif (($background = $this->background) != null) {
                    # code...
                    return Result::where('results.batch_id', $this->year_id)
                        ->whereNotNull('results.exam_score')
                        ->where('results.semester_id', $this->semester_id)
                        ->where('results.subject_id', $this->course_id)
                        ->pluck('results.id')->toArray();
                }else{
                    return Result::where('results.batch_id', $this->year_id)
                        ->whereNotNull('results.exam_score')
                        ->where('results.subject_id', $this->course_id)
                        ->pluck('results.id')->toArray();
                }
                break;
            case "BULK_MARK_ROUNDOFF":
                if(($class = $this->class) != null){
                    return $class->_students($this->year_id)->join('results', ['results.student_id'=>'students.id'])
                        ->where('results.batch_id', $this->year_id)->whereNotNull('results.exam_score')
                        ->where('results.semester_id', $this->semester_id)
                        ->where('results.subject_id', $this->course_id)
                        ->select(['results.id', \Illuminate\Support\Facades\DB::raw("SUM(exam_score + ca_score) as total")])->get()
                        ->where('total', '>=', intval($this->interval()->lower_limit) + $this->additional_mark)
                        ->where('total', '<=', intval($this->interval()->upper_limit) + $this->additional_mark)
                        ->pluck('id')->toArray();
                }
                elseif (($background = $this->background) != null) {
                    # code...
                    return Result::where('results.batch_id', $this->year_id)
                        ->whereNotNull('results.exam_score')
                        ->where('results.semester_id', $this->semester_id)
                        ->where('results.subject_id', $this->course_id)
                        ->select(['results.id', \Illuminate\Support\Facades\DB::raw("SUM(exam_score + ca_score) as total")])->get()
                        ->where('total', '>=', intval($this->interval()->lower_limit) + $this->additional_mark)
                        ->where('total', '<=', intval($this->interval()->upper_limit) + $this->additional_mark)
                        ->pluck('id')->toArray();
                }else{
                    return Result::where('results.batch_id', $this->year_id)
                        ->whereNotNull('results.exam_score')
                        ->where('results.subject_id', $this->course_id)
                        ->select(['results.id', \Illuminate\Support\Facades\DB::raw("SUM(exam_score + ca_score) as total")])->get()
                        ->where('total', '>=', intval($this->interval()->lower_limit) + $this->additional_mark)
                        ->where('total', '<=', intval($this->interval()->upper_limit) + $this->additional_mark)
                        ->pluck('id')->toArray();
                }
                break;
        }
    }
}
