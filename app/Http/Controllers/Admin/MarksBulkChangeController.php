<?php

namespace App\Http\Controllers\admin;

use App\Events\BulkMarkAddedEvent;
use App\Http\Controllers\Controller;
use App\Models\Background;
use App\Models\Batch;
use App\Models\ProgramLevel;
use App\Models\Result;
use App\Models\Semester;
use App\Models\Subjects;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MarksBulkChangeController extends Controller
{
    //
    public function exam_add_mark(Request $request, $year_id = null, $semester_id = null, $course_id = null, $class_id = null){
        $data['title'] = "Add Exam Marks";
        $data['year_id'] = $year_id;
        $data['semester_id'] = $semester_id;
        $data['course_id'] = $course_id;
        $data['class_id'] = $class_id;
        $data['years'] = Batch::all();
        $data['semesters'] = Semester::orderBy('name')->get();
        $data['classes'] = $this->sorted_program_levels();
        if($course_id != null){
            $data['year'] = Batch::find($year_id);
            $data['semester'] = Semester::find($semester_id);
            $data['course'] = Subjects::find($course_id);
            $data['title'] = "Add Exam Marks For {$data['course']->code}, {$data['semester']->name} {$data['year']->name}";
            if($class_id != null){
                $data['class'] = ProgramLevel::find($class_id);
                $data['title'] = "Add Exam Marks For [{$data['course']->code}], {$data['class']->name()}, {$data['semester']->name} {$data['year']->name}";
            }
        }
        return view('admin.res_and_trans.marks.add', $data);
    }
    
    //
    public function exam_add_mark_save(Request $request, $year_id, $semester_id, $course_id, $class_id = null){
        if($request->mark == null){
            return back()->withInput()->with('error', "mark field is requird");
        }

        if($class_id == null)
            $collection = Result::where(['batch_id'=>$year_id, 'semester_id'=>$semester_id, 'subject_id'=>$course_id,])->get();
        else
            $collection = Result::where(['batch_id'=>$year_id, 'semester_id'=>$semester_id, 'subject_id'=>$course_id, 'class_id'=>$class_id])->get();

        if ($collection->count() > 0) {
            # code...
            $collection->each(function($record)use($request){
                $record->exam_score += $request->mark;
                $record->save();
            });

            // track updates here
            event(new BulkMarkAddedEvent($year_id, $semester_id, $course_id, $action = "BULK_MARK_ADDED", $actor = auth()->user(), $additional_mark = $request->mark, $class_id = $class_id));
        }else {
            session()->flash('error', "No matching result records found");
        }

        return back()->with('success', 'Done');
    }
    
    //
    public function exam_roundoff_mark(Request $request, $year_id = null, $background_id=null, $semester_id = null, $course_id = null){
        $data['title'] = "Roundoff Exam Marks";
        $data['year_id'] = $year_id;
        $data['semester_id'] = $semester_id;
        $data['course_id'] = $course_id;
        $data['background_id'] = $background_id;
        $data['years'] = Batch::all();
        $data['semesters'] = Semester::orderBy('name')->get();
        $data['backgrounds'] = Background::all();
        if($course_id != null){
            $data['year'] = Batch::find($year_id);
            $data['semester'] = Semester::find($semester_id);
            $data['course'] = Subjects::find($course_id);
            $data['title'] = "Roundoff Exam Marks For {$data['course']->code}, {$data['semester']->name} {$data['year']->name}";
            if($background_id != null){
                $data['background'] = Background::find($background_id);
                $data['title'] = "Roundoff Exam Marks For [{$data['course']->code}], {$data['background']->background_name}, {$data['semester']->name} {$data['year']->name}";
            }
        }
        return view('admin.res_and_trans.marks.roundoff', $data);
    }
    
    //
    public function exam_roundoff_mark_save(Request $request, $year_id = null, $background_id=null, $semester_id = null, $course_id = null){
        
        $validity = Validator::make($request->all(), ['mark'=>'required', 'lower_limit'=>'required', 'upper_limit'=>'required']);

        if($validity->fails()){
            return back()->withInput()->with('error', $validity->errors()->first());
        }

        if($background_id == null){
            $background = null;
            $classes = null;
        }else{
            $background = Background::find($background_id);
            $classes = $background->classes;
        }
        $collection = Result::where(['batch_id'=>$year_id, 'semester_id'=>$semester_id, 'subject_id'=>$course_id])->where(function($qry)use($background, $classes){
            $background == null ? null : $qry->whereIn('class_id', $classes->pluck('id')->toArray());
        })->select(['*', DB::raw('SUM(ca_score + IFNULL(exam_score, 0)) as total')])->groupBy('id')
        ->having('total', '>=', $request->lower_limit)->having('total', '<=', $request->upper_limit)
        ->get();

        // dd($collection);
        if ($collection->count() > 0) {
            # code...
            $collection->each(function($record)use($request){
                $record->exam_score += $request->mark;
                $record->save();
            });
            event(new BulkMarkAddedEvent($year_id, $semester_id, $course_id, $action = "BULK_MARK_ROUNDOFF", $actor = auth()->user(), $additional_mark = $request->mark, $background_id = $background_id, $class_id=null, $range = ['lower_limit'=>$request->lower_limit, 'upper_limit'=>$request->upper_limit]));
            
            // track updates here
        }else {
            session()->flash('error', "No matching result records found");
        }

        return back()->with('success', 'Done');
    }
    
}
