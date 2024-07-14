<?php

namespace App\Http\Controllers\OpenAPI;

use App\Http\Controllers\Controller;
use App\Models\Campus;
use App\Models\CampusProgramStatus;
use App\Models\SchoolUnits;
use App\Models\Status;
use App\Models\Students;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
class StatusController extends Controller
{

    
    public function program_status(Request $request){
        try {
            //code...
            $program_id = $request->program_id;
            $campus_id = $request->campus_id;

            $program = SchoolUnits::find($program_id);
            $status = CampusProgramStatus::where('campus_program_status.program_id', $program_id)
                ->where(function($qry)use($campus_id){
                    $campus_id == null ? null : $qry->where('campus_program_status.campus_id', $campus_id);
                })->join('campuses', ['campuses.id'=>'campus_program_status.campus_id'])
                ->select(['campuses.name as campus', 'campus_program_status.status'])->distinct()->get();
            return response()->json(['data'=>$status]);
        } catch (\Throwable $th) {
            //throw $th;
            $err = "F:: {$th->getFile()}, L:: {$th->getLine()}, M:: {$th->getMessage()}";
            return response()->json(['message'=>$err], 500);
        }
    }

    public function campus_propram_status(Request $request){
        try {
            $campus_id = $request->campus_id;
            $status = $request->status;

            $data = Campus::where(function($qry)use($campus_id){
                    $campus_id == null ? null : $qry->where('campuses.id', $campus_id);
                })->join('campus_program_status', ['campus_program_status.campus_id'=>'campuses.id'])
                ->where(function($query)use($status){
                    $status == null ? null : $query->where('campus_program_status.status', $status);
                })->join('school_units', ['school_units.id'=>'campus_program_status.program_id'])
                ->where(['school_units.unit_id'=>4])->select(['campuses.id as campus_id', 'campuses.name as campus', 'school_units.id as program_id', 'school_units.name as program', 'campus_program_status.status'])
                ->orderBy('program')->distinct()->get()->groupBy('campus')
                ->map(function($query, $key){
                    return
                        collect($query)->groupBy('program_id')
                        ->map(function($prog_cols){
                            $rec = $prog_cols->first();
                            $rec->status = $prog_cols->pluck('status')->all();
                            return  $rec;
                        });
                });
            
            return response()->json(['data'=>$data]);
        } catch (\Throwable $th) {
            throw $th;
            $err = "F:: {$th->getFile()}, L:: {$th->getLine()}, M:: {$th->getMessage()}";
            return response()->json(['message'=>$err], 500);
        }
    }

    public function program_provisioning_status(Request $request){
        try {
            //code...
            $provisioning_status = Status::orderBy('name')->distinct()->get();
            return response()->json(['data'=>$provisioning_status]);
        } catch (\Throwable $th) {
            //throw $th;
            $err = "F:: {$th->getFile()}, L:: {$th->getLine()}, M:: {$th->getMessage()}";
            return response()->json(['message'=>$err], 500);
        }
    }

    public function update_campus_program_status(Request $request){
        try {
            //code...
            $validity = validator($request->all(), ['campus_id'=>'required', 'program_status'=>'required|array']);
            if($validity->fails()){
                return response()->json(['message'=>$validity->errors->first()], 400);
            }
    
            $campus = Campus::find($request->input('campus_id'));
            if($campus == null){
                return response()->json(['message'=>"No campus exists with the set ID"], 400);
            }
    
            $program_status = [];
            DB::beginTransaction();
            CampusProgramStatus::where('campus_id', $campus->id)->each(function($rec){$rec->delete();});
            foreach ($request->input('program_status') as $key => $pstatus) {
                foreach($pstatus as $status){
                    $program_status[] = ['campus_id'=>$campus->id, 'program_id'=>$key, 'status'=>$status];
                }
            }
            CampusProgramStatus::insert($program_status);
            DB::commit();
            
            return response()->json(['data'=>$request->all()]);
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            $err = "F:: {$th->getFile()}, L:: {$th->getLine()}, M:: {$th->getMessage()}";
            return response()->json(['message'=>$err], 500);
        }
    }

    public function admission_status_statics(Request $request){
        try {
            //code...
            // request has optional campus_id, year_id, status, program_id
    
            $year = $request->year_id != null ? $request->year_id : \App\Helpers\Helpers::instance()->getCurrentAccademicYear();
            $data = Students::where('students.admission_batch_id', $year)
                ->where(function($qry)use($request){
                    $request->campus_id == null ? null : $qry->where('students.campus_id', $request->campus_id);
                })->where(function($qry)use($request){
                    $request->status == null ? null : $qry->where('students.program_status', $request->status);
                })->join('campuses', ['campuses.id'=>'students.campus_id'])
                ->join('student_classes', ['student_classes.student_id'=>'students.id'])->where('student_classes.year_id', $year)
                ->join('program_levels', ['program_levels.id'=>'student_classes.class_id'])
                ->join('school_units', ['school_units.id'=>'program_levels.program_id'])
                ->where(function($qry)use($request){
                    $request->program_id == null ? null : $qry->where('school_units.id', $request->program_id);
                })->select(['campuses.id as campus_id', 'campuses.name as campus', 'students.program_status', 'school_units.id as program_id', 'school_units.name as program', DB::raw("COUNT(*) as count")])
                ->groupBy('program_id', 'program_status')->distinct()->get();

            return response()->json(['data'=>$data]);
        } catch (\Throwable $th) {
            //throw $th;
            $err = "F:: {$th->getFile()}, L:: {$th->getLine()}, M:: {$th->getMessage()}";
            return response()->json(['message'=>$err], 500);
        }
    }
}
