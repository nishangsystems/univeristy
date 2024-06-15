<?php


namespace App\Http\Controllers\API\Student;


use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Resources\CourseResource;
use App\Http\Resources\StudentResource3;
use App\Models\CampusProgram;
use App\Models\CampusSemesterConfig;
use App\Models\Payments;
use App\Models\ProgramLevel;
use App\Models\Students;
use App\Models\StudentSubject;
use App\Models\Subjects;
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;
use Throwable;

class CourseController extends Controller
{
    public function courses(Request $request)
    {

        try{
            $student = Auth('student_api')->user();
            // return $student;
            
            $_year = $request->year ?? Helpers::instance()->getYear();
            $_semester = $request->semester ?? Helpers::instance()->getSemester($student->_class(Helpers::instance()->getCurrentAccademicYear())->id)->id;
            $rcourses = StudentSubject::where(['student_courses.student_id'=>$student->id])
                ->where(['student_courses.year_id'=>$_year, 'student_courses.semester_id'=>$_semester])
                ->join('subjects', ['subjects.id'=>'student_courses.course_id'])
                ->join('class_subjects', ['class_subjects.subject_id'=>'subjects.id'])->whereNull('class_subjects.deleted_at')
                ->distinct()->orderBy('subjects.name')->get(['subjects.*', 'class_subjects.coef as cv', 'class_subjects.status as status']);
            $courses = StudentSubject::where(['student_courses.student_id'=>$student->id])->where(['student_courses.year_id'=>$_year])
                ->join('subjects', ['subjects.id'=>'student_courses.course_id'])->where(['subjects.semester_id'=>$_semester])
                ->join('class_subjects', ['class_subjects.subject_id'=>'subjects.id'])->whereNull('class_subjects.deleted_at')
                ->distinct()->orderBy('subjects.name')->get(['subjects.*', 'class_subjects.coef as cv', 'class_subjects.status as status'])
                ->filter(function($rec)use($rcourses){return !in_array($rec->id, $rcourses->pluck('id')->toArray());});
            return response()->json(['cv_sum'=>collect($courses)->sum('cv'), 'courses'=> CourseResource::collection($courses)]);
        }catch(\Throwable $th){
            return response()->json(['status'=>400, 'message'=>$th->getMessage(), 'error_type'=>'general-error']);
        }
    }

    public function class_courses(Request $request, $level = null)
    {
        try{

            
            $student = Auth('student_api')->user();
            // return "1234567890";
            $_year = $request->year ?? Helpers::instance()->getYear();
            $_semester = $request->semester ?? Helpers::instance()->getSemester($student->_class()->id)->id;
            
            $rCheck = $this->registration_check();
            $pl = Students::find($student->id)->_class($this->current_accademic_year)->select('program_levels.*')->first();
            $level_id = $level == null ? $pl->level_id : $level;
            $program_id = $pl->program_id;
            $rcourses = StudentSubject::where(['student_courses.student_id'=>$student->id])
                ->where(['student_courses.year_id'=>$_year, 'student_courses.semester_id'=>$_semester])
                ->join('subjects', ['subjects.id'=>'student_courses.course_id'])
                ->join('class_subjects', ['class_subjects.subject_id'=>'subjects.id'])->whereNull('class_subjects.deleted_at')
                ->distinct()->orderBy('subjects.name')->get(['subjects.*', 'class_subjects.coef as cv', 'class_subjects.status as status']);
            $subjects = Subjects::where('semester_id', $_semester)->join('class_subjects', 'class_subjects.subject_id', '=', 'subjects.id')->whereNull('class_subjects.deleted_at')
                ->join('program_levels', 'program_levels.id', '=', 'class_subjects.class_id')
                ->where('program_levels.level_id',$level_id)->where('program_levels.program_id', $program_id)
                ->get(['subjects.*', 'class_subjects.coef as cv', 'class_subjects.status as status'])
                ->filter(function($rec)use($rcourses){return !in_array($rec->id, $rcourses->pluck('id')->toArray());})
                ->sortBy('name')->all();
            return response()->json(['success'=>200, 'courses'=>CourseResource::collection($subjects), 'can_register'=>$rCheck['can'], 'reason'=>$rCheck['reason']]);
        }
        catch(Throwable $th){
            // throw $th;
            return response()->response(['status'=>400, 'message'=>$th->getMessage(), 'error_type'=>'general-error']);
        }
    }

