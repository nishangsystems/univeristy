<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\ClassSubject;
use App\Models\Config;
use App\Models\ProgramLevel;
use App\Models\Result;
use App\Models\Sequence;
use App\Models\StudentClass;
use App\Models\Students;
use App\Models\Subjects;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Redirect;
use Session;

class ResultController extends Controller
{

    public function index(Request $request)
    {
        $data['releases'] = \App\Models\Config::orderBy('id', 'desc')->get();
        $data['title'] = "All result releases";
        return view('admin.setting.result.index')->with($data);
    }

    public function create(Request $request)
    {
        $data['title'] = "Add Release";
        return view('admin.setting.result.create')->with($data);
    }

    public function edit(Request $request, $id)
    {
        $data['title'] = "Edit result releases";
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
        return redirect()->to(route('admin.result_release.index'))->with('success', "Release created successfully");
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
        return redirect()->to(route('admin.result_release.index'))->with('success', "Release updated successfully");
    }

    public function destroy(Request $request, $id)
    {
        $config = Config::find($id);
        if (\App\Models\Config::all()->count() > 0) {
            $config->delete();
            return redirect()->back()->with('success', "Release deleted successfully");
        } else {
            return redirect()->back()->with('error', "Change current academic year");
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

            \DB::beginTransaction();
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

                \DB::commit();
            } catch (\Exception $e) {
                \DB::rollback();
                echo ($e->getMessage());
            }
            Session::flash('message', 'Import Successful.');
            //echo("<h3 style='color:#0000ff;'>Import Successful.</h3>");

        } else {
            Session::flash('message', 'Invalid File Extension.');
        }
        return redirect()->back()->with('success', 'Result Imported successfully!');
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
        $data['title'] = "Student Results";
        return view('admin.result.report', $data);
    }

    public function report_show(Request $request)
    {
        return $request->all();
        # code...
    }


    
    
    // ADDITIONAL RESULT METHODS FROM OFFLINE APP
    
    public function ca_result(){
        $data['title'] = "Student CA Results";
        return view('admin.result.ca_result', $data);
    }

    public function ca_fill(){
        // check if CA total is set forthis program
        if (!Helpers::instance()->ca_total_isset(request('class_id'))) {
            # code...
            return back()->with('error', 'CA total not set for this program.');
        }

        $subject = Subjects::find(request('course_id'));
        $data['ca_total'] = Helpers::instance()->ca_total(request('class_id'));
        $data['title'] = "Fill CA Results For [ ".$subject->code." ] ".$subject->name." / ".ProgramLevel::find(request('class_id'))->name();
        return view('admin.result.fill_ca', $data);
    }

    public function ca_import(){
        // check if CA total is set forthis program
        if (!Helpers::instance()->ca_total_isset(request('class_id'))) {
            # code...
            return back()->with('error', 'CA total not set for this program.');
        }

        $subject = Subjects::find(request('course_id'));
        $data['title'] = "Import CA Results For [ ".$subject->code." ] ".$subject->name." / ".ProgramLevel::find(request('class_id'))->name();
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
            $file->storeAs('/files', $filename);

            $file_pointer = fopen(storage_path('app/files').'/'.$filename, 'r');

            $imported_data = [];
            $course = Subjects::find($request->course_id);
            $year = \App\Helpers\Helpers::instance()->getCurrentAccademicYear();
            $semester = \App\Helpers\Helpers::instance()->getSemester($request->class_id);
            
            while(($row = fgetcsv($file_pointer, 100, ',')) != null){
                if(is_numeric($row[1]))
                $imported_data[] = [$row[0], $row[1]];
            }
            if(count($imported_data)==0){
                return back()->with('error', 'No data or wrong data format.');
            }

            $bad_results = 0;
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
                        'semester_id' => $semester->id
                    ];
                    Result::updateOrCreate($base, ['ca_score'=>$data[1], 'reference'=>$request->reference, 'coef'=>$course->_class_subject($request->class_id)->coef??$course->coef, 'user_id'=>auth()->id(), 'class_subject_id'=>$course->_class_subject($request->class_id)->id??0]);
                }
            }
            if($bad_results > 1){
                return back()->with('success', 'Done. ' . $bad_results . ' records not imported. Unsupported values supplied.');
            }
            return back()->with('success', 'Done');
        }else{
            return back()->with('error', 'Empty or bad file type. CSV files only are accepted.');
        }
        
    }
    
    public function exam_result(){
        $data['title'] = "Student Exam Results";
        return view('admin.result.exam_result', $data);
    }

    public function exam_fill(){
        // check if exam total is set for this program
        if (!Helpers::instance()->exam_total_isset(request('class_id')) || !Helpers::instance()->ca_total_isset(request('class_id'))) {
            # code...
            return back()->with('error', 'CA or Exam total not set for this program.');
        }

        $subject = ClassSubject::find(request('course_id'));
        $data['ca_total'] = Helpers::instance()->ca_total(request('class_id'));
        $data['exam_total'] = Helpers::instance()->exam_total(request('class_id'));
        $data['title'] = "Fill Exam Results For [ ".$subject->code." ] ".$subject->name." / ".ProgramLevel::find(request('class_id'))->name();
        return view('admin.result.fill_exam', $data);
    }

    public function exam_import(){
        // check if exam total is set for this program
        if (!Helpers::instance()->exam_total_isset(request('class_id'))) {
            # code...
            return back()->with('error', 'CA total not set for this program.');
        }

        $subject = ClassSubject::find(request('course_id'));
        $data['title'] = "Import Exam Results For [ ".$subject->code." ] ".$subject->name." / ".ProgramLevel::find(request('class_id'))->name();
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
            $file->storeAs('/files', $filename);

            $file_pointer = fopen(storage_path('app/files').'/'.$filename, 'r');

            $imported_data = [];
            $course = Subjects::find($request->course_id);
            $year = Helpers::instance()->getCurrentAccademicYear();
            $semester = Helpers::instance()->getSemester($request->class_id);
            
            while(($row = fgetcsv($file_pointer, 100, ',')) != null){
                if(is_numeric($row[1]))
                $imported_data[] = [$row[0], $row[1], $row[2]];
            }
            if(count($imported_data)==0){
                return back()->with('error', 'No data or wrong data format.');
            }

            $bad_results = 0;
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
                        'semester_id' => $semester->id
                    ];
                    Result::updateOrCreate($base, ['ca_score'=>$data[1], 'exam_score'=>$data[2], 'reference'=>$request->reference, 'coef'=>$course->_class_subject($request->class_id)->coef??$course->coef, 'user_id'=>auth()->id(), 'class_subject_id'=>$course->_class_subject($request->class_id)->id??0]);
                }
            }
            if($bad_results > 1){
                return back()->with('success', 'Done. ' . $bad_results . ' records not imported. Unsupported values supplied.');
            }
            return back()->with('success', 'Done');
        }else{
            return back()->with('error', 'Empty or bad file type. CSV files only are accepted.');
        }
    }

    public function imports_index()
    {
        # code...
        $data['title'] = "Result Imports";
        return view('admin.result.imports_index', $data);
    }

    public function individual_results()
    {
        $data['title'] = "Individual Results";
        return view('admin.result.individual_result', $data);
    }

    public function class_results()
    {
        $data['title'] = "Class Results";
        return view('admin.result.class_result', $data);
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
                ->get(['student_classes.id', 'student_classes.year_id', 'student_classes.class_id', 'students.name', 'students.matric']);
    
            return $instances;
            
        } catch (\Throwable $th) {
            return $th;
        }
    }
}
