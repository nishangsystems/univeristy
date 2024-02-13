<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Services\HeadOfSchoolService;
use Illuminate\Http\Request;

class HeadOfSchoolController extends Controller
{
    //
    protected $headOfSchoolService;
    public function __construct(HeadOfSchoolService $headOfSchoolService)
    {
        # code...
        $this->headOfSchoolService = $headOfSchoolService;
    }

    public function schools()
    {
        # code...
        try {
            //code...
            $user = auth()->user();
            $data['title'] = "Schools Headed By ".$user->name;
            $data['user'] = $user;
            $data['schools'] = $this->headOfSchoolService->schools($user->id);
            return view('admin.head_of_school.schools', $data);
        } catch (\Throwable $th) {
            //throw $th;
            return redirect(route('admin.home'))->with('error', $th->getMessage());
        }
    }


    public function school_students(Request $request, $school_id)
    {
        # code...
        try {
            //code...
            $school = $this->headOfSchoolService->getSchoolUnitById($school_id);
            $data['title'] = "Students Under School of ".$school->name??'';
            $data['students'] = $this->headOfSchoolService->school_students($school_id, $request->active??!null, $request->year??$this->current_accademic_year, $request->level_id);
            return view('admin.head_of_school.students', $data);
        } catch (\Throwable $th) {
            //throw $th;
            return redirect(route('admin.headOfSchools.index'))->with('error', $th->getMessage());
        }
    }


    public function departments(Request $request, $school_id)
    {
        # code...
        try {
            //code...
            $school = $this->headOfSchoolService->getSchoolUnitById($school_id);
            $data['title'] = "Departments Under School of ".$school->name??'';
            $data['school'] = $school;
            $data['departments'] = $this->headOfSchoolService->departments($school_id);
            return view('admin.head_of_school.departments', $data);
        } catch (\Throwable $th) {
            //throw $th;
            return redirect(route('admin.headOfSchools.index'))->with('error', $th->getMessage());
        }
    }


    public function department_students(Request $request, $department_id)
    {
        # code...
        try {
            //code...
            $department = $this->headOfSchoolService->getSchoolUnitById($department_id);
            $data['title'] = "Students Under Department of ".$department->name??'';
            $data['department'] = $department;
            $data['students'] = $this->headOfSchoolService->department_students($department_id, $request->active??!null, $request->year??$this->current_accademic_year, $request->level_id);
            return view('admin.head_of_school.students', $data);
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('error', $th->getMessage());
        }
    }


    public function programs(Request $request, $school_id, $department_id = null)
    {
        # code...
        try {
            //code...
            $school = $this->headOfSchoolService->getSchoolUnitById($school_id);
            $department = $department_id == null ? null : $this->headOfSchoolService->getSchoolUnitById($department_id);
            if($department == null){
                $data['title'] = "Programs Under School of ".$school->name??'';
            }else{
                $data['title'] = "Programs Under Department of ".$department->name??'';
            }
            $data['department'] = $department;
            $data['school'] = $school;
            $data['programs'] = $this->headOfSchoolService->programs($school_id, $department_id);
            return view('admin.head_of_school.programs', $data);
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('error', $th->getMessage());
        }

    }
    
    public function program_students(Request $request, $program_id)
    {
        # code...
        try {
            //code...
            $program = $this->headOfSchoolService->getSchoolUnitById($program_id);
            $data['title'] = "Students under ".$program->name;
            $data['students'] = $this->headOfSchoolService->program_students($program_id, $request->active??!null, $request->year??$this->current_accademic_year, $request->level_id);
            $data['program'] = $program;
            return view('admin.head_of_school.students', $data);
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('error', $th->getMessage());
        }
    }

    public function classes(Request $request, $school_id, $department_id = null, $program_id = null)
    {
        # code...
        try {
            //code...
            if($program_id != null){
                $program = $this->headOfSchoolService->getSchoolUnitById($program_id);
                $data['program'] = $program;
            }
            if($department_id != null){
                $department = $this->headOfSchoolService->getSchoolUnitById($department_id);
                $data['department'] = $department;
            }
            $school = $this->headOfSchoolService->getSchoolUnitById($school_id);
            $data['school'] = $school;
    
            if($program != null)
            $data['title'] = "Classes Under ".$program->name??'';
    
            elseif($department != null)
            $data['title'] = "Classes Under Department of ".$department->name??'';
            else
            $data['title'] = "Classes Under School of ".$school->name??'';
    
            $data['classes'] = $this->headOfSchoolService->classes($school_id, $department_id, $program_id);
            return view('admin.head_of_school.classes', $data);
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('error', $th->getMessage());
        }
    }

    public function class_students(Request $request, $class_id)
    {
        # code...
        try {
            //code...
            $class = $this->headOfSchoolService->getClassById($class_id);
            $data['title'] = "Students Under ".$class->name();
            $data['students'] = $this->headOfSchoolService->class_students($class_id, $request->active??!null, $request->year??$this->current_accademic_year);
            return view('admin.head_of_school.students', $data);
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('error', $th->getMessage());
        }

    }
}
