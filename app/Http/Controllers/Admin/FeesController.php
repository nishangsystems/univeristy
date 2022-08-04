<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Payments;
use App\Models\SchoolUnits;
use App\Models\Students;
use App\Models\TeachersSubject;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FeesController extends Controller
{

    public function classes(Request  $request)
    {

        $title = "Classes";
        $classes = \App\Models\SchoolUnits::where('parent_id', $request->get('parent_id', '0'))->get();

        return view('admin.fee.classes', compact('classes', 'title'));
    }

    public function student(Request  $request, $class_id)
    {
        $class = SchoolUnits::find($class_id);
        $title = $class->name . " Students";
        $students = $class->students(Session::get('mode', \App\Helpers\Helpers::instance()->getCurrentAccademicYear()))->paginate(20);
        return view('admin.fee.students', compact('students', 'title'));
    }

    public function collect(Request  $request)
    {
        $title = "Collect Fee";
        return view('admin.fee.collect', compact('title'));
    }

    public function printFee(Request  $request)
    {
        $title = "Print Fee";
        return view('admin.fee.print', compact('title'));
    }

    public function printStudentFee(Request  $request, $student_id)
    {
        $student = Students::find($student_id);
        $year = \App\Helpers\Helpers::instance()->getYear();
        $numbers = [1, 2];
        return view('admin.fee.print_reciept', compact('student', 'year', 'numbers'));
    }

    public function daily_report(Request  $request)
    {
        $title = "Fee Daily Report for " . ($request->date ? $request->date : Carbon::now()->format('d/m/Y'));
        $fees = Payments::whereDate('created_at', $request->date ? $request->date : Carbon::now())->get();
        return view('admin.fee.daily_report', compact('fees', 'title'));
    }

    public function fee(Request  $request)
    {
        $type = request('type', 'completed');
        $title = $type . " fee ";
        $students = [];
        return view('admin.fee.fee', compact('students', 'title'));
    }

    public function drive(Request  $request)
    {
        $title = "Fee Drive";
        $students = [];
        return view('admin.fee.drive', compact('students', 'title'));
    }

    public function delete(Request  $request, $id)
    {
        Payments::find($id)->delete();
        Session::flash('success', "Fee collection deleted successfully!");
        return redirect()->back();
    }
}
