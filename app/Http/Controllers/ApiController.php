<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use App\Models\CampusDegree;
use App\Models\Certificate;
use App\Models\CertificateProgram;
use App\Models\Degree;
use App\Models\DegreeCertificate;
use App\Models\Level;
use App\Models\ProgramLevel;
use App\Models\SchoolUnits;
use App\Models\StudentClass;
use App\Models\Students;
use Illuminate\Foundation\Console\RouteListCommand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    //

    public function degrees()
    {
        # code...
        return response()->json(['data'=>Degree::all()]);
    }

    public function certificates()
    {
        # code...
        return response()->json(['data'=>Certificate::all()]);
    }

    public function campuses()
    {
        # code...
        return response()->json(['data'=>Campus::all()]);
    }

    public function campus_degrees(Request $request, $campus_id)
    {
        # code...
        return response()->json(['data'=>Campus::find($campus_id)->degrees]);
    }

    public function update_campus_degrees(Request $request, $campus_id)
    {
        # code...
        $validity = Validator::make($request->all(), ['degrees'=>'array']);
        if($validity->fails()){
            return response()->json(['data'=>$validity->errors()->first()])->setStatusCode(400, 'Invalid data provided.');
        }
        if($request->degrees != null){
            CampusDegree::where('campus_id', $campus_id)->each(function($row){
                $row->delete();
            });
            $campus_degs = array_map(function($degree_id)use($campus_id){
                return ['campus_id'=>$campus_id, 'degree_id'=>$degree_id];
            }, $request->degrees);
            CampusDegree::insert($campus_degs);
            return response()->json(['data'=>'1']);
        }else{
            CampusDegree::where('campus_id', $campus_id)->each(function($row){
                $row->delete();
            });
            return response()->json(['data'=>'1']);
        }
    }

    public function campus_program_levels(Request $request, $campus_id, $program_id)
    {
        # code...
        return response()->json(['data'=>Campus::find($campus_id)->programs()->where('program_id', $program_id)->join('levels', ['levels.id'=>'program_levels.level_id'])->select(['levels.*'])->distinct()->get()]) ;
    }

    public function campus_degree_certificate_programs(Request $request, $campus_id, $degree_id, $certificate_id)
    {
        # code...
        // return response()->json(['data'=> Campus::find($campus_id)->programs()->join('school_units', ['school_units.id'=>'program_levels.program_id'])->where('school_units.degree_id', $degree_id)->join('school_units as departments', ['departments.id'=>'school_units.parent_id'])->join('certificate_programs', ['certificate_programs.program_id'=>'school_units.id'])->where('certificate_programs.certificate_id', $certificate_id)->distinct()->get(['school_units.id', 'school_units.name', 'school_units.unit_id', 'departments.name as parent'])]);
        $certificate = Certificate::find($certificate_id);
        $certificate_programs = $certificate->programs()->pluck('school_units.id')->toArray();
        $cert_degree_programs = SchoolUnits::where('degree_id', $degree_id)->whereIn('id', $certificate_programs)->pluck('id')->toArray();
        $campus = Campus::find($campus_id);
        $campus_deg_cert_programs = $campus->programs()->join('school_units', 'school_units.id', '=', 'program_levels.program_id')->whereIn('school_units.id', $cert_degree_programs)->join('school_units as departments', ['departments.id'=>'school_units.parent_id'])->select(['school_units.*', 'departments.name as parent'])->get()->unique('id')->all();
        return response()->json(['data'=> $campus_deg_cert_programs]);
    }

    public function get_certificate_programs(Request $request, $certificate_id)
    {
        # code...
        return response()->json(['data'=>Certificate::find($certificate_id)->programs()->join('school_units as departments', ['departments.id'=>'school_units.parent_id'])->orderBy('school_units.name')->distinct()->get(['school_units.*', 'departments.name as parent'])]);
    }
    public function save_certificate_programs(Request $request, $certificate_id)
    {
        # code...
        $validity = Validator::make($request->all(), ['program_ids'=>'required|array', 'certificate_id'=>'required']);

        if($validity->fails()){
            return response($validity->errors()->first(), 400);
        }

        // delete and re-save certificate programs
        CertificateProgram::where('certificate_id', $certificate_id)->each(function($row){$row->delete();});

        $program_ids = $request->program_ids;   //array of program ids coming from request
        foreach($program_ids as $p_id){
            $instance = new CertificateProgram(['program_id'=>$p_id, 'certificate_id'=>$certificate_id]);
            $instance->save();
        }

        // CertificateProgram::insert($instances);
        return response('success');
    }

    public function programs(){
        return response()->json(['data'=>SchoolUnits::where('school_units.unit_id', 4)->join('school_units as departments', ['departments.id'=>'school_units.parent_id'])->orderBy('school_units.name')->distinct()->get(['school_units.*', 'departments.name as parent'])]);
    }

    public function campus_programs($campus_id)
    {
        # code...
        return response()->json(['data'=>Campus::find($campus_id)->programs()->join('school_units', ['school_units.id'=>'program_levels.program_id'])->join('school_units as departments', ['departments.id'=>'school_units.parent_id'])->groupBy('school_units.id')->orderBy('school_units.name')->distinct()->get(['school_units.*', 'departments.name as parent'])]);
    }

    public function levels()
    {
        # code...
        return response()->json(['data'=>Level::all()]);
    }

    public function store_student(Request $request)
    {
        # code...
        $student = json_decode($request->student);
        // return $student;
        if($student == null){
            return response(json_encode(['data'=>'No student data specified']), 400);
        }
        if(Students::where('matric', $student->matric)->count() > 0){
            return response(json_encode(['data'=>'Assigned matricule number already used']), 400);
        }
        if(Students::where('email', $student->email)->where('admission_batch_id', $student->year_id)->where('active', 1)->count() > 0){
            return response(json_encode(['data'=>'Applicant email already used']), 400);
        }
        if($student != null){
            // save student to Database
            $record = ['name'=>$student->name, 'email'=>$student->email,
            'phone'=>$student->phone,
            'address'=>$student->residence,
            'gender'=>$student->gender,
            'matric'=>$student->matric,
            'dob'=>$student->dob,
            'pob'=>$student->pob,
            'campus_id'=>$student->campus_id,
            'admission_batch_id'=>$student->year_id,
            'password'=>Hash::make('12345678'),
            'parent_name'=>$student->fee_payer_name,
            'program_id'=>$student->program_first_choice,
            'parent_phone_number'=>$student->fee_payer_tel,
            'imported'=>0,
            'active'=>1];
            $student_instance = new Students($record);
            $student_instance->save();

            // create student_class
            $level = Level::where('level', $student->level)->first();
            $program_level = ProgramLevel::where('program_id', $student->program_first_choice)->where('level_id', $level->id)->first();
            if($program_level == null){
                $program_level = new ProgramLevel(['program_id'=>$student->program_first_choice, 'level_id'=> $level->id]);
                $program_level->save();
            }
            $student_class = ['student_id'=>$student_instance->id, 'class_id'=>$program_level->id, 'year_id'=>$student->year_id, 'current'=>1];
            StudentClass::updateOrInsert(['student_id'=>$student_instance->id, 'class_id'=>$program_level->id, 'year_id'=>$student->year_id], ['current'=>1]);

            return response()->json(['data'=>['student'=>$student_instance->toArray(), 'student_class'=>$student_class, 'class'=>$program_level->toArray(), 'status'=>1]]);
        }
    }

    public function update_student(Request $request)
    {
        # code...
        $update = json_decode($request->query('student'))??collect([]);
        $matric = json_decode($request->query('matric'));
        // return $update;

        $student = Students::where('matric', $matric)->first();
        if($update == null){
            return response(json_encode(['data'=>'No update data specified']), 400);
        }
        if(Students::where('matric', $update->matric)->count() > 0){
            return response(json_encode(['data'=>'Assigned matricule number already used']), 400);
        }
        // return $student;
        
        if($student != null){
            // save student to Database
            $record = ['matric'=>$update->matric, 'program_id'=>$update->program];
            $student->update($record);

            // create student_class
            $level = Level::where('level', $update->level)->first();
            $program_level = ProgramLevel::where('program_id', $update->program)->where('level_id', $level->id)->first();
            if($program_level == null){
                $program_level = new ProgramLevel(['program_id'=>$update->program, 'level_id'=> $level->id]);
                $program_level->save();
            }
            $student_class = ['student_id'=>$student->id, 'class_id'=>$program_level->id, 'year_id'=>$student->year_id, 'current'=>1];
            StudentClass::updateOrInsert(['student_id'=>$student->id, 'year_id'=>$student->admission_batch_id], ['current'=>1, 'class_id'=>$program_level->id]);

            return response()->json(['data'=>['student'=>$student->toArray(), 'student_class'=>$student_class, 'class'=>$program_level->toArray(), 'status'=>1]]);
        }
    }

    public function max_matric(Request $request, $prefix, $year)
    {
        # code...
        return response()->json(['data'=> Students::where('matric', 'LIKE', "%{$prefix}/{$year}/%")->orderBy('matric', 'DESC')->get()->pluck('matric')->first()]);
    }

    public function matricule_exists(Request $request)
    {
        $validity = Validator::make($request->all(), ['matric'=>'required']);
        if($validity->fails()){
            return response()->json(['data'=>$validity->errors()->first()]);
        }
        return response()->json(['data'=>(Students::where('matric', $request->matric)->count() > 0)]);
    }

    public function get_degree_certificates($degree_id)
    {
        # code...
        $degree = Degree::find($degree_id);
        if($degree != null){
            return response()->json(['status'=>'success', 'data'=>$degree->certificates->unique('id')]) ;
        }
        return response()->json(['status'=>'failed', 'data'=>[], 'message'=>"specified degree is missing"]);
    }

    public function set_degree_certificates(Request $request, $degree_id)
    {
        # code...
        $certs = $request->certificates; //an indexed array of certificate IDs
        if($certs != null && is_array($certs)){
            DegreeCertificate::where('degree_id', $degree_id)->each(function($row){
                $row->delete();
            });
            $degree_certs = array_map(function($cert)use($degree_id){
                return ['degree_id'=>$degree_id, 'certificate_id'=>$cert];
            }, $certs);
            DegreeCertificate::insert($degree_certs);
            return response()->json(['status'=>'success', 'data'=>$certs]);
        }
        return response()->json(['status'=>'failed', 'data'=>[], 'message'=>'null or wrongly formated request data']);
    }
}
