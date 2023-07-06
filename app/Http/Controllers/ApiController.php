<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use App\Models\Certificate;
use App\Models\CertificateProgram;
use App\Models\Degree;
use App\Models\SchoolUnits;
use Illuminate\Foundation\Console\RouteListCommand;
use Illuminate\Http\Request;
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

    public function campus_program_levels(Request $request, $campus_id, $program_id)
    {
        # code...
        return response()->json(['data'=>Campus::find($campus_id)->programs()->where('program_id', $program_id)->join('levels', ['levels.id'=>'program_levels.level_id'])->select(['levels.*'])->distinct()->get()]) ;
    }

    public function campus_degree_certificate_programs(Request $request, $campus_id, $degree_id, $certificate_id)
    {
        # code...
        return response()->json(['data'=> Campus::find($campus_id)->programs()->join('school_units', ['school_units.id'=>'program_levels.program_id'])->where('school_units.degree_id', $degree_id)->join('school_units as departments', ['departments.id'=>'school_units.parent_id'])->join('certificate_programs', ['certificate_programs.program_id'=>'school_units.id'])->where('certificate_programs.certificate_id', $certificate_id)->select(['school_units.*', 'departments.name as parent'])->distinct()->get()]);
    }

    public function get_certificate_programs(Request $request, $certificate_id)
    {
        # code...
        return response()->json(['data'=>Certificate::find($certificate_id)->programs()->join('school_units as departments', ['departments.id'=>'school_units.parent_id'])->select(['school_units.*', 'departments.name as parent'])->distinct()->get()]);
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
        return response()->json(['data'=>SchoolUnits::where('school_units.unit_id', 4)->join('school_units as departments', ['departments.id'=>'school_units.parent_id'])->orderBy('school_units.id', 'ASC')->select(['school_units.*', 'departments.name as parent'])->distinct()->get()]);
    }
}
