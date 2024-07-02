<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\FeeTrack;
use App\Models\Payments;
use App\Models\ResultTrack;
use App\Models\StudentClass;
use Illuminate\Http\Request;
use App\Models\Students;
use App\Models\StudentTrack;

class TrashController extends Controller
{
    
    //
    public function students(Request $request){
        $data['title'] = "Trashed Student Accounts";
        $data['data'] = Students::onlyTrashed()->orderBy('deleted_at', 'desc')->get();
        return view('admin.trash.students', $data);
    }
    
    //
    public function undo_student_trash(Request $request, $student_id){
        $student = Students::onlyTrashed()->where('id', $student_id)->first();
        // dd($student);
        $track = StudentTrack::where(['student_id'=>$student_id, 'action'=>'STUDENT_ACCOUNT_DELETED'])->first();
        $student->deleted_at = null;
        $sclass_data = ['student_id'=>$student_id, 'class_id'=>$track->class_id, 'year_id'=>$track->year_id];
        StudentClass::create($sclass_data);
        $student->save();
        $track->delete();
        return back()->with('success', "Done");
    }
    
    //
    public function fee(Request $request){
        $data['title'] = "Trashed Fee Records";
        $data['data'] = Payments::onlyTrashed()->orderBy('deleted_at', 'desc')->get();
        // dd($data);
        return view('admin.trash.fee', $data);
    }
    
    //
    public function undo_fee_trash(Request $request, $fee_id){
        $payment = Payments::onlyTrashed()->where('id', $fee_id)->first();
        $track = FeeTrack::where('payment_id', $fee_id)->first();
        $payment->deleted_at = null;
        $payment->save();
        $track == null ? null :$track->delete();
        return back()->with('success', "Done");
    }
    
    //
    public function result_bypass(Request $request){
        $data['title'] = "Student Result Bypass Records";
        $data['data'] = StudentClass::where('bypass_result', true)
            ->join('students', 'students.id', '=', 'student_classes.student_id')
            ->orderBy('student_classes.year_id', 'desc')->orderBy('student_classes.updated_at', 'desc')
            ->select(['student_classes.*', 'students.name', 'students.matric'])->get();
        // dd($data);
        return view('admin.trash.result_bypass', $data);
    }
    
    //
    public function bulk_added_marks(Request $request){
        $data['title'] = "MASS RESULT CHANGES";
        $data['years'] = \App\Models\Batch::all();
        $data['data'] = \App\Models\BulkMarkChange::where(function($qry)use($request){
            $request->month == null ? null : $qry->where('created_at', 'LIKE', '%'.$request->month.'%');
        })->get();
        // dd($data);
        return view('admin.trash.bulk_result_changes', $data);
    }
    
    
    //
    public function cancel_bulk_added_marks(Request $request, $track_id){
        $data['title'] = "Student Result Bypass Records";
        $data['data'] = StudentClass::where('bypass_result', true)
            ->join('students', 'students.id', '=', 'student_classes.student_id')
            ->orderBy('student_classes.year_id', 'desc')->orderBy('student_classes.updated_at', 'desc')
            ->select(['student_classes.*', 'students.name', 'students.matric'])->get();
        // dd($data);
        return view('admin.trash.result_bypass', $data);
    }
    
    
    //
    public function mark_changes(Request $request){
        $data['title'] = "Student Result Change Records";
        $data['years'] = \App\Models\Batch::all();
        $data['data'] = ResultTrack::where(function($rec)use($request){
            $request->month == null ? null : $rec->where('created_at', 'LIKE', '%'.$request->month.'%');
        })->get();
        // dd($data);
        return view('admin.trash.result_changes', $data);
    }
    
    
    //
    public function cancel_mark_changes(Request $request, $track_id){
        $data['title'] = "Student Result Bypass Records";
        $data['data'] = StudentClass::where('bypass_result', true)
            ->join('students', 'students.id', '=', 'student_classes.student_id')
            ->orderBy('student_classes.year_id', 'desc')->orderBy('student_classes.updated_at', 'desc')
            ->select(['student_classes.*', 'students.name', 'students.matric'])->get();
        // dd($data);
        return view('admin.trash.result_bypass', $data);
    }
    
    
    public function program_change_report(Request $request){
        $data['title'] = "Program Change Report";
        $data['changes'] = \App\Models\ProgramChangeTrack::orderBy('id', 'DESC')->get();
        return view('admin.trash.program_change_report', $data);
    }
    
}