    public function register(Request $request)//takes courses=[course_ids]
    {

        
        $student = Auth('student_api')->user();

        $year = $this->current_accademic_year;
        $semester = Helpers::instance()->getSemester($student->_class($this->current_accademic_year)->id)->id;
        $_semester = Helpers::instance()->getSemester($student->_class($this->current_accademic_year)->id)->background->semesters()->orderBy('sem', 'DESC')->first()->id;
        try {
            $rCheck = $this->registration_check();
            if ($rCheck['can'] == false) {
                return response()->json(['success'=>400, 'message'=>$rCheck['reason'], 'error_type'=>$rCheck['error_type']]);
            }
            if ($request->has('courses')) {
                # code...
                foreach (json_decode($request->courses, true) as $key => $value) {
                    # code...
                    StudentSubject::create(['year_id'=>$year, 'semester_id'=>$semester, 'student_id'=>$student->id, 'course_id'=>$value]);
                }
            }
            // DB::commit();
            return response()->json(['success'=>200, 'message'=>"Course Registered Successfully"]);
        } catch (\Throwable $th) {
            // DB::rollBack();
            return response()->json(['success'=>400, 'message'=>"Something went wrong , try again"]);
        }
    }

    public function drop(Request $request)//takes courses=[course_ids]
    {

        $student = Auth('student_api')->user();

        $year = $this->current_accademic_year;
        $semester = Helpers::instance()->getSemester($student->_class($this->current_accademic_year)->id)->id;
        $_semester = Helpers::instance()->getSemester($student->_class($this->current_accademic_year)->id)->background->semesters()->orderBy('sem', 'DESC')->first()->id;
        try {
            $rCheck = $this->registration_check();
            if ($rCheck['can'] == false) {
                return response()->json(['success'=>400, 'message'=>$rCheck['reason'], 'error_type'=>$rCheck['error_type']]);
            }
            if ($request->has('courses')) {
                foreach (json_decode($request->courses, true) as $key => $value) {
                    # code...
                    StudentSubject::where(['year_id'=>$year, 'semester_id'=>$semester, 'student_id'=>$student->id, 'course_id'=>$value])->each(function($rec){$rec->delete();});
                }
            }
            // DB::commit();
            return response()->json(['success'=>200, 'message'=>"Course(s) Droped Successfully"]);
        } catch (\Throwable $th) {
            // DB::rollBack();
            return response()->json(['success'=>400, 'message'=>"Something went wrong , try again", 'error_type'=>'general-error'], 400);
        }
    }

    private function registration_check($student_id = null){
        
        $student = auth('student_api')->user();
        
        $student_class = $student->_class($this->current_accademic_year);
        $semester = Helpers::instance()->getSemester($student_class->id);
        $_semester = Helpers::instance()->getSemester($student_class->id)->background->semesters()->orderBy('sem', 'DESC')->first()->id;
        if ($semester->id == $_semester) {
            # code...
            return ['can'=>false, 'reason'=>'Resit registration can not be done here. Do that under \"Resit Registration\"', 'error_type'=>'general-error'];
        }
        $fee = [
            'amount' => array_sum(
                Payments::where('payments.student_id', '=', $student->id)
                ->join('payment_items', 'payment_items.id', '=', 'payments.payment_id')
                ->where('payment_items.name', '=', 'TUTION')
                ->where('payments.batch_id', '=', $this->current_accademic_year)
                ->pluck('payments.amount')
                ->toArray()
            ),
            'total' => 
                    CampusProgram::join('program_levels', 'program_levels.id', '=', 'campus_programs.program_level_id')
                    ->join('payment_items', 'payment_items.campus_program_id', '=', 'campus_programs.id')
                    ->where('payment_items.name', '=', 'TUTION')
                    ->whereNotNull('payment_items.amount')
                    ->join('students', 'students.program_id', '=', 'program_levels.id')
                    ->where('students.id', '=', $student->id)->pluck('payment_items.amount')[0] ?? 0,
            'fraction' => $semester->courses_min_fee
        ];
        $conf = CampusSemesterConfig::whereNull('campus_id')->where(['semester_id'=>$semester->id])->first();
        if ($conf != null) {
            # code...
            if(($data['on_time'] = strtotime($conf->courses_date_line)) > strtotime(date('d-m-Y'))){
                return ['can'=>false, 'reason'=>'Course registration dateline has passed', 'error_type'=>'course-dateline-error'];
            };
        }else{
            return ['can'=>false, 'reason'=>'Can not sign courses for this program at the moment. Date limit not set. Contact registry.',  'error_type'=>'course-dateline-error'];
        }
        $data['min_fee'] = number_format($fee['total']*$fee['fraction']);
        $data['access'] = ($fee['total'] + $student->total_debts($this->current_accademic_year)) >= $data['min_fee']  || $student->classes()->where(['year_id'=>$this->current_accademic_year])->first()->bypass_result;
        if(!$data['access']){
            return ['can'=>false, 'reason'=>"Minimum fee requirement not met. You must pay at least {$data['fraction']} ({$data['min_fee']}) of the total fee to register your courses",  'error_type'=>'min-fee-error'];
        }
        return ['can'=>true, 'reason'=>null];
        
    }

