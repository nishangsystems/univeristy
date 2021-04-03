<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Session;
class AssignmentController extends Controller
{

    public function index($course_id, $year_id, $semester_id){
        $data['year_id'] = $year_id;
        $data['semester_id'] = $semester_id;
        $data['assignments'] = \App\Course::find($course_id)->assignment()->where(['materials.year_id'=>$year_id,'materials.semester_id'=>$semester_id])->get();
        $data['title'] = "Assignments for ".\App\Year::find($year_id)->name."  ".\App\Semesters::find($semester_id)->byLocale()->name;
        return view('student.assignment.index')->with($data);
    }


    public function view($id){
        $data['assignment'] = \App\Materials::find($id);
        return view('student.assignment.view')->with($data);
    }

    public function __construct(){
        $this->middleware('auth:student');
    }

    public function course($type){
        $year_id =  \App\Helpers\Helpers::instance()->getCurrentAccademicYear(\Auth::guard('student')->user()->studentInfo->level->name);
        $semester_id =  \App\Helpers\Helpers::instance()->getCurrentSemester(\Auth::guard('student')->user()->studentInfo->level->name);
        $data['type'] = $type;
        $data['year_id'] = $year_id;
        $data['semester_id'] = $semester_id;
        $id =  \Auth::guard('student')->user()->id;
        $data['title'] = "Courses for ".\App\Year::find($year_id)->name."  ".\App\Semesters::find($semester_id)->byLocale()->name;

        if(!(\Auth::guard('student')->user()->studentInfo->options->department_id == 9)){
            if($type == 'notes'){
                $data['courses'] =     \App\Course::select('courses.id','courses.title','courses.title_fr','courses.course_code')
                                ->join('student_course',['student_course.course_id'=>'courses.id'])
                                ->where(['student_course.student_id'=>$id, 'student_course.year_id'=>$year_id,'student_course.semester_id'=>$semester_id ])
                                ->join('materials', ['materials.course_id'=>'student_course.course_id'])
                                ->distinct('courses.id')
                                ->where('type','0')->get();
                return view('student.notes.courses')->with($data);
            }else{
                $data['courses'] =   \App\Course::select('courses.id','courses.title','courses.title_fr','courses.course_code')
                                    ->join('student_course',['student_course.course_id'=>'courses.id'])
                                    ->where(['student_course.student_id'=>$id, 'student_course.year_id'=>$year_id,'student_course.semester_id'=>$semester_id ])
                                    ->join('materials', ['materials.course_id'=>'student_course.course_id'])
                                    ->distinct('courses.id')
                                    ->where('type','1')->get();
                return view('student.assignment.courses')->with($data);
            }
        }else{
           $program_id = \Auth::guard('student')->user()->studentInfo->options->id;
           $level_id =  \Auth::guard('student')->user()->studentInfo->level->id;
          if($type == 'notes'){
                $data['courses'] =  \App\Course::select('courses.id','courses.title','courses.title_fr','courses.course_code')
                                    ->where('programe_id',$program_id)
                                    ->where('sem', $semester_id)
                                    ->where('level_id',$level_id)->get();
                return view('student.notes.courses')->with($data);
            }else{
                   $data['courses'] =  \App\Course::select('courses.id','courses.title','courses.title_fr','courses.course_code')
                                                   ->where('programe_id',$program_id)
                                                   ->where('sem', $semester_id)
                                                   ->where('level_id',$level_id)->get();
                return view('student.assignment.courses')->with($data);
            }
        }
    }

    public function allcourse(Request $request, $type){
        $year_id = $request->year;
        $semester_id = $request->semester;
        $data['year_id'] = $year_id;
        $data['type'] = $type;
        $data['semester_id'] = $semester_id;
        $id =  \Auth::guard('student')->user()->id;
        $data['title'] = "Courses for ".\App\Year::find($year_id)->name."  ".\App\Semesters::find($semester_id)->byLocale()->name;
        if($type == 'notes'){
            $data['courses'] =  \App\Course::select('courses.id','courses.title','courses.title_fr','courses.course_code')
            ->join('student_course',['student_course.course_id'=>'courses.id'])
            ->where(['student_course.student_id'=>$id, 'student_course.year_id'=>$year_id,'student_course.semester_id'=>$semester_id ])
            ->join('materials', ['materials.course_id'=>'student_course.course_id'])
            ->distinct('courses.id')
            ->where('type','0')->get();
            return view('student.notes.courses')->with($data);
        }else{
            $data['courses'] =  \App\Course::select('courses.id','courses.title','courses.title_fr','courses.course_code')
            ->join('student_course',['student_course.course_id'=>'courses.id'])
            ->where(['student_course.student_id'=>$id, 'student_course.year_id'=>$year_id,'student_course.semester_id'=>$semester_id ])
            ->join('materials', ['materials.course_id'=>'student_course.course_id'])
            ->distinct('courses.id')
            ->where('type','1')->get();
            return view('student.assignment.courses')->with($data);
        }
    }

    public function reply(Request $request, $id){
        $year_id =  \App\Helpers\Helpers::instance()->getCurrentAccademicYear(\Auth::guard('student')->user()->studentInfo->level->name);
        $semester_id =  \App\Helpers\Helpers::instance()->getCurrentSemester(\Auth::guard('student')->user()->studentInfo->level->name);
        $data['year_id'] = $year_id;
        $data['semester_id'] = $semester_id;
        $data['assignment'] = \App\Materials::find($id);
        return view('student.assignment.newreply')->with($data);
    }
    public function replyPost(Request $request, $id){
        //Validate input
        $validator = Validator::make($request->all(), [
            'content' => 'required',
            'file' => 'nullable',
        ]);

        //check if input is valid before moving on
        if ($validator->fails()) {
            return redirect()->back()->withErrors( $validator->errors()->first());
        }

        $response = new \App\AssignmentResponse();
        $response->student_id = \Auth::guard('student')->user()->id;
        $response ->year_id = \App\Helpers\Helpers::instance()->getCurrentAccademicYear(\Auth::guard('student')->user()->studentInfo->level->name);
        $response ->semester_id = \App\Helpers\Helpers::instance()->getCurrentSemester(\Auth::guard('student')->user()->studentInfo->level->name);
        $response ->assignment_id = $id;
        $response ->message = $request->content;
        if($request->file('file')!=null){
            $response->file = explode('/', $request->file->store('files'))[1];
        }
        $response->save();
        return redirect()->to(route('student.assignment.reply.view', $response->id))->with('s', "Assignment reply was saved sucsessfully!");
    }
    public function replyView(Request $request, $id){
        $data['reply'] = \App\AssignmentResponse::find($id);
        return view('student.assignment.reply')->with($data);
    }

    public function delete(Request $request, $id){
        $reply = \App\AssignmentResponse::find($id);
        $reply->delete();

        return redirect()->to(route('student.assignment.view', $reply->assignment_id))->with('s', "Reply deleted successfully !");
    }
}
