<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return redirect()->to(route('login'));
    }

    public function migrate()
    { $count = 0;
      
        \DB::beginTransaction();
        try{
           
            $students = \App\DemoStudent::all();
            foreach($students as $student){
                $newStudent = new \App\StudentInfo();
                $newStudent->matricule = $student->matricule;
                $newStudent->firstname = $student->firstname;
                $year = \App\Year::where('name',$student->db1)->first();
                $program = \App\Options::where('name',$student->departmet)->first();
                $password = "12345678";
                $newStudent->year_id = $year->id;
                $newStudent->password = Hash::make($password);
                $newStudent->program_id = ($program == null)?'':$program->id;
                $newStudent->gender = $student->sex;
                $newStudent->institutional_email = str_replace(" ", "",strtolower($newStudent->firstname)).'@buib.com';

            $count++;
            }

            \DB::commit();
        echo("successful");
        }catch(\Exception $e){
            echo("<h3>".$count."</h3>");
            \DB::rollback();
            echo ($e);
            }
    }


    public function refresh()
    { $count = 0;
      
        \DB::beginTransaction();
        try{
           
            $students = \App\Students::all();
            foreach($students as $student){
              $studentInfo = \App\StudentInfo::where('matricule',$student->matric)->first();
              if($studentInfo == null){
                echo ("<h3>".$student->id." - ".$count." - ".$student->matric."  </h3> ");
              }else{
                $student->student_id = $studentInfo->id;
                $student->save();
                  $count++;
              }
            }

            \DB::commit();
        echo("successful");
        }catch(\Exception $e){
            echo("<h3>".$count."</h3>");
            \DB::rollback();
            echo ($e);
            }
    }
}
