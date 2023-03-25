<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Resources\ResultResource;
use App\Models\Batch;
use App\Models\ClassSubject;
use App\Models\Config;
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

    public function ca_fill(){
        // check if CA total is set forthis program
        if (!Helpers::instance()->ca_total_isset(request('class_id'))) {
            # code...
            return back()->with('error', __('text.CA_total_not_set_for', ['program'=>__('text.word_program')]));
        }

        $subject = Subjects::find(request('course_id'));
        $data['ca_total'] = Helpers::instance()->ca_total(request('class_id'));
        $data['title'] = __('text.fill_CA_results_for', ['course'=>'[ '.$subject->code." ] ".$subject->name, 'class'=>ProgramLevel::find(request('class_id'))->name()]);
        return view('admin.result.fill_ca', $data);
    }

    public function ca_import(){
        // check if CA total is set forthis program
        if (!Helpers::instance()->ca_total_isset(request('class_id'))) {
            # code...
            return back()->with('error',  __('text.CA_total_not_set_for', ['program'=>__('text.word_program')]));
        }

        $subject = Subjects::find(request('course_id'));
        $data['title'] = __('text.import_CA_results_for', ['course'=>"[ ".$subject->code." ] ".$subject->name, 'class'=>ProgramLevel::find(request('class_id'))->name()]);
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
                $imported_data[] = [$row[0], $row[1]];
            }
            if(count($imported_data)==0){
                return back()->with('error', __('text.empty_or_wrong_data_format'));
            }

            $bad_results = 0;
            $null_students = '';
            foreach($imported_data as $data){
                if ($data[1] > $ca_total) {
                    # code...
                    $bad_results++;
                    continue;
                }
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

    public function exam_fill(){
        // check if exam total is set for this program
        if (!Helpers::instance()->exam_total_isset(request('class_id')) || !Helpers::instance()->ca_total_isset(request('class_id'))) {
            # code...
            return back()->with('error', __('text.exam_total_not_set_for', ['program'=>__('text.word_program')]));
        }

        $subject = Subjects::find(request('course_id'));
        $data['ca_total'] = Helpers::instance()->ca_total(request('class_id'));
        $data['exam_total'] = Helpers::instance()->exam_total(request('class_id'));
        $data['title'] = __('text.fill_exam_results_for', ['course'=>"[ ".$subject->code." ] ".$subject->name, 'class'=>ProgramLevel::find(request('class_id'))->name()]);
        return view('admin.result.fill_exam', $data);
    }
    
    public function exam_import(){
        // check if exam total is set for this program
        if (!Helpers::instance()->exam_total_isset(request('class_id'))) {
            # code...
            return back()->with('error',  __('text.exam_total_not_set_for', ['program'=>__('text.word_program')]));
        }
        
        $subject = Subjects::find(request('course_id'));
        $data['title'] = __('text.import_exam_results_for', ['course'=>"[ ".$subject->code." ] ".$subject->name, 'class'=>ProgramLevel::find(request('class_id'))->name()]);
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
                $imported_data[] = [$row[0], $row[1], $row[2]];
            }
            if(count($imported_data)==0){
                return back()->with('error', __('text.empty_or_wrong_data_format'));
            }

            $bad_results = 0;
            $null_students = '';
            $existing_results = '';
            foreach($imported_data as $data){
                if ($data[1] > $ca_total || $data[2] > $exam_total) {
                    # code...
                    $bad_results++;
                    continue;
                }
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
                    if(OfflineResult::where($base)->whereNotNull('ca_score')->count()>0){
                        $existing_results .= "<br> ".__('text.ca_results_already_exist_for', ['item'=>$data[0]]);
                    }elseif (!$data[1] == null) {
                        # code...
                        OfflineResult::updateOrCreate($base, ['ca_score'=>$data[1], 'reference'=>$request->reference, 'user_id'=>auth()->id(),  'campus_id'=>$student->campus_id]);
                    }
                    if(OfflineResult::where($base)->whereNotNull('exam_score')->count()>0){
                        $existing_results .= "<br> ".__('text.exam_results_already_exist_for', ['item'=>$data[0]]);
                    }elseif (!$data[2] == null) {
                        # code...
                        OfflineResult::updateOrCreate($base, ['exam_score'=>$data[1], 'reference'=>$request->reference, 'user_id'=>auth()->id(),  'campus_id'=>$student->campus_id]);
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
            })->join('student_classes', ['student_classes.student_id' => 'students.id'])
                ->get(['student_classes.id', 'student_classes.year_id', 'student_classes.class_id', 'students.name', 'students.id as student_id', 'students.matric']);
    
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
        $semester = $request->semester ? Semester::find($request->semester) : Helpers::instance()->getSemester($student->_class(Helpers::instance()->getCurrentAccademicYear())->id);
        $class = $student->_class($year);
        $data['title'] = __('text.my_exam_results');
        $data['user'] = $student;
        $data['semester'] = $semester;
        $data['ca_total'] = $class->program()->first()->ca_total;
        $data['exam_total'] = $class->program()->first()->exam_total;
        $data['grading'] = $class->program()->first()->gradingType->grading()->get() ?? [];
        $res = $student->result()->where('results.batch_id', '=', $year)->where('results.semester_id', $semester->id)->distinct()->pluck('subject_id')->toArray();
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
        }, $res);

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

    public function date_line(Request $request)
    {
        # code...
        $data['title'] = __('text.set_result_submission_dateline_for', ['item'=>$request->has('semester') ? Semester::find($request->semester)->name : '']);
        if(request()->has('background')){
            $data['current_semester'] = Semester::where(['background_id'=>$request->background, 'status'=>1])->first()->id ?? null;
        }
        return view('admin.setting.results_date_line', $data);
    }
    public function date_line_save(Request $request)
    {
        # code...
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
}
