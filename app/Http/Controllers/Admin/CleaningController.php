<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ClassSubject;
use App\Models\Result;
use App\Models\StudentSubject;
use App\Models\Subjects;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CleaningController extends Controller
{
    //

    public function courses(){
        $data['title'] = "Cleaning Courses To Ensure Unique Course Codes";
        Subjects::all()->each(function($rec){
            $rec->code = str_replace(' ', '', $rec->code);
            $rec->save();
        });
        $courses = Subjects::select(['id', 'code'])->get()
        ->each(function($rec){$rec->code = str_replace(' ', '', $rec->code);})->groupBy('code')->map(function($colc){
            return $colc->count() > 1 ? $colc : null;
        })->filter(function($row){return $row != null;});
        $data['duplicates'] = $courses->count();
        $replacements = $courses->map(function($scol){
            return  ['take'=>$scol->shift()->toArray(), 'drop'=>$scol->pluck('id')->toArray()];
        });
        $data['replacements'] = $replacements;
        $drops = 0;
        foreach ($replacements as $key => $rep) {
            $drops += count($rep['drop']);
            # code...
            // drop dropable courses and update results, course registrations and class courses
            Subjects::whereIn('id', $rep['drop'])->each(function($fl){$fl->delete();});
            Result::whereIn('subject_id', $rep['drop'])->update(['subject_id'=>$rep['take']]);
            StudentSubject::whereIn('course_id', $rep['drop'])->update(['course_id'=>$rep['take']]);
            ClassSubject::whereIn('subject_id', $rep['drop'])->update(['subject_id'=>$rep['take']]);
        }
        $data['drops'] = $drops;
        return view('admin.subject.cleaner', $data);
    }
}
