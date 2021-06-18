<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\SchoolUnits;
use App\Models\StudentClass;
use App\Models\Students;
use App\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Session;

class StudentController extends Controller
{

    public function create(Request $request)
    {
        $data['title'] = "Admit New Student";
        return view('admin.student.create')->with($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'phone' => 'required',
            'address' => 'nullable',
            'gender' => 'required',
            'section' => 'required',
        ]);
        try {
            \DB::beginTransaction();
            $input = $request->all();
            $input['password'] = Hash::make('password');
            $student = \App\Models\Students::create($input);

            $class = StudentClass::create([
                'student_id' => $student->id,
                'class_id' => $request->section,
                'year_id' => \App\Helpers\Helpers::instance()->getCurrentAccademicYear()
            ]);

            $student->admission_batch_id = $class->id;
            $student->save();
            DB::commit();
            return redirect()->to(route('admin.students.index', $request->section))->with('success', "Student saved successfully !");
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $data['title'] = "Student Profile";
        $data['user'] = \App\Models\Students::find($id);
        return view('admin.student.show')->with($data);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function edit(Request $request, $id)
    {
        $data['title'] = "Edit Student Profile";
        $data['student'] = \App\Models\Students::find($id);
        return view('admin.student.edit')->with($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'address' => 'nullable',
            'gender' => 'required',
            'section' => 'required',
        ]);
        try {
            \DB::beginTransaction();
            $input = $request->all();
            $student = Students::find($id);
            $student->update($input);
            $class = StudentClass::where('student_id', $student->id)->where('year_id', \App\Helpers\Helpers::instance()->getCurrentAccademicYear())->first();
            $class->class_id = $request->section;
            $class->save();
            DB::commit();
            return redirect()->to(route('admin.students.index', $request->section))->with('success', "Student saved successfully !");
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $student = Students::find($id);
        if ($student->classes->count() > 1 || $student->result->count() > 0 || $student->payment->count() > 0) {
            return redirect()->back()->with('error', "Student cant be deleted !");
        }
        $student->classes->first()->delete();
        $student->delete();
        return redirect()->back()->with('success', "Student deleted successfully !");
    }

    public  function import()
    {
        $data['title'] = "Import Student";
        return view('admin.student.import')->with($data);
    }

    public  function matric()
    {
        $data['title'] = "Generate Student Matricule Number";
        return view('admin.student.matricule')->with($data);
    }

    public  function matricPost(Request  $request)
    {
        $this->validate($request, [
            'batch' => 'required',
            'section' => 'required',
        ]);
        $sec = $request->section;
        $id = $sec[count($request->section) - 1];
        $students = Students::join('student_classes', ['students.admission_batch_id' => 'student_classes.id'])->where(['student_classes.year_id' => $request->batch, 'student_classes.class_id' => $id])->orderBy('name')->get();
        $section = SchoolUnits::find($id);
        $batch = Batch::find($request->batch);
        foreach ($students as $k => $student) {
            $student->matric = $section->prefix . substr(Batch::find($request->batch)->name, 2, 2) . $section->suffix . str_pad(($k + 1), 3, 0, STR_PAD_LEFT);
            $student->save();
        }
        return redirect()->to(route('admin.students.index', [$id]))->with('success', 'Matricule number generated successfully!');
    }

    public  function importPost(Request  $request)
    {
        // Validate request
        $request->validate([
            'batch' => 'required',
            'file' => 'required|mimes:csv,txt,xlxs',
            'section' => 'required',
        ]);

        $file = $request->file('file');
        // File Details

        $extension = $file->getClientOriginalExtension();
        $filename = "Names." . $extension;
        // Valid File Extensions;
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

            while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                $num = count($filedata);

                for ($c = 0; $c < $num; $c++) {
                    $importData_arr[$i][] = $filedata[$c];
                }
                $i++;
            }
            fclose($file);



            \DB::beginTransaction();
            try {

                foreach ($importData_arr as $importData) {
                    if (Students::where('name', $importData[0])->count() === 0) {
                        $student = \App\Models\Students::create([
                            'name' => str_replace('’', "'", $importData[0]),
                            'gender' => 'male',
                            'password' => Hash::make('12345678'),
                            'email' => explode(' ', str_replace('’', "'", $importData[0]))[0]
                        ]);

                        $class = StudentClass::create([
                            'student_id' => $student->id,
                            'class_id' => $request->section,
                            'year_id' => $request->batch
                        ]);
                        $student->admission_batch_id = $class->id;
                        $student->save();

                        // echo ($importData[0]." Inserted Successfully<br>");
                    } else {
                        //  echo ($importData[0]."  <b style='color:#ff0000;'> Exist already on DB and wont be added. Please verify <br></b>");
                    }
                }

                \DB::commit();
            } catch (\Exception $e) {
                \DB::rollback();
                echo ($e);
            }
            Session::flash('message', 'Import Successful.');
            //echo("<h3 style='color:#0000ff;'>Import Successful.</h3>");

        } else {
            //echo("<h3 style='color:#ff0000;'>Invalid File Extension.</h3>");

            Session::flash('message', 'Invalid File Extension.');
        }

        return redirect()->to(route('admin.students.index', [$request->section]))->with('success', 'Student Imported successfully!');
    }
}
