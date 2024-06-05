<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Resources\ResultResource;
use App\Models\Batch;
use App\Models\ClassSubject;
use App\Models\Config;
use App\Models\Grading;
use App\Models\OfflineResult;
use App\Models\ProgramLevel;
use App\Models\Result;
use App\Models\Semester;
use App\Models\Sequence;
use App\Models\StudentClass;
use App\Models\Students;
use App\Models\Subjects;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Redirect;
use Illuminate\Support\Facades\Session;

class ResultController extends Controller
{

    public function index(Request $request)
    {
        $data['releases'] = \App\Models\Config::orderBy('id', 'desc')->get();
        $data['title'] = __('text.all_result_releases');
        return view('admin.setting.result.index')->with($data);
    }

    public function create(Request $request)
    {
        $data['title'] = __('text.add_release');
        return view('admin.setting.result.create')->with($data);
    }

    public function edit(Request $request, $id)
    {
        $data['title'] = __('text.edit_result_release');
        $data['release'] = \App\Models\Config::find($id);
        return view('admin.setting.result.edit')->with($data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'year_id' => 'required',
            'seq_id' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
        ]);
        Config::create($request->all());
        return redirect()->to(route('admin.result_release.index'))->with('success', __('text.word_done'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'year_id' => 'required',
            'seq_id' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
        ]);
        $release = \App\Models\Config::find($id);
        $release->update($request->all());
        return redirect()->to(route('admin.result_release.index'))->with('success', __('text.word_done'));
    }

    public function destroy(Request $request, $id)
    {
        $config = Config::find($id);
        if (\App\Models\Config::all()->count() > 0) {
            $config->delete();
            return redirect()->back()->with('success', __('text.word_done'));
        } else {
            return redirect()->back()->with('error', __('text.change_current_accademic_year'));
        }
    }

    public function import()
    {
        return view('admin.result.import');
    }

    public function importPost(Request $request)
    {
        // Validate request
        $request->validate([
            'file' => 'required|mimes:csv,txt,xlxs',
        ]);

        $file = $request->file('file');

        $extension = $file->getClientOriginalExtension();
        $filename = "Names." . $extension;

        $valid_extension = array("csv", "xls");
        if (in_array(strtolower($extension), $valid_extension)) {
            // File upload location
            $location = public_path() . '/files/';
            // Upload file
            $file->move($location, $filename);
            $filepath = public_path('/files/' . $filename);

            $file = fopen($filepath, "r");

            $importData_arr = array();
            $i = 0;

            while (($filedata = fgetcsv($file, 100, ",")) !== FALSE) {
                $num = count($filedata);
                for ($c = 0; $c < $num; $c++) {
                    $importData_arr[$i][] = $filedata[$c];
                }
                $i++;
            }
            fclose($file);

            DB::beginTransaction();
            try {
                foreach ($importData_arr as $k => $importData) {
                    if ($k > 0) {
                        $result = Result::where([
                            'student_id' =>  $importData[1],
                            'class_id' => $importData[2],
                            'sequence' => $importData[3],
                            'subject_id' => $importData[4],
                            'batch_id' => $importData[0]
                        ])->first();

                        if ($result == null) {
                            $result = new Result();
                        }

                        $result->batch_id = $importData[0];
                        $result->student_id =  $importData[1];
                        $result->class_id =  $importData[2];
                        $result->sequence =  $importData[3];
                        $result->subject_id =  $importData[4];
                        $result->score =  $importData[5];
                        $result->coef =  $importData[6] ?? 1;
                        $result->remark = $importData[7];
                        $result->class_subject_id =  $importData[0];
                        $result->save();
                    }
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                echo ($e->getMessage());
            }
            Session::flash('message', __('text.word_done'));
            //echo("<h3 style='color:#0000ff;'>Import Successful.</h3>");

        } else {
            Session::flash('message', __('text.file_type_constraint', ['type'=>'.csv']));
        }
        return redirect()->back()->with('success', __('text.word_done'));
    }

    public function export()
    {
        return view('admin.result.export');
    }

    public function exportPost(Request $request)
    {
        $request->validate([
            'year' => 'required',
            'sequence' => 'required',
        ]);


        $results = Result::where(['batch_id' => $request->year, 'sequence' => $request->sequence])->get();
        $year = Batch::find($request->year);
        $sequence = Sequence::find($request->sequence);

        $fileName = $sequence->name . ' ' . $year->name . ' ' . 'results.csv';

        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $columns = array('batch_id', 'student_id', 'class_id', 'sequence', 'subject_id', 'score', 'coef', 'remark', 'class_subject_id');

        $callback = function () use ($results, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($results as $result) {
                fputcsv($file, array($result->batch_id, $result->student_id, $result->class_id, $result->sequence, $result->subject_id, $result->score, $result->coef, $result->remark, $result->class_subject_id));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function report(Request $request)
    {
        # code...
        $data['title'] = __('text.student_results');
        return view('admin.result.report', $data);
    }

    public function report_show(Request $request)
    {
        return $request->all();
        # code...
    }

    // ADDITIONAL RESULT METHODS FOR OFFLINE APP
    
    public function ca_result(){
        $data['title'] = __('text.student_CA_results');

        return view('admin.result.ca_result', $data);
    }

    public function ca_fill(Request $request){
        // check if CA total is set forthis program
        // if (!Helpers::instance()->ca_total_isset(request('class_id'))) {
        //     # code...
        //     return back()->with('error', __('text.CA_total_not_set_for', ['program'=>__('text.word_program')]));

        // }

        $subject = Subjects::find(request('course_id'));
        $classSubject = $subject->_class_subject($request->class_id);
        $data['ca_total'] = Helpers::instance()->ca_total(request('class_id'));
        $data['title'] = __('text.fill_CA_results_for', ['course'=>"{$subject->name} [ {$subject->code} ] | CV : ".($classSubject->coef ?? $subject->coef)." | ST : ".($classSubject->status ?? $subject->status), 'class'=>ProgramLevel::find(request('class_id'))->name()]);

        return view('admin.result.fill_ca', $data);
    }

    public function ca_import(Request $request){
        // check if CA total is set forthis program
        // if (!Helpers::instance()->ca_total_isset(request('class_id'))) {
        //     # code...
        //     return back()->with('error',  __('text.CA_total_not_set_for', ['program'=>__('text.word_program')]));
        // }

        $subject = Subjects::find(request('course_id'));
        $classSubject = $subject->_class_subject($request->class_id);
        $data['title'] = __('text.import_CA_results_for', ['course'=>"{$subject->name} [ {$subject->code} ] | CV : ".($classSubject->coef ?? $subject->coef)." | ST : ".($classSubject->status ?? $subject->status), 'class'=>ProgramLevel::find(request('class_id'))->name()]);

        return view('admin.result.import_ca', $data);
    }

    public function ca_import_save(Request $request, $class_id, $course_id){
        // return $request->all();
        $check = Validator::make($request->all(), [
            'reference'=>'required',
            'file'=>'required|file'
        ]);

        $ca_total = Helpers::instance()->ca_total(request('class_id'));
        if($check->fails()){
            return back()->with('error', $check->errors()->first());
        }
        $file = $request->file('file');
        if($file != null &&$file->getClientOriginalExtension() == 'csv'){
            $filename = 'ca_'.random_int(1000, 9999).'_'.time().'.'.$file->getClientOriginalExtension();
            $file->move(storage_path('app/files'), $filename);

            $file_pointer = fopen(storage_path('app/files').'/'.$filename, 'r');

            $imported_data = [];
            $course = Subjects::find($request->course_id);
            $year = Helpers::instance()->getCurrentAccademicYear();
            $semester = $request->has('semester_id') ? Semester::find($request->semester_id) : Helpers::instance()->getSemester($request->class_id);

            
            while(($row = fgetcsv($file_pointer, 100, ',')) != null){
                if(is_numeric($row[1]))
                $imported_data[] = $row;
            }
            if(count($imported_data)==0){
                return back()->with('error', __('text.empty_or_wrong_data_format'));
            }

            $bad_results = 0;
            $null_students = '';

            foreach($imported_data as $data){
                $student = Students::where(['matric'=>$data[0]])->first() ?? null;
                if($student != null){
                    $base=[
                        'batch_id' => $year, 
                        'subject_id' => $request->course_id,
                        'student_id' => $student->id,
                        'class_id' => $request->class_id,
                        'semester_id' => $semester->id,
                        'coef'=>$course->_class_subject($request->class_id)->coef??$course->coef,
                        'class_subject_id'=>$course->_class_subject($request->class_id)->id??0
                    ];
                    if(OfflineResult::where($base)->whereNotNull('ca_score')->count() == 0){
                        OfflineResult::updateOrCreate($base, ['ca_score'=>$data[1], 'reference'=>$request->reference, 'user_id'=>auth()->id(), 'campus_id'=>$student->campus_id]);
                    }
                }else{
                    $null_students .= __('text.student_matric_not_found', ['matric'=>$data[0]]);
                }
            }
            if($bad_results > 1){
                return back()->with('message', __('text.word_done').'. ' .( $bad_results == 0 ? '' : $bad_results. ' '.__('text.records_not_imported_phrase')).$null_students);
            }
            return back()->with('success', __('text.word_done'));
        }else{
            return back()->with('error', __('text.file_type_constraint', ['type'=>'.csv']));

        }
        
    }
    
    public function exam_result(){
        $data['title'] = __('text.student_exam_results');
        return view('admin.result.exam_result', $data);
    }

    public function exam_fill(Request $request){

        $subject = Subjects::find(request('course_id'));
        $classSubject = $subject->_class_subject($request->class_id);
        $data['ca_total'] = Helpers::instance()->ca_total(request('class_id'));
        $data['exam_total'] = Helpers::instance()->exam_total(request('class_id'));
        $data['title'] = __('text.fill_exam_results_for', ['course'=>"{$subject->name} [ {$subject->code} ] | CV : ".($classSubject->coef ?? $subject->coef)." | ST : ".($classSubject->status ?? $subject->status), 'class'=>ProgramLevel::find(request('class_id'))->name()]);
        return view('admin.result.fill_exam', $data);
    }

    public function exam_import(Request $request){
        
        $subject = Subjects::find(request('course_id'));
        $classSubject = $subject->_class_subject($request->class_id);
        $data['title'] = __('text.import_exam_results_for', ['course'=>"{$subject->name} [ {$subject->code} ] | CV : ".($classSubject->coef ?? $subject->coef)." | ST : ".($classSubject->status ?? $subject->status), 'class'=>ProgramLevel::find(request('class_id'))->name()]);

        return view('admin.result.import_exam', $data);
    }

    public function exam_import_save(Request $request){
        $check = Validator::make($request->all(), [
            'reference'=>'required',
            'file'=>'required|file'
        ]);
        if($check->fails()){
            return back()->with('error', $check->errors()->first());
        }

        $ca_total = Helpers::instance()->ca_total(request('class_id'));
        $exam_total = Helpers::instance()->exam_total(request('class_id'));

        $file = $request->file('file');
        if($file != null &&$file->getClientOriginalExtension() == 'csv'){
            $filename = 'ca_'.random_int(1000, 9999).'_'.time().'.'.$file->getClientOriginalExtension();
            $file->move(storage_path('app/files'), $filename);


            $file_pointer = fopen(storage_path('app/files').'/'.$filename, 'r');

            $imported_data = [];
            $course = Subjects::find($request->course_id);
            $year = Helpers::instance()->getCurrentAccademicYear();
            $semester = $request->has('semester_id') ? Semester::find($request->semester_id) : Helpers::instance()->getSemester($request->class_id);

            
            while(($row = fgetcsv($file_pointer, 100, ',')) != null){
                if(is_numeric($row[1]))
                $imported_data[] = $row;
            }
            if(count($imported_data)==0){
                return back()->with('error', __('text.empty_or_wrong_data_format'));
            }

            $bad_results = 0;
            $null_students = '';
            $existing_results = '';

            foreach($imported_data as $data){
                
                $student = Students::where(['matric'=>$data[0]])->first() ?? null;
                if($student != null){
                    $base=[
                        'batch_id' => $year, 
                        'subject_id' => $request->course_id,
                        'student_id' => $student->id,
                        'class_id' => $request->class_id,
                        'semester_id' => $semester->id,
                        'coef'=>$course->_class_subject($request->class_id)->coef??$course->coef,
                        'class_subject_id'=>$course->_class_subject($request->class_id)->id??0
                    ];
                    if(Result::where($base)->whereNotNull('ca_score')->count()>0){
                        $existing_results .= "<br> ".__('text.ca_results_already_exist_for', ['item'=>$data[0]]);
                    }elseif ($data[1] != null) {
                        # code...
                        Result::updateOrCreate($base, ['ca_score'=>$data[1], 'reference'=>$request->reference, 'user_id'=>auth()->id(),  'campus_id'=>$student->campus_id]);
                    }
                    if(Result::where($base)->whereNotNull('exam_score')->count()>0){
                        $existing_results .= "<br> ".__('text.exam_results_already_exist_for', ['item'=>$data[0]]);
                    }elseif (!$data[2] == null) {
                        # code...
                        Result::updateOrCreate($base, ['exam_score'=>$data[2], 'reference'=>$request->reference, 'user_id'=>auth()->id(),  'campus_id'=>$student->campus_id]);
                    }
                }
                else{
                    $null_students .= __('text.student_matric_not_found', ['matric'=>$data[0]])." <br>";
                }
            }
            if($bad_results > 1){
                return back()->with('message', __('text.word_done').'. ' . ($bad_results == 0 ? '' : $bad_results . ' '.__('text.records_not_imported_phrase')) . $null_students . $existing_results);
            }
            return back()->with('success', __('text.word_done'));
        }else{
            return back()->with('error', __('text.file_type_constraint', ['type'=>'.csv']));

        }
    }

    public function imports_index()
    {
        # code...
        $data['title'] = __('text.result_imports');

        return view('admin.result.imports_index', $data);
    }

    public function individual_results()
    {
        $data['title'] = __('text.individual_results');
        return view('admin.result.individual_result', $data);
    }

    public function class_results(Request $request)
    {
        $data['title'] = __('text.class_results');
        if ($request->has('class_id')) {
            # code...
            $results = OfflineResult::where(['batch_id' => $request->year_id, 'class_id' => $request->class_id, 'semester_id' => $request->semester_id]);
            $data['results'] = $results->get();
            $data['students'] = Students::whereIn('id', $results->distinct()->pluck('student_id')->toArray())->orderBy('matric', 'ASC')->get();
            $data['class'] = ProgramLevel::find($request->class_id);
            $data['year'] = Batch::find($request->year_id);
            $data['semester'] = Semester::find($request->semester);
            $data['ca_total'] = $data['class']->program()->first()->ca_total;
            $data['exam_total'] = $data['class']->program()->first()->exam_total;
            $data['grading'] = $data['class']->program()->first()->gradingType->grading()->get() ?? [];
            
            // dd($data);
            // show public health template to a public health
            if ($data['class']->program->background->background_name == 'PUBLIC HEALTH') {
                # code...
                return view('admin.result.public_health_class_result', $data);
            }
            return view('admin.result.class_result', $data);
        } else {
            # code...
            return view('admin.res_and_trans.index', $data);
        }
        

    }

    public function individual_instances(Request $request)
    {
        // $request->validate(['searchValue'=>'required']);

        // return $request->searchValue;
        try {
            //code...
            $instances = Students::where(function ($blda) use ($request) {
                $blda->where('name', 'like', "%{$request->searchValue}%")
                    ->orWhere('matric', 'like', "%{$request->searchValue}%");
            })
                ->join('student_classes', ['student_classes.student_id' => 'students.id'])
                ->select(['student_classes.id', 'student_classes.year_id', 'student_classes.class_id', 'students.name', 'students.id as student_id', 'students.matric'])->take(30)->get();
    
            return \response()->json(ResultResource::collection($instances));

            
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function print_individual_results(Request $request)
    {
        # code...
        $student = Students::find($request->student_id);
        $year = $request->year ?? Helpers::instance()->getCurrentAccademicYear();
        $class = $student->_class(Helpers::instance()->getCurrentAccademicYear());
        $semester = $request->semester ? 
            Semester::find($request->semester) : 
            Helpers::instance()->getSemester($class->id);
        $class = $student->_class($year);
        $data['title'] = __('text.my_exam_results');
        $data['user'] = $student;
        $data['semester'] = $semester;
        $data['ca_total'] = $class->program()->first()->ca_total;
        $data['exam_total'] = $class->program()->first()->exam_total;
        $data['grading'] = $class->program()->first()->gradingType->grading()->get() ?? [];
        $res = $student->result()->where('results.batch_id', '=', $year)->where('results.semester_id', $semester->id)->distinct()->pluck('subject_id')->toArray();

        $_registered_courses = $data['user']->registered_courses($year)->where('semester_id', $semester->id)->pluck('course_id')->unique()->toArray();
        $registered_courses = count($_registered_courses) > 0 ? $_registered_courses : $data['user']->registered_courses($year->id)->whereNotNull('resit_id')->pluck('course_id')->unique()->toArray();
      
        $data['subjects'] = $student->_class(Helpers::instance()->getYear())->subjects()->whereIn('subjects.id', $res)->get();
        $data['results'] = array_map(function($subject_id)use($data, $year, $semester, $student){
            $ca_mark = $student->result()->where('results.batch_id', '=', $year)->where('results.subject_id', '=', $subject_id)->where('results.semester_id', '=', $semester->id)->first()->ca_score ?? 0;
            $exam_mark = $student->result()->where('results.batch_id', '=', $year)->where('results.subject_id', '=', $subject_id)->where('results.semester_id', '=', $semester->id)->first()->exam_score ?? 0;
            $total = $ca_mark + $exam_mark;
            foreach ($data['grading'] as $key => $value) {
                # code...
                if ($total >= $value->lower && $total <= $value->upper) {
                    # code...
                    $grade = $value;
                    return [
                        'id'=>$subject_id,
                        'code'=>Subjects::find($subject_id)->code ?? '',
                        'name'=>Subjects::find($subject_id)->name ?? '',
                        'status'=>Subjects::find($subject_id)->status ?? '',
                        'coef'=>Subjects::find($subject_id)->coef ?? '',
                        'ca_mark'=>$ca_mark,
                        'exam_mark'=>$exam_mark,
                        'total'=>$total,
                        'grade'=>$grade->grade,
                        'remark'=>$grade->remark
                    ];
                }
            }
            
            // dd($grade);
        }, $registered_courses);

        $fee = [
            'total_debt'=>$student->total_debts($year),
            'total_paid'=>$student->total_paid($year),
            'total' => $student->total($year),
            'fraction' => $semester->semester_min_fee
        ];
        // TOTAL PAID - TOTAL DEBTS FOR THIS YEAR = AMOUNT PAID FOR THIS YEAR
        $data['min_fee'] = $fee['total']*$fee['fraction'];
        $data['access'] = $fee['total_paid']-$fee['total_debt'] >= $data['min_fee'] || $student->classes()->where(['year_id'=>$year, 'result_bypass_semester'=>$semester->id, 'bypass_result'=>1])->count() > 0;
        // dd($fee);

        // show public health template to a public health
        if ($class->program->background->background_name == 'PUBLIC HEALTH') {
            # code...
            return view('admin.result.public_health_individual_result_print', $data);
        }
        return view('admin.result.individual_result_print')->with($data);
    }

    public function result_publishing (Request $request)
    {
        # code...
        $year = $request->year ?? $this->current_accademic_year;
        $data['title'] = __('text.publish_results').' - '.Batch::find($year)->name;
        return view('admin.result.publish', $data);
    }

    public function publish_results(Request $request)
    {
        # code...
        $results = Result::where(['batch_id'=>$request->year, 'campus_id'=>auth()->user()->campus_id, 'semester_id'=>$request->semester]);
        if($results->count() == 0){return back()->with('error', 'Results not yet uploaded');}
        $results->update(['published'=>1]);
        return back()->with('success', __('text.word_done'));
    }
    
    public function unpublish_results(Request $request)
    {
        # code...
        $results = Result::where(['batch_id'=>$request->year, 'campus_id'=>auth()->user()->campus_id, 'semester_id'=>$request->semester]);
        if($results->count() == 0){return back()->with('error', 'Results not yet uploaded');}
        Result::where(['batch_id'=>$request->year, 'semester_id'=>$request->semester])->update(['published'=>0]);
        return back()->with('success', __('text.word_done'));
    }


    public function store_results(Request $request)
    {
        # code...
        $validity = Validator::make($request->all(), [
            'student'=>'required', 'semester_id'=>'required', 'subject'=>'required', 'year'=>'required',
            'class_id'=>'required', 'coef'=>'required', 'ca_score'=>'required'
        ]);

        if($validity->fails()){
            return response(['message'=>'Validation error. '.$validity->errors()->first()]);
        }

        try{
            
            $totalMark = ($request->ca_score??0) + ($request->exam_score??0);
            $grading = Grading::where('lower', '<=', $totalMark)->where('upper', '>=', $totalMark)->first();
            $student = Students::find($request->student);
            $data = [
                'batch_id'=>$request->year, 'student_id'=>$request->student, 'class_id'=>$request->class_id, 'semester_id'=>$request->semester_id, 
                'subject_id'=>$request->subject, 'ca_score'=>$request->ca_score, 'exam_score'=>$request->exam_score, 'coef'=>$request->coef, 'remark'=>$grading->remark??'FAIL',
                'class_subject_id'=>$request->class_subject_id, 'reference'=>'REF'.$request->year.$request->student.$request->class_id.$request->semester_id.$request->subject_id.$request->coef, 
                'user_id'=>auth()->id(), 'campus_id'=>$student->campus_id, 'published'=>0
            ];
            $base = ['batch_id'=>$request->year, 'student_id'=>$request->student, 'class_id'=>$request->class_id, 'semester_id'=>$request->semester_id, 
            'subject_id'=>$request->subject];
    
            Result::updateOrInsert($base, $data);
            return response(['message'=>'saved successfully']);
        }catch(\Throwable $th){
            return response(['message'=>$th->getMessage()], 500);
        }
    }


    public function import_special_ca(Request $request, $year=null, $semester=null, $course_code=null){
        $data['title'] = "Special CA importation";
        $data['year_id'] = $year;
        $data['semester_id'] = $semester;
        $data['course_code'] = $course_code;
        $data['semesters'] = Semester::where('is_main_semester', 1)->distinct()->get();
        if($course_code != null){
            $sem = Semester::find($data['semester_id']);
            $batch = Batch::find($data['year_id']);
            $subject = Subjects::where('code', $course_code)->first();
            $data['semester'] = $sem;
            $data['year'] = $batch;
            $data['course'] = $subject;
            $data['title2'] = __('text.uploading_ca_marks_for', ['ccode'=>$course_code, 'year'=>$batch->name]);
            $data['_title2'] = __('text.word_course').' :: <b class="text-danger">'.$subject->name.'</b> || '.__('text.course_code').' :: <b class="text-danger">'. $course_code .'</b> || '.__('text.word_semester').' :: <b class="text-danger">'. $sem->name .'</b>';
            $data['delete_label'] = __('text.clear_ca_for', ['year'=>$batch->name??'YR', 'ccode'=>$course_code??'CCODE', 'semester'=>$sem->name??'SEMESTER']);
            $data['results'] = Result::where(['batch_id'=>$year, 'semester_id'=>$semester, 'subject_id'=>$subject->id??null])->get();
            $data['can_update_ca'] = !(now()->isAfter($sem->ca_upload_latest_date??now()->addDays()->toString()));;
            $data['delete_prompt'] = "You are about to delete all the CA marks for {$subject->code}, {$sem->name} {$batch->name}";
        }
        // dd($data);
        return view('admin.result.special.ca', $data);
    }


    public function import_save_special_ca(Request $request, $year, $semester, $course_code){
        if($request->file('file') == null){
            session()->flash('error', 'file field requried');
            return back()->withInput();
        }

        // save the file
        $file = $request->file('file');
        $fname = "ca_upload_".time().'.csv';
        $path = public_path('uploads/files');
        $file->move($path, $fname);

        // open file for reading
        $reading_stream = fopen($path.'/'.$fname, 'r');

        // read file data into an array
        $file_data = [];
        while(($row = fgetcsv($reading_stream, 1000)) != null){
            $file_data[] = ['matric'=>$row[0], 'ca_score'=>$row[1]];
        }
        fclose($reading_stream);

        // write CA results to database
        $missing_students = "";
        $subject = Subjects::where('code', $course_code)->first();
        $sem = Semester::find($semester);
        if($subject == null){
            return back()->withInput()->with('error', "Course with course code {$course_code} not found");
        }
        foreach($file_data as $rec){
            $student = Students::where('matric', $rec['matric'])->first();
            
            if($student == null){
                $missing_students .= " ".$rec['matric'];
                continue;
            }
            $class = $student->a_class($year);
            $class_subject = $class->class_subjects()->where('subject_id', $subject->id)->first();
            $data = [
                'batch_id'=>$year, 'student_id'=>$student->id, 'class_id'=>$class->id, 'semester_id'=>$sem->sem, 
                'subject_id'=>$subject->id, 'ca_score'=>$rec['ca_score'], 'coef'=>$class_subject->coef ?? $subject->coef,
                'class_subject_id'=>$class_subject->id??null, 'reference'=>'REF'.$year.$student->id.$class->id.$semester.$subject->id.$subject->coef, 
                'user_id'=>auth()->id(), 'campus_id'=>$student->campus_id, 'published'=>0
            ];
            $base = ['batch_id'=>$year, 'student_id'=>$student->id, 'class_id'=>$class->id, 'semester_id'=>$semester, 
            'subject_id'=>$subject->id];
    
            Result::updateOrInsert($base, $data);
        }
        if(strlen($missing_students) > 0){
            return back()->with('success', 'Done')->with('message', "Students with matricules {$missing_students} are not found");
        }
        return back()->with('success', 'Done');

    }


    public function clear_special_ca(Request $request, $year, $semester, $course_code){
        $subject = Subjects::where('code', $course_code)->first();
        if($subject != null){
            Result::where(['batch_id'=>$year, 'semester_id'=>$semester, 'subject_id'=>$subject->id])->each(function($row){
                $row->exam_score == null ? $row->delete() : $row->update(['ca_score'=>null]);
            });
            return back()->with('sucess', 'Done');
        }else{
            return back()->with('error', "Course not found");
        }
    }


    public function import_special_exam(Request $request, $year=null, $semester=null, $course_code=null){
        $data['title'] = "Special Exam importation";
        $data['year_id'] = $year;
        $data['semester_id'] = $semester;
        $data['course_code'] = $course_code;
        $data['semesters'] = Semester::where('is_main_semester', 1)->distinct()->get();
        if($course_code != null){
            $sem = Semester::find($data['semester_id']);
            $batch = Batch::find($data['year_id']);
            $subject = Subjects::where('code', $course_code)->first();
            $data['semester'] = $sem;
            $data['year'] = $batch;
            $data['course'] = $subject;
            $data['title2'] = __('text.uploading_exam_marks_for', ['ccode'=>$course_code, 'year'=>$batch->name]);
            $data['_title2'] = __('text.word_course').' :: <b class="text-danger">'.$subject->name.'</b> || '.__('text.course_code').' :: <b class="text-danger">'. $course_code .'</b> || '.__('text.word_semester').' :: <b class="text-danger">'. $sem->name .'</b>';
            $data['delete_label'] = __('text.clear_exam_for', ['year'=>$batch->name??'YR', 'ccode'=>$course_code??'CCODE', 'semester'=>$sem->name??'SEMESTER']);
            $data['results'] = Result::where(['batch_id'=>$year, 'semester_id'=>$semester, 'subject_id'=>$subject->id??null])->get();
            $data['can_update_exam'] = !(now()->isAfter($sem->exam_upload_latest_date??now()->addDays()->toString()));;
            // $data['can_update_exam'] = $data['results']->where('ca_score', '>', 0)->count() > 0;
            $data['delete_prompt'] = "You are about to delete all the entire results for {$subject->code}, {$sem->name} {$batch->name}";
        }
        // dd($data);
        return view('admin.result.special.exam', $data);
    }


    public function import_save_special_exam(Request $request, $year, $semester, $course_code){
        if($request->file('file') == null){
            session()->flash('error', 'file field requried');
            return back()->withInput();
        }

        // save the file
        $file = $request->file('file');
        $fname = "exam_upload_".time().'.csv';
        $path = public_path('uploads/files');
        $file->move($path, $fname);

        // open file for reading
        $reading_stream = fopen($path.'/'.$fname, 'r');

        // read file data into an array
        $file_data = [];
        while(($row = fgetcsv($reading_stream, 1000)) != null){
            $file_data[] = ['matric'=>$row[0], 'exam_score'=>$row[1]];
        }
        fclose($reading_stream);

        // write CA results to database
        $missing_students = "";
        $subject = Subjects::where('code', $course_code)->first();
        $sem = Semester::find($semester);
        if($subject == null){
            return back()->withInput()->with('error', "Course with course code {$course_code} not found");
        }
        foreach($file_data as $rec){
            $student = Students::where('matric', $rec['matric'])->first();
            
            if($student == null){
                $missing_students .= " ".$rec['matric'];
                continue;
            }
            $class = $student->a_class($year);
            $class_subject = $class->class_subjects()->where('subject_id', $subject->id)->first();
            $data = [
                'batch_id'=>$year, 'student_id'=>$student->id, 'class_id'=>$class->id, 'semester_id'=>$sem->sem, 
                'subject_id'=>$subject->id, 'exam_score'=>$rec['exam_score'], 'coef'=>$class_subject->coef ?? $subject->coef,
                'class_subject_id'=>$class_subject->id??null, 'reference'=>'REF'.$year.$student->id.$class->id.$semester.$subject->id.$subject->coef, 
                'user_id'=>auth()->id(), 'campus_id'=>$student->campus_id, 'published'=>0
            ];
            $base = ['batch_id'=>$year, 'student_id'=>$student->id, 'class_id'=>$class->id, 'semester_id'=>$semester, 
            'subject_id'=>$subject->id];
    
            Result::updateOrInsert($base, $data);
        }
        if(strlen($missing_students) > 0){
            return back()->with('success', 'Done')->with('message', "Students with matricules {$missing_students} are not found");
        }
        return back()->with('success', 'Done');
    }
    

    public function clear_special_exam(Request $request, $year, $semester, $course_code){
        $subject = Subjects::where('code', $course_code)->first();
        if($subject != null){
            Result::where(['batch_id'=>$year, 'semester_id'=>$semester, 'subject_id'=>$subject->id])->each(function($row){
                ($row->ca_score == null) ? $row->delete() : $row->update(['exam_score'=>null]);
            });
            return back()->with('sucess', 'Done');
        }else{
            return back()->with('error', "Course not found");
        }
    }


    public function migrate_results(Request $request, $year=null, $semester=null, $class=null, $course_code=null)
    {
        # code...
        $data['title'] = "Migrate Results";
        $data['year_id'] = $year;
        $data['semester_id'] = $semester;
        $data['class_id'] = $class;
        $data['course_code'] = $course_code;
        $data['semesters'] = Semester::where('is_main_semester', 1)->distinct()->get();
        $data['classes'] = HomeController::sorted_program_levels();
        if($course_code != null){
            $sem = Semester::find($data['semester_id']);
            $batch = Batch::find($data['year_id']);
            $subject = Subjects::where('code', $course_code)->first();
            $program_level = ProgramLevel::find($class);
            $data['semester'] = $sem;
            $data['year'] = $batch;
            $data['course'] = $subject;
            $data['class'] = $program_level;
            $data['title2'] = __('text.migrating_results_for', ['class'=>$program_level->name(), 'ccode'=>$course_code, 'year'=>$batch->name, 'sem'=>$sem->name]);
            $data['_title2'] = __('text.word_course').' :: <b class="text-danger">'.$subject->name.'</b> || '.__('text.course_code').' :: <b class="text-danger">'. $course_code .'</b> || '.__('text.word_class').' :: <b class="text-danger">'. $program_level->name() .'</b>'.__('text.word_semester').' :: <b class="text-danger">'. $sem->name .'</b>';
            $data['delete_label'] = __('text.clear_results_for', ['year'=>$batch->name??'YR', 'class'=>$program_level->name(), 'ccode'=>$course_code??'CCODE', 'semester'=>$sem->name??'SEMESTER']);
            $data['results'] = Result::where(['batch_id'=>$year, 'class_id'=>$class, 'semester_id'=>$semester, 'subject_id'=>$subject->id??null])->get();
            $data['can_update_exam'] = !(now()->isAfter($sem->exam_upload_latest_date??now()->addDays()->toString()));
            $data['delete_prompt'] = "You are about to delete all {$program_level->name()} results for {$subject->code}, {$sem->name} {$batch->name}";
        }
        // dd($data);
        return view('admin.result.special.migrate', $data);
    }


    public function migrate_save_results(Request $request, $year=null, $semester=null, $class_id, $course_code=null)
    {
        # code...
        if($request->file('file') == null){
            session()->flash('error', 'file field requried');
            return back()->withInput();
        }

        // save the file
        $file = $request->file('file');
        $fname = "exam_upload_".time().'.csv';
        $path = public_path('uploads/files');
        $file->move($path, $fname);

        // open file for reading
        $reading_stream = fopen($path.'/'.$fname, 'r');

        // read file data into an array
        $file_data = [];
        while(($row = fgetcsv($reading_stream, 1000)) != null){
            $file_data[] = ['matric'=>$row[0], 'ca_score'=>$row[1], 'exam_score'=>$row[2]];
        }
        fclose($reading_stream);

        // write CA results to database
        $missing_students = "";
        $subject = Subjects::where('code', $course_code)->first();
        if($subject == null){
            return back()->withInput()->with('error', "Course with course code {$course_code} not found");
        }

        $class = ProgramLevel::find($class_id);
        $_sem = Semester::find($semester);
        foreach($file_data as $rec){
            $student = Students::where('matric', $rec['matric'])->first();
            
            if($student == null){
                $missing_students .= " ".$rec['matric'];
                continue;
            }
            $class_subject = $class->class_subjects()->where('subject_id', $subject->id)->first();
            $data = [
                'batch_id'=>$year, 'student_id'=>$student->id, 'semester_id'=>$_sem->sem, 'subject_id'=>$subject->id, 
                'ca_score'=>$rec['ca_score'], 'exam_score'=>$rec['exam_score'], 'coef'=>$class_subject->coef ?? $subject->coef,
                'class_subject_id'=>$class_subject->id??null, 'reference'=>'REF'.$year.$student->id.$class->id.$semester.$subject->id.$subject->coef, 
                'user_id'=>auth()->id(), 'campus_id'=>$student->campus_id, 'published'=>0
            ];
            $base = ['batch_id'=>$year, 'student_id'=>$student->id, 'class_id'=>$class_id, 'semester_id'=>$semester, 
            'subject_id'=>$subject->id];
    
            Result::updateOrInsert($base, $data);
        }
        if(strlen($missing_students) > 0){
            return back()->with('success', 'Done')->with('message', "Students with matricules {$missing_students} are not found");
        }
        return back()->with('success', 'Done');
    }


    public function migrate_clear_results(Request $request, $year, $semester, $class, $course_code)
    {
        # code...
        // dd(23);
        $subject = Subjects::where('code', $course_code)->first();
        if($subject != null){
            Result::where(['batch_id'=>$year, 'semester_id'=>$semester, 'subject_id'=>$subject->id, 'class_id'=>$class])->each(function($row){
                $row->delete();
            });
            return back()->with('sucess', 'Done');
        }else{
            return back()->with('error', "Course not found");
        }
    }


    public function get_record($student_id, $year_id, $semester_id, $course_id){
        return Result::where(['batch_id'=>$year_id, 'student_id'=>$student_id, 'semester_id'=>$semester_id, 'subject_id'=>$course_id])->first();
    }

    public function ca_upload_report(Request $request, $year=null, $semester=null, $pl=null){
        $data['title'] = "CA Upload Record";
        $data['year_id'] = $year; $data['semester_id'] = $semester; $data['class_id'] = $pl;
        $data['years'] = Batch::all();
        $data['semesters'] = Semester::all();
        $data['classes'] = \App\Http\Controllers\Controller::sorted_program_levels();
        if($pl != null){
            $data['year'] = Batch::find($year);
            $data['semester'] = Semester::find($semester);
            $data['class'] = ProgramLevel::find($pl);
            if($data['semester'] != null && $data['class'] != null){
                $data['title'] = "CA Upload Record For ".($data['class'] == null ? "" :$data['class']->name()).", ".($data['semester']->name??'')." ".($data['year']->name??'');
                $uploaded = Result::where(['batch_id'=>$year, 'semester_id'=>$semester, 'class_id'=>$pl])->whereNotNull('ca_score')->orderBy('subject_id')->distinct()->pluck('subject_id')->toArray();
                $data['record'] = ProgramLevel::find($pl)->subjects()->where('semester_id', $semester)->get()->map(function($rec)use($uploaded){
                    $rec->_status = in_array($rec->id, $uploaded) ? 1: 0;
                    return $rec;
                });
            }
        }
        return view('admin.result.ca_upload_record', $data);
    }

    public function exam_upload_report(Request $request, $year=null, $semester=null, $pl=null){
        $data['title'] = "Exam Upload Record";
        $data['year_id'] = $year; $data['semester_id'] = $semester; $data['class_id'] = $pl;
        $data['years'] = Batch::all();
        $data['semesters'] = Semester::all();
        $data['classes'] = \App\Http\Controllers\Controller::sorted_program_levels();
        if($pl != null){
            $data['year'] = Batch::find($year);
            $data['semester'] = Semester::find($semester);
            $data['class'] = ProgramLevel::find($pl);
            if($data['semester'] != null && $data['class'] != null){
                $data['title'] = "Exam Upload Record For ".($data['class'] == null ? "" :$data['class']->name()).", ".($data['semester']->name??'')." ".$data['year']->name??'';
                $uploaded = Result::where(['batch_id'=>$year, 'semester_id'=>$semester, 'class_id'=>$pl])->whereNotNull('exam_score')->orderBy('subject_id')->distinct()->pluck('subject_id')->toArray();
                $data['record'] = ProgramLevel::find($pl)->subjects()->where('semester_id', $semester)->get()->map(function($rec)use($uploaded){
                    $rec->_status = in_array($rec->id, $uploaded) ? 1: 0;
                    return $rec;
                });
            }
        }
        return view('admin.result.exam_upload_record', $data);
    }

    public function super_migrate_results(Request $request, $year=null, $class=null){
        $data['title'] = "Result Super-Migrator Terminal";
        $data['year_id'] = $year;
        $data['class_id'] = $class;
        $data['semesters'] = Semester::where('is_main_semester', 1)->distinct()->get();
        $data['classes'] = HomeController::sorted_program_levels();
        $data['years'] = Batch::all();
        if($class != null){
            $batch = Batch::find($data['year_id']);
            $program_level = ProgramLevel::find($class);
            $data['year'] = $batch;
            $data['class'] = $program_level;
            $data['title2'] = "Result Super-Migrator Terminal :: ".($data['class']->name()).", ".$data['year']->name??'';
            $data['_title2'] = __('text.word_class').' :: <b class="text-danger">'. $program_level->name() .'</b>';
            $data['delete_label'] = __('text.clear_results_for', ['year'=>$batch->name??'YR', 'class'=>$program_level->name()]);
            $data['results'] = Result::where(['batch_id'=>$year, 'class_id'=>$class])->get();
            $data['can_update_exam'] = true;
            $data['delete_prompt'] = "You are about to delete all {$program_level->name()} results for {$batch->name}";
        }
        return view('admin.result.super.migrate', $data);
    }

    public function super_migrate_save_results(Request $request, $year=null, $class=null){
        if($request->file('file') == null){
            session()->flash('error', 'file field requried');
            return back()->withInput();
        }

        // save the file
        $file = $request->file('file');
        $fname = "exam_upload_".time().'.csv';
        $path = public_path('uploads/files');
        $file->move($path, $fname);

        // open file for reading
        $reading_stream = fopen($path.'/'.$fname, 'r');

        // read file data into an array
        $file_data = [];
        while(($row = fgetcsv($reading_stream, 1000)) != null){
            $file_data[] = ['semester'=>$row[0], 'ccode'=>str_replace(' ', '', $row[1]), 'matric'=>$row[2], 'ca_score'=>$row[3], 'exam_score'=>$row[4]];
        }
        fclose($reading_stream);
        unlink($path.'/'.$fname);

        // write CA results to database
        $missing_students = "";
        $missing_courses = "";

        $_class = ProgramLevel::find($class);
        foreach($file_data as $rec){
            $_sem = Semester::find($rec['semester']);
            $student = Students::where('matric', $rec['matric'])->first();
            $subject = Subjects::where('code', $rec['ccode'])->first();
            
            if($student == null){
                $missing_students .= " ".$rec['matric'];
                continue;
            }
            if($subject == null){
                $missing_courses .= " ".$rec['ccode'];
                continue;
            }
            $class_subject = $_class->class_subjects()->where('subject_id', $subject->id)->first();
            $data = [
                'batch_id'=>$year, 'student_id'=>$student->id, 'semester_id'=>$_sem->sem, 'subject_id'=>$subject->id, 
                'ca_score'=>$rec['ca_score'], 'exam_score'=>$rec['exam_score'], 'coef'=>$class_subject->coef ?? $subject->coef,
                'class_subject_id'=>$class_subject->id??null, 'reference'=>'REF'.$year.$student->id.$class.$rec['semester'].$subject->id.$subject->coef, 
                'user_id'=>auth()->id(), 'campus_id'=>$student->campus_id, 'published'=>0
            ];
            $base = ['batch_id'=>$year, 'student_id'=>$student->id, 'class_id'=>$class, 'semester_id'=>$rec['semester'], 
            'subject_id'=>$subject->id];
    
            Result::updateOrInsert($base, $data);
        }
        if(strlen($missing_students) > 0 || strlen($missing_courses) > 0){
            return back()->with('success', 'Done')->with('message', (strlen($missing_students) == 0 ? '' : "Students with matricules {$missing_students} are not found").(strlen($missing_courses) == 0 ? '' : ", Courses with codes {$missing_courses} are not found"));
        }
        return back()->with('success', 'Done');
    }

    public function super_clear_results(Request $request, $year=null, $class=null){
        if(Result::where(['batch_id'=>$year, 'class_id'=>$class])->count() == 0){
            session()->flash('error', "No results are found for the set CLASS, SEMESTER, YEAR");
        }
        Result::where(['batch_id'=>$year, 'class_id'=>$class])->each(function($rec){$rec->delete();});
        return back()->with('success', 'Done');
    }
}
