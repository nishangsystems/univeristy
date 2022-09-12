<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Session;

class TeacherController extends Controller
{
  /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['departments'] = \App\Department::all();
        return view('admin.programs.department')->with($data);
    }

    public function programs($id)
    {     $data['dep_id'] = $id;
        $data['programs'] = \App\Department::find($id)->options;
        return view('admin.programs.index')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {        $data['dep_id'] = $request->id;
            return view('admin.programs.create')->with($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $program = new \App\Option();
      $program->name = $request->name;
      $program->description = $request->content;
      $program->department_id = $request->department;
      $program->save();
        return redirect()->to(route('admin.department.programs', $program->department_id))->with('s', "Program was saved !");

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {

        $data['program'] = \App\Options::find($id);
	    return view('admin.programs.show')->with($data);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $data['program'] = \App\Options::find($id);
        return view('admin.programs.edit')->with($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){

        $program = \App\Option::find($id);
        $program->name = $request->name;
        $program->description = $request->content;
        $program->save();
        return redirect()->to(route('admin.department.programs', $program->department_id))->with('s', $program->name." was updates Successfully!");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    	return back();
    }

    public function getMainClasses()
    {
        # code...
        // added by Germanus. Loads listing of all classes accross all sections in a given school
        
        $base_units = DB::table('school_units')->where('parent_id', '>', 0)->get();
    
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
    // get promotion base classes
    function _getBaseClasses(){
        $base_class_ids = DB::table('school_units')->whereNotNull('base_class')->get(['base_class']);
        return DB::table('school_units')->whereIn('id', $base_class_ids->pluck(('base_class')))->get();
    }
    public function initialisePromotion()
    {
        # code...
        // get main and target classes
        $classes = DB::table('school_units')->distinct()->get(['id', 'base_class', 'target_class']);
        $class_names = DB::table('school_units')->distinct()->get(['id', 'name', 'parent_id']);

        $data['base_classes'] = $this->_getBaseClasses();
        $data['class_pairs'] = $classes;
        $data['class_names'] = $class_names;
        $data['classes'] = $this->getMainClasses();
        return view('admin.student.initialise-promotion', $data);
    }

    public function promotion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'class_from'=>'required',
            'class_to'=>'required',
            'year_from'=>'required',
            'year_to'=>'required'
        ]);
        // return $request;
        if ($validator->fails()) {
            # code...
            return back()->with('error', json_encode($validator->getMessageBag()->getMessages()));
        }
        if ($request->class_from >= $request->class_to) {
            # code...
            return back()->with('error', 'next class must be higher than the current');
        }
        if ($request->year_from >= $request->year_to) {
            # code...
            return back()->with('error', 'next academic year must be higher than the current');
        }
        
        $mainClasses = $this->getMainClasses();

        $classes = [
            'cf'=>[
                'id' => $request->class_from, 'name' => $mainClasses[$request->class_from]
            ],
            'ct' => [
                'id' => $request->class_to, 'name' => $mainClasses[$request->class_to]
                ]];

        $data['title'] = "Student Promotion";
        $data['request'] = $request;
        $data['classes'] = $classes;
        $data['students'] =  DB::table('student_classes')
                                ->where('class_id', '=', $request->class_from)
                                ->where('year_id', '=', $request->year_from)
                                ->leftJoin('students', 'student_classes.student_id', '=', 'students.id')
                                ->get(['students.id as id', 'students.matric as matric', 'students.name as name', 'students.email as email']);
        // return $data['students'];

        return view('teacher.student.promotion', $data);
    }

    public function pend_promotion(Request $request)
    {
        // return $request->all();
        # write promotion to pending promotion for confirmation
        $valid = Validator::make($request->all(), [
            'class_from' => 'required',
            'class_to' => 'required',
            'year_from' => 'required',
            'year_to' => 'required',
            'type' => 'required',
            'students' => 'required|array'
        ]);
        if($valid->fails()){
            return back()->with('error', json_encode($valid->getMessageBag()->getMessages()));
        }
        try {
            // create pending promotion and delete it upon confirmation
            $ppromotion = [
                'from_year'=>$request->year_from,
                'to_year'=>$request->year_to,
                'from_class'=>$request->class_from,
                'to_class'=>$request->class_to,
                'type'=>$request->type,
                'students'=>json_encode($request->students)
            ];
            DB::table('pending_promotions')->insert($ppromotion);
            return back()->with('success', 'Operation Complete');
            //code...
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('error', 'Error occured: '.$th->getMessage());
        }

    }

}
