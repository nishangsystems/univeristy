<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\ProgramLevel;
use App\Models\StudentClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class BioDataDownloadController extends Controller
{
    //
    public function initialize()
    {
        # code...
        $data['title'] = "Download Student Data";
        $data['years'] = Batch::all();
        $data['classes'] = $this->sorted_program_levels();
        return view('admin.student.bio_data', $data);
    }
    //
    public function download(Request $request)
    {
        # code...
        $validity = Validator::make($request->all(), ['class_id'=>'required', 'year_id'=>'required']);
        if($validity->fails()){
            session()->flash('error', $validity->errors()->first());
            return back()->withInput();
        }

        try{
            // Get students per class per accademic year
            $students = StudentClass::where(['student_classes.year_id'=>$request->year_id, 'student_classes.class_id'=>$request->class_id])
                ->join('students', 'students.id', '=', 'student_classes.student_id')->where('students.active', 1)
                ->select(['students.name', 'students.matric', 'students.gender', 'students.dob', 'students.pob'])
                ->get();
    
            if($students->count() > 0){
            
                $class=ProgramLevel::find($request->class_id);
                $year = Batch::find($request->year_id);
                
                $fname = str_replace( [' ', '/', ':', '---', '--'], '-', $year->name.'_'.$class->name().'_BIO_DATA_'.time().'.csv');
                $file = public_path('files/_'.$fname);
                $filewriter = fopen($file, 'w');
                $headings = ['Name', 'Matricule', 'Sex', 'Date of Birth', 'Place of Birth'];
                fputcsv($filewriter, $headings);
                foreach ($students as $key => $student) {
                    # code...
                    fputcsv($filewriter, [$student->name, $student->matric, $student->gender, $student->dob, $student->pob]);
                }
                fclose($filewriter);
                return response()->download($file, $fname);
            }
        }
        catch(Throwable $th){
            return back()->with('error', "Operation failed. ".$th->getMessage());
        }
        
    }
}
