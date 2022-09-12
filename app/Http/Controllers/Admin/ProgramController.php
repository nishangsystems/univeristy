<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassSubject;
use App\Models\SchoolUnits;
use App\Models\Students;
use App\Models\Subjects;
use App\Session;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use SebastianBergmann\Environment\Console;

class ProgramController extends Controller
{

    public function sections()
    {
        $data['title'] = "Sections";
        $data['parent_id'] = 0;
        $data['units'] = \App\Models\SchoolUnits::where('parent_id', 0)->get();
        return view('admin.units.sections')->with($data);
    }

    public static function subunitsOf($id){
        $s_units = [];
        $direct_sub = DB::table('school_units')->where('parent_id', '=', $id)->get()->pluck('id')->toArray();
        $s_units[] = $id;
        if (count($direct_sub) > 0) {
            # code...
            foreach ($direct_sub as $sub) {
                # code...
                $s_units = array_merge_recursive($s_units, Self::subunitsOf($sub));
            }
        }
        return $s_units;
    }

    public static function orderedUnitsTree()
    {
        # code...
        $ids = DB::table('school_units')
                ->pluck('id')
                ->toArray();
        $units = [];
        $names = Self::allUnitNames();
        foreach ($ids as $id) {
            # code...
            foreach (Self::subunitsOf($id) as $sub) {
                # code...
                if (!in_array($sub, $units)) {
                    # code...
                    $units[$sub] = $names[$sub];
                }
            } 
        }
        return $units;
    }

    public static function allUnitNames()
    {
        # code...
        // added by Germanus. Loads listing of all classes accross all sections in a given school
        
        $base_units = DB::table('school_units')->get();
    
        // return $base_units;
        $listing = [];
        $separator = ' : ';
        foreach ($base_units as $key => $value) {
            # code...
            // set current parent as key and name as value, appending from the parent_array
            if (array_key_exists($value->parent_id, $listing)) {
                $listing[$value->id] = $listing[$value->parent_id] . $separator . $value->name; 
            }else {$listing[$value->id] = $value->name;}
    
            // atatch parent units if there be any
            if ($base_units->where('id', '=', $value->parent_id)->count() > 0) {
                // return $base_units->where('id', '=', $value->parent_id)->pluck('name')[0];
                $listing[$value->id] = array_key_exists($value->parent_id, $listing) ? 
                $listing[$value->parent_id] . $separator . $value->name :
                $base_units->where('id', '=', $value->parent_id)->pluck('name')[0] . $separator . $value->name ;
            }
            // if children are obove, move over and prepend to children listing
            foreach ($base_units->where('parent_id', '=', $value->id) as $keyi => $valuei) {
                $value->id > $valuei->id ?
                $listing[$valuei->id] = $listing[$value->id] . $separator . $listing[$value->id]:
                null;
            }
        }
        return $listing;
    }

    public function index($parent_id)
    {
        $data = [];
        $parent = \App\Models\SchoolUnits::find($parent_id);
        if (!$parent) {
            return  redirect(route('admin.sections'));
        }
        $units =  $parent->unit;
        $name = $parent->name;
        $data['title'] = ($units->count() == 0) ? "No Sub Units Available in " . $name : "All " . $units->first()->type->name . " > {$name}";
        $data['units']  = $units;
        $data['parent_id']  = $parent_id;
        return view('admin.units.index')->with($data);
    }

