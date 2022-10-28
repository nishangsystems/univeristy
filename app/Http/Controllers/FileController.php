<?php

namespace App\Http\Controllers;
use \PDF;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
class FileController extends Controller
{
    // download file
    public function download($file){
      $pathToFile = storage_path().'/'.'app'.'/files/'. $file;
      return response()->download($pathToFile);
    }
    public function result($year, $semester){
        $data['title'] = "Result for ".\App\Year::find($year)->name." ".\App\Semester::find($semester)->name;
        $data['year'] = \App\Year::find($year);
        $data['student'] = \Auth::guard('student')->user();
        $data['semester'] = \App\Semester::find($semester);
        $data['earned'] = 200;
        $data['maximum'] = \Auth::guard('student')->user()->studentInfo->options->max_credit;
        $data['gpa'] = 3.5;
        if( \Auth::guard('student')->user()->studentInfo->options->department_id == 9){
            $data['results'] = \Auth::guard('student')->user()->p_result($year,$semester);
            view()->share($data);
            $pdf = PDF::loadView('templates.p_result');
        }else{
            $data['results'] = \Auth::guard('student')->user()->result($year,$semester);
            view()->share($data);
            $pdf = PDF::loadView('templates.result');
        }
        return $pdf->download( (\Auth::guard('student')->user()!= null)?\Auth::guard('student')->user()->matric:''.'_result.pdf');
    }


    public function ca($year, $semester){
      $data['title'] = "CA for ".\App\Year::find($year)->name." ".\App\Semester::find($semester)->name;
      $data['year'] = \App\Year::find($year);
      $data['student'] = \Auth::guard('student')->user();
      $data['semester'] = \App\Semester::find($semester);
      $data['results'] = \Auth::guard('student')->user()->result($year,$semester);
      view()->share($data);
      $pdf = PDF::loadView('templates.ca');
      //return view('templates.ca')->with($data);
     return $pdf->download( (\Auth::guard('student')->user()!= null)?\Auth::guard('student')->user()->matric:''.'_result.pdf');
    }

    public function getOPTIONS(Request $request){
      $id = $request->department;
      $department = \App\Department::find($id);
      echo json_encode($department->options);
    }

    public function classTimetable(Request $request,$year, $semester, $type){
        $level_id= \Auth::guard('student')->user()->studentInfo->level->id;
        $data['year'] = \App\Helpers\Helpers::instance()->getCurrentAccademicYear(\Auth::guard('student')->user()->studentInfo->level->name);
        $data['semester'] =  \App\Helpers\Helpers::instance()->getCurrentSemester(\Auth::guard('student')->user()->studentInfo->level->name);
        $option = \Auth::guard('student')->user()->studentInfo->options;
        $data['option_id'] = $option->id;
        $data['student'] = $request->user();
        $data['periods']=\App\Period::WHERE('area','=',1)
            ->WHERE('prog_id',$option->id)
            ->WHERE('levels_id',$level_id)
            ->orderby('id' ) ->get();
      view()->share($data);
      $pdf = PDF::loadView('templates.class_timetable');

      return $pdf->download( $option->name.'_class_timetable.pdf');
    }

    public function form_b($year,$semester){
      $data['courses'] = \Auth::guard('student')->user()->courses($year,$semester);
      $data['year'] = \App\Year::find($year);
      $data['student'] = \Auth::guard('student')->user();
      $data['semester'] = \App\Semester::find($semester);
      $data['title'] = "Form R ".\App\Year::find($year)->name." ".\App\Semester::find($semester)->name;
        view()->share($data);
        $pdf = PDF::loadView('templates.courses');
      //return view('templates.courses')->with($data);
        return $pdf->download( \Auth::guard('student')->user()->matric.'_form_b.pdf');
    }



    public function examTimetable(Request $request,$year, $semester, $type){
      $level_id= \Auth::guard('student')->user()->studentInfo->level->id;
        $data['year'] = \App\Helpers\Helpers::instance()->getCurrentAccademicYear(\Auth::guard('student')->user()->studentInfo->level->name);
        $data['semester'] =  \App\Helpers\Helpers::instance()->getCurrentSemester(\Auth::guard('student')->user()->studentInfo->level->name);
        $option = \Auth::guard('student')->user()->studentInfo->options;
        $data['option_id'] = $option->id;
        $data['student'] = $request->user();
        $data['periods']=\App\Period::WHERE('area','=',2)
            ->WHERE('prog_id',$option->id)
            ->WHERE('levels_id',$level_id)
            ->orderby('id' ) ->get();
      view()->share($data);
      $pdf = PDF::loadView('templates.exam_timetable');

      return $pdf->download( $option->name.'_exam_timetable.pdf');
    }

    public function courseList($year_id, $semester_id, $course_id){
       // CSV file name
       $year = \App\Year::find($year_id)->name;
       $semester = \App\Semester::find($semester_id)->name;
       $course = \App\Course::find($course_id)->title;
       $filename = 'Student List for '.$course."-".$year."-".$semester.'.csv';

       //create a file pointer
       $file_pointer = fopen('php://memory','w');

      $studentCourses =\App\Course::find($course_id)->student($year_id,$semester_id)->get();
       //set column header
       $column_header = array(
             'SN',
             'Name',
             'Matricule'
       );
      $delimiter = ",";

       fputcsv($file_pointer, $column_header, $delimiter);

       //output each row of the data ,format as csv and write to file pointer
       $counter = 1;

       foreach($studentCourses as $student){
            $lineData = array(
              $counter,
              $student->studentInfo->firstname." ". $student->studentInfo->lastname,
              $student->matric,

            );
            fputcsv($file_pointer, $lineData, $delimiter);
            $counter++;
       }
      //move back to beginning of file
       fseek($file_pointer, 0);

       //set headers to download file rather than displayed
       header('Content-Type: text/cvs');
       header('Content-Disposition: attachment; filename ="' .$filename . '";');

       //output all remaining data on a file pointer
       fpassthru($file_pointer);

    }