    public function registration_eligible(Request $request){
        $rCheck = $this->registration_check($request->student_id);
        return response()->json(['success'=>200, 'eligible'=>$rCheck['can']?"YES":"NO", 'message'=>$rCheck['reason'], 'error_type'=>$rCheck['can'] ? '' : $rCheck['error_type']]);
    }

    public function form_b(Request $request)// expects $year:int and $semester:int as request data;
    {
        try {
            //code...
            $student = auth('student_api')->user();
            
            // return $request->all();
            $year = $request->year != null ? $request->year : $this->current_accademic_year;
            $semester = $request->semester != null ? $request->semester : Helpers::instance()->getSemester($student->_class($year)->id)->id;
    
            $reg = $this->_registerd_courses($year, $semester, $student->id)->getData();
            // return $reg;
            $data['cv_sum'] = $reg->cv_sum;
            $data['courses'] = $reg->courses;
            $data['user'] = $student;
            $data['semester'] = $semester;
            $data['year'] = $year;
            $data['class'] = $data['user']->_class($year);
            $data['title'] = "Registered Courses";
            // return $data;
            $fname = 'files/'.str_replace('/', '_', $data['user']->matric).'_'.time().'_FORM_B.pdf';
            $fpath = public_path($fname);
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('student.courses.form_b_template',$data);
            $pdf->save($fpath);
            return response()->json(['status'=>'success', 'url'=>asset($fname)]);
        } catch (\Throwable $th) {
            // throw $th;
            return response()->json(['success'=>400, 'message'=>$th->getMessage(), 'error_type'=>'general-error']);
        }
    }

    private function _registerd_courses($year = null, $semester = null, $student = null )
    {
        try {
            //code...
            $user = auth('student_api')->user();
            $_year = $year ?? $this->current_accademic_year;
            $_semester = $semester ?? Helpers::instance()->getSemester($user->_class($_year)->id)->id;
            $class = $user->_class($_year);
            $yr = \App\Models\Batch::find($_year);
            $sem = \App\Models\Semester::find($_semester);
            # code...

            // return response()->json(['user'=>$_student, 'year'=>$year, 'semester'=>$semester]);
            $courses = StudentSubject::where(['student_courses.student_id'=>$user->id])->where(['student_courses.year_id'=>$_year, 'student_courses.semester_id'=>$semester])
                    ->join('subjects', ['subjects.id'=>'student_courses.course_id'])
                    // ->join('class_subjects', ['class_subjects.subject_id'=>'subjects.id'])
                    ->join('class_subjects', ['class_subjects.subject_id'=>'subjects.id'])->whereNull('class_subjects.deleted_at')
                    ->distinct()->orderBy('subjects.name')->get(['subjects.*', 'class_subjects.coef as cv', 'class_subjects.status as status']);
            return response()->json([
                'ids'=>$courses->pluck('id'), 
                'cv_sum'=>collect($courses)->sum('cv'), 
                'courses'=>$courses,
                'year'=>['id'=>$yr->id, 'name'=>$yr->name],
                'semester'=>['id'=>$sem->id, 'name'=>$sem->name],
                'class'=>$class == null ? "" : ['id'=>$class->id, 'name'=>$class->name()],
            ]);
        } catch (\Throwable $th) {
            // throw $th;
            return response()->response(['status'=>400, 'message'=>$th->getMessage(), 'error_type'=>'general-error']);
            
        }
    }

    public function registered_courses(Request $request)
    {
        # code...
        return $this->_registerd_courses($request->year, $request->semester);
    }
}