    public function show($parent_id)
    {
        $data = [];
        $parent = \App\Models\SchoolUnits::find($parent_id);
        if (!$parent) {
            return  redirect(route('admin.sections'));
        }
        $units =  $parent->unit();
        $data['title'] = ($units->count() == 0) ? "No Sub Units Available in " . $parent->name : "All " . $units->first()->type->name;
        $data['units']  = $units;
        $data['parent_id']  = $parent_id;
        return view('admin.units.show')->with($data);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'type' => 'required',
        ]);
        \DB::beginTransaction();
        try {
            $unit = new \App\Models\SchoolUnits();
            $unit->name = $request->input('name');
            $unit->unit_id = $request->input('type');
            $unit->parent_id = $request->input('parent_id');
            $unit->prefix = $request->input('prefix');
            $unit->suffix = $request->input('suffix');
            $unit->save();
            \DB::commit();
            return redirect()->to(route('admin.units.index', [$unit->parent_id]))->with('success', $unit->name . " Added to units !");
        } catch (\Exception $e) {
            \DB::rollback();
            echo ($e);
        }
    }

    public function edit(Request $request, $id)
    {
        $lang = !$request->lang ? 'en' : $request->lang;
        \App::setLocale($lang);
        $data['id'] = $id;
        $unit = \App\Models\SchoolUnits::find($id);
        $data['unit'] = $unit;
        $data['parent_id'] = \App\Models\SchoolUnits::find($id)->parent_id;
        $data['title'] = "Edit " . $unit->name;
        return view('admin.units.edit')->with($data);
    }

    public function create(Request $request, $parent_id)
    {
        $data['parent_id'] = $parent_id;
        $parent = \App\Models\SchoolUnits::find($parent_id);
        $data['title'] = $parent ? "New Sub-unit Under " . $parent->name : "New Section";
        return view('admin.units.create')->with($data);
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
            'type' => 'required',
        ]);

        \DB::beginTransaction();
        try {
            $unit = \App\Models\SchoolUnits::find($id);
            $unit->name = $request->input('name');
            $unit->unit_id = $request->input('type');
            $unit->prefix = $request->input('prefix');
            $unit->suffix = $request->input('suffix');
            $unit->save();
            \DB::commit();

            return redirect()->to(route('admin.units.index', [$unit->parent_id]))->with('success', $unit->name . " Updated !");
        } catch (\Exception $e) {
            \DB::rollback();
            echo ($e);
        }
    }

    /**
     * Delete the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $slug
     * @return \Illuminate\Http\Response
     */
    public function destroy($slug)
    {
        $unit = \App\Models\SchoolUnits::find($slug);
        if ($unit->unit->count() > 0) {
            return redirect()->back()->with('error', "Unit cant be deleted");
        }
        $unit->delete();
        return redirect()->back()->with('success', "units deleted");
    }

    public function subjects($id)
    {
        $parent = \App\Models\SchoolUnits::find($id);
        $data['title'] = "Subjects under " . $parent->name;
        $data['parent'] = $parent;
        $data['subjects'] = DB::table('class_subjects')
            ->join('school_units', ['school_units.id' => 'class_subjects.class_id'])
            ->join('subjects', ['subjects.id' => 'class_subjects.subject_id'])
            ->where('class_subjects.class_id', $id)
            ->select('subjects.id as subject_id', 'subjects.name', 'class_subjects.coef', 'class_subjects.class_id')->paginate(15);
        //  dd($data['subjects']);
        return view('admin.units.subjects')->with($data);
    }

    public function manageSubjects($id)
    {
        $parent = \App\Models\SchoolUnits::find($id);
        $data['parent'] = $parent;
        $data['title'] = "Manage subjects under " . $parent->name;
        return view('admin.units.manage_subjects')->with($data);
    }

    public function students($id)
    { 
        return $this->studentsListing($id);

        $parent = \App\Models\SchoolUnits::find($id);
        $data['parent'] = $parent;

        $data['title'] = "Manage student under " . $parent->name;
        return view('admin.units.student')->with($data);
    }
     public function studentsListing($id)
     {
        # code...
        // get array of ids of all sub units
        $year = \App\Helpers\Helpers::instance()->getCurrentAccademicYear();
        $subUnits = $this->subunitsOf($id);

        $students = DB::table('student_classes')
                ->whereIn('class_id', $subUnits)
                ->join('students', 'students.id', '=', 'student_classes.student_id')
                ->get();
        $parent = \App\Models\SchoolUnits::find($id);
        $data['parent'] = $parent;
        $data['students'] = $students;
        $data['classes'] = \App\Http\Controllers\Admin\StudentController::getMainClasses();
        $data['title'] = "Manage student under " . $parent->name;
        return view('admin.units.student-listing')->with($data);
     }

    public function saveSubjects(Request  $request, $id)
    {
        $class_subjects = [];
        $validator = Validator::make($request->all(), [
            'subjects' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $parent = \App\Models\SchoolUnits::find($id);

        $new_subjects = $request->subjects;
        foreach ($parent->subject as $subject) {
            array_push($class_subjects, $subject->subject_id);
        }


        foreach ($new_subjects as $subject) {
            if (!in_array($subject, $class_subjects)) {
                \App\Models\ClassSubject::create([
                    'class_id' => $id,
                    'subject_id' => $subject,
                ]);
            }
        }

        foreach ($class_subjects as $k => $subject) {
            if (!in_array($subject, $new_subjects)) {
                ClassSubject::where('class_id', $id)->where('subject_id', $subject)->first()->delete();
            }
        }


        $data['title'] = "Manage subjects under " . $parent->name;
        return redirect()->back()->with('success', "Subjects Saved Successfully");
    }

    public function getSubUnits($parent_id)
    {
        $data = SchoolUnits::where('parent_id', $parent_id)->get();
        return response()->json($data);
    }
}