    public function programList($program,$level){
      // CSV file name
      $level = \App\Level::find($level);
      $program = \App\Options::find($program);
      $filename = 'Students Under '.$program->name."- Level ".$level->name.'.csv';

      //create a file pointer
      $file_pointer = fopen('php://memory','w');

      $students = \App\Students::select('*')
      ->join('student_infos',['student_infos.id'=>'students.student_id'])
      ->where(['student_infos.level_id'=>$level->id,'student_infos.program_id'=>$program->id])->orderBy('student_infos.firstname','ASC')->get();

      //set column header
      $column_header = array(
            'SN',
            'Name',
            'Matricule'
      );

      $delimiter = ",";

      fputcsv($file_pointer, $column_header, $delimiter);

      //output each row of the data ,format as csv and write to file pointer
      $counter = 1;

      foreach($students as $student){
           $lineData = array(
             $counter,
             $student->studentInfo->firstname,
             $student->matricule,

           );
           fputcsv($file_pointer, $lineData, $delimiter);
           $counter++;
      }
     //move back to beginning of file
      fseek($file_pointer, 0);

      //set headers to download file rather than displayed
      header('Content-Type: text/cvs');
      header('Content-Disposition: attachment; filename ="' .$filename . '";');

      //output all remaining data on a file pointer
      fpassthru($file_pointer);

    }

    public function  caTemplate($course_id){
        // CSV file name
        $year_id = \App\Helpers\Helpers::instance()->getCurrentAccademicYear(\Session::get('graduate'));
        $semester_id=  \App\Helpers\Helpers::instance()->getCurrentSemester(\Session::get('graduate'));
        $year = \App\Year::find($year_id)->name;
        $semester = \App\Semester::find($semester_id)->name;
        $course = \App\Course::find($course_id)->title;
        $filename = 'CA Template for '.$course."-".$year."-".$semester.'.csv';

        //create a file pointer
        $file_pointer = fopen('php://memory','w');
        $studentCourses =\App\Course::select('students.*','students.id as s_id','student_infos.firstname','courses.id','student_course.year_id','student_course.semester_id')
                            ->join('student_course',['student_course.course_id'=>'courses.id'])
                            ->join('students',['student_course.student_id'=>'students.id'])
                            ->join('student_infos',['students.student_id'=>'student_infos.id'])
                            ->where('courses.id','=',$course_id)
                            ->where('student_course.year_id', '=', $year_id)
                            ->where('student_course.semester_id','=',$semester_id)
                            ->orderBy('student_infos.firstname','ASC')
                            ->get();
       // set column header
        $column_header = array(
              'SN',
              'Name',
              $course,
              'Matricule',
              'Course Code',
              'CA Mark'
        );
          $delimiter = ",";

        fputcsv($file_pointer, $column_header, $delimiter);

        //output each row of the data ,format as csv and write to file pointer
        $counter = 1;

        foreach($studentCourses as $student){
              $lineData = array(
                $counter,
                $student->firstname." ". $student->lastname,
                $course_id,
                $student->matric,
                \App\Course::find($course_id)->course_code,
                '',

              );
              fputcsv($file_pointer, $lineData, $delimiter);
              $counter++;
        }
        //move back to beginning of file
        fseek($file_pointer, 0);

        //set headers to download file rather than displayed
        header('Content-Type: text/cvs');
        header('Content-Disposition: attachment; filename ="' .$filename . '";');

        //output all remaining data on a file pointer
        fpassthru($file_pointer);

    }

    public function teacherCourses(){
        $teachers = \App\TeachersCourses::all();
        foreach($teachers as $teacher){
          $teacher->level_id = $teacher->course1->level->id;
          $teacher->save();
        }

    }


    public function  programFee($program_id, $level_id, $year_id){
     $program = \App\Options::find($program_id);
     $level = \App\Level::find($level_id);
      $filename = 'Program Fee Report '.$program->name." - Level ".$level->name.'.csv';

      //create a file pointer
      $file_pointer = fopen('php://memory','w');
      $students =\App\StudentInfo::where('year_id',$year_id)
                                  ->where('program_id',$program_id)
                                  ->where('level_id',$level_id)
                                  ->orderBy('firstname','ASC')
                                  ->get();
      //set column header
      $column_header = array(
            'SN',
            'Name',
            'Matricule',
            'Amount Payed',
            'Amount Owed'
      );
       $delimiter = ",";

      fputcsv($file_pointer, $column_header, $delimiter);

      //output each row of the data ,format as csv and write to file pointer
      $counter = 1;

      foreach($students as $student){
           $lineData = array(
             $counter,
             $student->firstname,
             $student->matricule,
             $student->totalfeePaid($year_id),
             $student->myBalance($year_id),

           );
           fputcsv($file_pointer, $lineData, $delimiter);
           $counter++;
      }
     //move back to beginning of file
      fseek($file_pointer, 0);

      //set headers to download file rather than displayed
      header('Content-Type: text/cvs');
      header('Content-Disposition: attachment; filename ="' .$filename . '";');

      //output all remaining data on a file pointer
      fpassthru($file_pointer);

  }



}
