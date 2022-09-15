<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProgramCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
    //

    public function index()
    {
        # code...
        if(request()->has('program_id')){
            $data['title'] =  \App\Models\Program::find(request('program_id'))->name . ' courses';
            $data['courses'] = \App\Models\Program::find(request('program_id'))->courses();
        }
        else{
            $data['title'] = 'Courses';
            $data['courses'] = \App\Models\Course::all();
        }
    }

    // import courses in .csv or .xls format; Course Code, Course Title, Credit Value, Status
    public function import()
    {
        # code...
        request()->validate([
            'file'=>'required|mimes:csv,xls'
        ]);

        try {
            //code...
            $filename = random_int(10000000, 99999999).'_'.time().request()->file('file')->getClientOriginalExtension();
            request()->file('file')->storePubliclyAs('files', $filename);
            $path = public_path().'/files/'.$filename;
            $file = fopen($path, 'r');
    
            $file_data = [];    //initialize array to read file data
            $fields = ['code', 'name', 'credit_value', 'status'];   //fields expected from import file in order
            $sn = 0;    //counter for the records imported from file.
    
            // read file data into $file_data in and associative mannar
            while (($line = fgetcsv($file)) != null) {
                # code...
                $cnt = count($line);
                for($i = 0; $i < $cnt; $i++){
                    $file_data[$sn][$fields[$i]] = $line[$i];
                }
                $sn++;
            }
    
            fclose($file);
            // store data
            
            $validator = Validator::make(request()->all(),
            [
                'program_id'=>'required',
                'school_level_id'=>'required',
                'degree_semester_id'=>'required',
            ]);
            $duplicates = '';
            array_map(function($el) use ($validator, $duplicates){
                if(count(\App\Models\Course::where('code', $el['code'])->get())==0){
    
                    $course = new \App\Models\Course($el);
                    $course->save();
                    if (!$validator->fails()) {
                        # code...
                        (new ProgramCourse([
                            'program_id'=>request('program_id'), 
                            'course_id'=>$course->id, 
                            'school_level_id'=>request('school_level_id'), 
                            'degree_semester_id'=>request('degree_semester_id'), 
                            'credit_value'=>$course->credit_value
                            ]))->save();
                    }
                }
                $duplicates .= ' '.$el['code'];
            }, $file_data);
    
            if ($duplicates != null) {
                return redirect(route('courses.index'))->with('message', 'Import complete. The following courses already exist and where not uploaded.\n'.$duplicates);
            }
            return redirect(route('courses.index'))->with('success', 'Import complete');
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('error', $th->getMessage());
        }

    }

    // export course to a csv file
    public function export()
    {
        # code...
    }

    // save a course instance to database
    public function store()
    {
        request()->validate([
            'name'=>'required',
            'code'=>'required',
            'credit_value'=>'required',
            'status'=>'in:general,compulsery,required',//[could be available or unavailable ],
            // 'program_id', not a required field in course since there is program_courses
            'school_level_id'=>'required'
        ]);
        
        try {
            //code...
            if (count(\App\Models\Course::where('code', request('code')))>0) {
                return back()->with('error', 'A course already exists with course code '.request('code'));
            }
            $course = new \App\Models\Course(request()->all());
            $course->save();
    
            if (request()->has('program_id')) {
                $program_course = new \App\Models\ProgramCourse(['program_id'=>request('program_id'), 'course_id'=>$course->id]);
                $program_course->save();
                return redirect(route('courses.index').'?program_id='.request('program_id'));
            }
            return redirect(route('courses.index'));
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('error', $th->getMessage());
        }
    }
    public function update()
    {
        request()->validate([
            'course_id'=>'required',
            'name'=>'required',
            'code'=>'required',
            'credit_value'=>'required',
            'type'=>'in:general,compulsery,required',//[could be available or unavailable ],
            // 'program_id', not required in course since there is program_courses
            'school_level_id'=>'required'
        ]);
        
        try {
            //code...
            if (count(\App\Models\Course::where('code', request('code')))>0) {
                if (\App\Models\Course::find(request('course_id'))->code != request('code')) {
                    return back()->with('error', 'A course already exists with course code '.request('code'));
                }
            }
            $course = new \App\Models\Course(request()->all());
            $course->save();
    
            if (request()->has('program_id')) {
                $program_course = new \App\Models\ProgramCourse(['program_id'=>request('program_id'), 'course_id'=>$course->id]);
                $program_course->save();
                return redirect(route('courses.index').'?program_id='.request('program_id'));
            }
            return redirect(route('courses.index'));
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('error', $th->getMessage());
        }
    }

    public function delete()
    {
        // Can not delete a course that  has results, assigned to a program,
        $course = \App\Models\Course::find(request('course_id'));
        if (count($course->programs())>0) {
            # code...
            return back()->with('error', 'Can not delete a course assigned to a program');
        }
        if (count($course->results())>0) {
            # code...
            return back()->with('error', 'Can not delete a course with results');
        }
        $course->delete();
        return redirect(route('courses.index'));
    }
}
