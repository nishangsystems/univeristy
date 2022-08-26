<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\SchoolUnits;
use App\Models\StudentClass;
use App\Models\Students;
use App\Option;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session as FacadesSession;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Stringable;
use Prophecy\Util\StringUtil;
use App\Services\StudentMatriculeGenerationService;

use function PHPUnit\Framework\stringStartsWith;

class StudentController extends Controller
{

    private $years;
    private $year;
    private $select = [
        'students.id',
        'students.name',
        'students.matric',
        'students.email',
        'students.phone',

    ];

    private $student_matricule_generation_service;

    public function __construct(StudentMatriculeGenerationService $student_matricule_generation_service)
    {
        $this->year = Batch::find(\App\Helpers\Helpers::instance()->getCurrentAccademicYear())->name;
        $this->years = Batch::all();
        $this->student_matricule_generation_service = $student_matricule_generation_service;
    }
    public function index()
    {
        $curent_year = substr($this->year, 5);
        $data['title'] = 'Manage Students';
        $data['school_units'] = DB::table('school_units')->where('parent_id', 0)->get()->toArray();
        $data['years'] = $this->years;
        // $data['students'] = DB::table('students')->whereYear('students.created_at', $curent_year)->get()->toArray();
        $data['students'] = Students::all();
        return view('admin.student.index')->with($data);
    }
    public function getStudentsPerClass(Request $request)
    {
        $type = $this->CheckoutSchoolType($request);
        $data['school_units'] = DB::table('school_units')->where('parent_id', 0)->get()->toArray();
        $data['years'] = $this->years;
        $class_name =  DB::table('school_units')->where('id', $request->class_id)->pluck('name')->first();
        $data['title'] = 'Manage Students Under ' .  $type . ' in ' . $class_name;
        $data['students'] = DB::table('student_classes')
            ->join('students', 'students.id', '=', 'student_classes.student_id')
            ->join('school_units', 'school_units.id', '=', 'student_classes.class_id')
            ->where('student_classes.year_id', $request->batch_id)
            ->where('school_units.id', $request->class_id)
            ->where('students.type', $request->type)
            ->select($this->select)->paginate(15);
        return view('admin.student.index')->with($data);
    }
    private function CheckoutSchoolType($request)
    {
        $type = null;
        if ($request->type == 'day') {
            $type = 'Day Section';
        } else if ($request->type == 'boarding') {
            $type = 'Boarding Section';
        }
        return $type;
    }

    public function getBaseClasses()
    {
        # code...
        // added by Germanus. Loads listing of all classes accross all sections in a given school

        $base_units = DB::table('school_units')->where('parent_id', '>', 0)->get();

        // return $base_units;
        $listing = [];
        $options = [];
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
            // if unit has no child, add to options
            if ($base_units->where('parent_id', '=', $value->id)->count() == 0) {
                $options[$value->id] = $listing[$value->id];
            }
        }
        return $options;
    }

    public function create(Request $request)
    {
        $data['options'] = $this->getBaseClasses();
        // end of germanus' work

        // Original stuff
        $data['title'] = "Admit New Student";
        return view('admin.student.create')->with($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // return $request->all();
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'phone' => 'regex:/^([0-9\s\-\+\(\)]*)$/',
            'address' => 'nullable',
            'religion' => 'nullable',
            'gender' => 'nullable',
            'section' => 'required',
            'type'  => 'required|string',
            'dob' => 'nullable|date',
            'pob' => 'nullable|string',
            'parent_name' => 'nullable|string',
            'parent_phone_number' => 'nulla|unique:students|regex:/^([0-9\s\-\+\(\)]*)$/'
        ]);
        try {
            DB::beginTransaction();
            // read user input
            $input = $request->all();
            $input['password'] = Hash::make('password');
            // create a student
            $student = \App\Models\Students::create($input);
            // create student class
            $class = StudentClass::create([
                'student_id' => $student->id,
                'class_id' => $request->section,
                'year_id' => \App\Helpers\Helpers::instance()->getCurrentAccademicYear()
            ]);

            // create student matricule with the given section ID
            // first check if section has prefix and/ot suffix
            $unit = \App\Models\SchoolUnits::find($request->section);
            if ($unit->prefix == null ) {
                # code...
                return back()->with('error', "Sorry prefix not set. Go to Settings Zone > Manage sections to set prefix");
            }
            if ($unit->suffix == null) {
                # code...
                return back()->with('error', "Sorry, suffix not set. Go to Settings Zone > Manage sections to set suffix");
            }
            // get the first part of matric
            $academic_year_name = \App\Models\Batch::find(\App\Helpers\Helpers::instance()->getCurrentAccademicYear())->name;
            $matric = $unit->prefix . substr($academic_year_name, 2, 2) . $unit->suffix;
         
            // get the highest available matric for this class
            $mats = DB::table('student_classes')
                    ->where('class_id', '=', $request->section)
                    ->join('students', 'students.id', '=', 'student_classes.student_id')
                    ->whereNotNull('matric')
                    ->distinct()
                    ->orderByDesc('students.matric')
                    ->get('matric');

                    if (count($mats) == null) {
                        # code...
                        $mats[0] = $matric.'000';
                        
                    }
                    else {
                        $mats = $mats->pluck('matric')->toArray();
                }

                // extract last 3 digits; serial number, increment and append to class matric template
                $matric_end = (int)substr($mats[0], -3) + 1;
                while (strlen($matric_end)<3) {
                    # code...
                $matric_end = '0'.$matric_end;
                }
                $matric .= $matric_end;


            // set student matric
            $student->matric = $matric;
            $student->admission_batch_id = $class->id;
            $student->save();
            DB::commit();
            return redirect()->to(route('admin.students.index', $request->section))->with('success', "Student saved successfully !");
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $data['title'] = "Student Profile";
        $data['user'] = \App\Models\Students::find($id);
        return view('admin.student.show')->with($data);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function edit(Request $request, $id)
    {
        $data['title'] = "Edit Student Profile";
        $data['student'] = \App\Models\Students::find($id);
        return view('admin.student.edit')->with($data);
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
            'email' => 'nullable',
            'phone' => 'nullable',
            'address' => 'nullable',
            'gender' => 'nullable',
            'section' => 'required',
            'type' => 'required',
            'religion' => 'nullable'
        ]);
        try {
            DB::beginTransaction();
            $input = $request->all();
            $student = Students::find($id);
            $student->update($input);
            $class = StudentClass::where('student_id', $student->id)->where('year_id', \App\Helpers\Helpers::instance()->getCurrentAccademicYear())->first();
            $class->class_id = $request->section;
            $class->save();
            DB::commit();
            return redirect()->back()->with('success', "Student saved successfully !");
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $student = Students::find($id);
        if ($student->classes->count() > 1 || $student->result->count() > 0 || $student->payment->count() > 0) {
            return redirect()->back()->with('error', "Student cant be deleted !");
        }
        $student->classes->first()->delete();
        $student->delete();
        return redirect()->back()->with('success', "Student deleted successfully !");
    }

    public  function import()
    {
        $data['title'] = "Import Student";
        return view('admin.student.import')->with($data);
    }


    public  function matric()
    {
        $data['title'] = "Generate Student Matricule Number";

        return view('admin.student.matricule')->with($data);
    }

    public  function matricPost(Request  $request)
    {
        return $request->all();
        $this->validate($request, [
            'batch' => 'required',
            'section' => 'required',
        ]);

        $sec = $request->section;
        $this->student_matricule_generation_service->generateMatricule($request->section, $request->batch);
        return redirect()->to(route('admin.students.index', [$sec]))->with('success', 'Matricule number generated successfully!');
    }


    public  function importPost(Request  $request)
    {
        // Validate request
        $request->validate([
            'batch' => 'required',
            'file' => 'required|mimes:csv,txt,xlxs',
            'section' => 'required',
        ]);

        $file = $request->file('file');
        // File Details

        $extension = $file->getClientOriginalExtension();
        $filename = "Names." . $extension;
        // Valid File Extensions;
        $valid_extension = array("csv", "xls");
        if (in_array(strtolower($extension), $valid_extension)) {
            // File upload location
            $location = public_path() . '/files/';
            // Upload file
            $file->move($location, $filename);
            $filepath = public_path('/files/' . $filename);

            $file = fopen($filepath, "r");

            $importData_arr = array();
            $i = 0;

            while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                $num = count($filedata);

                for ($c = 0; $c < $num; $c++) {
                    $importData_arr[$i][] = $filedata[$c];
                }
                $i++;
            }
            fclose($file);



            DB::beginTransaction();
            try {

                foreach ($importData_arr as $importData) {
                    if (Students::where('name', $importData[0])->count() === 0) {
                        $student = \App\Models\Students::create([
                            'name' => str_replace('’', "'", $importData[0]),
                            'gender' => 'male',
                            'password' => Hash::make('12345678'),
                            'email' => explode(' ', str_replace('’', "'", $importData[0]))[0]
                        ]);

                        $class = StudentClass::create([
                            'student_id' => $student->id,
                            'class_id' => $request->section,
                            'year_id' => $request->batch
                        ]);
                        $student->admission_batch_id = $class->id;
                        $student->save();

                        // echo ($importData[0]." Inserted Successfully<br>");
                    } else {
                        //  echo ($importData[0]."  <b style='color:#ff0000;'> Exist already on DB and wont be added. Please verify <br></b>");
                    }
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                echo ($e);
            }
            session('message', 'Import Successful.');
            //echo("<h3 style='color:#0000ff;'>Import Successful.</h3>");

        } else {
            //echo("<h3 style='color:#ff0000;'>Invalid File Extension.</h3>");

            session('message', 'Invalid File Extension.');
        }

        return redirect()->to(route('admin.students.index', [$request->section]))->with('success', 'Student Imported successfully!');
    }

    function getSubunitsOf($id){
        DB::table('school_units')->where('parent_id', '=', $id)->get(['id', 'name', 'parent_id']);
    }
    
    public static function getMainClasses()
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
                                ->whereIn('class_id', \App\Http\Controllers\Admin\ProgramController::subunitsOf($request->class_from))
                                ->where('year_id', '=', $request->year_from)
                                ->leftJoin('students', 'student_classes.student_id', '=', 'students.id')
                                ->get(['students.id as id', 'students.matric as matric', 'students.name as name', 'students.email as email']);
        // return $data['students'];

        return view('admin.student.promotion', $data);
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
            $promotion_id = DB::table('pending_promotions')->insertGetId($ppromotion);
            $pending_promotion_students = [];
            foreach($request->students as $student){
                $pending_promotion_students[] = ['pending_promotions_id'=>$promotion_id, 'students_id'=>$student];
            }
            DB::table('pending_promotion_students')->insert($pending_promotion_students);
            return back()->with('success', 'Operation Complete');
            //code...
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('error', 'Error occured: '.$th->getMessage());
        }

    }

    public function teacherInitPromotion(Request $request)
    {
        # code...
        $classes = DB::table('school_units')->distinct()->get(['id', 'base_class', 'target_class']);
        $class_names = DB::table('school_units')->distinct()->get(['id', 'name', 'parent_id']);

        $data['base_classes'] = $this->_getBaseClasses();
        $data['class_pairs'] = $classes;
        $data['class_names'] = $class_names;
        $data['classes'] = $this->getMainClasses();
        return view('teacher.initialise-promotion', $data);
    }

    public function teacherPromotion(Request $request)
    {
    # code...
        $validator = Validator::make($request->all(), [
            'class_from'=>'required',
            'class_to'=>'required',
            'year_from'=>'required',
            'year_to'=>'required'
        ]);
        
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
                                ->whereIn('class_id', \App\Http\Controllers\Admin\ProgramController::subunitsOf($request->class_from))
                                ->where('year_id', '=', $request->year_from)
                                ->leftJoin('students', 'student_classes.student_id', '=', 'students.id')
                                ->get(['students.id as id', 'students.matric as matric', 'students.name as name', 'students.email as email']);
        // return $data['students'];

        return view('teacher.promotion', $data);
    }
    
    public function teacherPromote(Request $request)
    {
        # code...
        $this->pend_promotion($request);
    }
    public function trigger_approval(Request $request)
    {
        # code...
        $data['classes'] = $this->getMainClasses();
        if ($request->promotion_id != null) {
            # get and show all students
            $pending_promotion = DB::table('pending_promotions')->find($request->promotion_id);
            $students = DB::table('pending_promotion_students')
                    ->where('pending_promotions_id', '=', $request->promotion_id)
                    ->join('students', 'students.id', '=', 'pending_promotion_students.students_id')
                    ->get(['students.id as id', 'students.matric as matric', 'students.name as name']);
            $data['students'] = $students;
        }
        return view('admin.student.approve-promotion', $data);
    }
    public function approvePromotion(Request $request)
    {
        # code...
        $validity = Validator::make($request->all(), [
            'pending_promotion'=>'required',
            'students'=>'required|array'
        ]);

        if ($validity->fails()) {
            # code...
            return back()->with('error', json_encode($validity->getMessageBag()->getMessages()));
        }

        // retrieve pending promotion and perform promotion
        $ppromotion = \App\Models\PendingPromotion::find($request->pending_promotion);
        // create request object and call promote for proper promotion
        $promotion_request = new Request();
        $promotion_request->pp = $request->pending_promotion;
        $promotion_request->year_from = $ppromotion->from_year;
        $promotion_request->year_to = $ppromotion->to_year;
        $promotion_request->class_from = $ppromotion->from_class;
        $promotion_request->class_to = $ppromotion->to_class;
        $promotion_request->type = $ppromotion->type;
        $promotion_request->students = $request->students;

        // remove students from pending_promotion_students to student_promotions, update student classes and academic year
        return $this->promote($promotion_request);
    }

    public function promote(Request $request)
    {
        # code...
        try {
            //code...
            // create promotion > create student promotions > update students class and academic year
            $promotion = [
                'from_year'=>$request->year_from,
                'to_year'=>$request->year_to,
                'from_class'=>$request->class_from,
                'to_class'=>$request->class_to,
                'type'=>$request->type,
                'students'=>json_encode($request->students)
            ];
            $promotion_id = DB::table('promotions')->insertGetId($promotion);
            if ($promotion_id != null) {
                # code...
                // create student promotions
                $students_promotion = [];
                foreach ($request->students as $value) {
                    # code...
                    $students_promotion[] = ['student_id'=>$value, 'promotion_id'=>$promotion_id];
                }
                DB::table('student_promotions')->insert($students_promotion);

                // update students' class and academic year
                DB::table('student_classes')->whereIn('student_id', $request->students)->update(['class_id'=>$request->class_to, 'year_id'=>$request->year_to]);

                
                // delete pending_promotion_students
                DB::table('pending_promotion_students')
                    ->where('pending_promotions_id', '=', $request->pp)
                    ->whereIn('students_id', $request->students)
                    ->delete();
                return back()->with('success', 'Students promoted successfully!');
            }


        } catch (\Throwable $th) {
            //throw $th;
            FacadesSession::flash('error', 'Error occured. Promotion failed. Please try again later.');
            return back()->with('error', 'Error occured. Promotion failed. Please try again later.');
        }


    }

    public function cencelPromotion(Request $request)
    {
        # code...
        $pending_promotion = DB::table('pending_promotions')->delete($request->promotion_id);
        db::table('pending_promotion_students')->where('pending_promotions_id', '=', $request->promotion_id)
        ->delete();
        return back()->with('promotion was successfully cancelled');
    }

    public function unitTarget(Request $request){
        $target_id = DB::table('school_units')->find($request->id)->target_class;
        return json_encode(DB::table('school_units')->find($target_id));
    }

    public function initialiseDemotion(Request $request){
        // get promotions for batch demote and target classes for custom demotion
        // get promotions
        $classes = DB::table('school_units')->distinct()->get(['id', 'base_class', 'target_class']);
        $class_names = DB::table('school_units')->distinct()->get(['id', 'name', 'parent_id']);

        $data['base_classes'] = $this->_getBaseClasses();
        $data['class_pairs'] = $classes;
        $data['class_names'] = $class_names;
        $data['classes'] = $this->getMainClasses();
        return view('admin.student.initialise-demotion', $data);
    }
    public function demotion(Request $request){
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

        $mainClasses = $this->getMainClasses();

        $classes = ['cf'=>['id' => $request->class_from, 'name' => $mainClasses[$request->class_from]],'ct' => ['id' => $request->class_to, 'name' => $mainClasses[$request->class_to]]];

        $data['title'] = "Student Demotion";
        $data['request'] = $request;
        $data['classes'] = $classes;
        $data['students'] =  DB::table('student_classes')
                                ->where('class_id', '=', $request->class_from)
                                ->where('year_id', '=', $request->year_from)
                                ->leftJoin('students', 'student_classes.student_id', '=', 'students.id')
                                ->get(['students.id as id', 'students.matric as matric', 'students.name as name', 'students.email as email']);
        // return $data['students'];

        return view('admin.student.promotion', $data);
    }


    public function demote(Request $request){}


    public function unitDemoteTarget(Request $request){
        return DB::table('school_units')->where('target_class', '=', $request->id)->first();
    }

    // Student results/average calculation section

    // get subjects of a given class with class id
    public function getSubjectsByClass($class_id)
    {
        # code...
        return DB::table('class_subjects')
                ->where('class_id', '=', $class_id)
                ->join('subjects', 'subjects.id','=', 'class_subjects.subject_id')
                ->distinct()
                ->get(['subjects.id as id', 'subjects.name as name', 'subjects.coef as coef', 'subjects.code as code']);
    }

    // get subjects of a given student with $student_id
    // here, we get the subjects for which a given student with $student_id has atleast a mark
    public function getSubjectsByStudent($student_id)
    {
        # code...
        return DB::table('student_classes')
                ->where('student_id', '=', $student_id)
                ->join('results', 'student_id', '=', $student_id)
                ->where('results.class_id', '=', 'student_classes.class_id')
                ->whereNotNull('results.score')
                ->join('subjects', 'subjects.id', '=', 'results.subject_id')
                ->distinct()
                ->get(['subjects.id as id', 'subjects.name as name', 'subjects.coef as coef', 'subjects.code as code']);
    }

    // get sum of coeficients for given class subjects with $class_id
    public function getCoeficientsSumByClass($class_id)
    {
        # code...
        $coefs = DB::table('class_subjects')
                ->where('class_id', '=', $class_id)
                ->join('subjects', 'subjects.id','=', 'class_subjects.subject_id')
                ->distinct()
                ->get(['subjects.id as id', 'subjects.name as name', 'subjects.coef as coef', 'subjects.code as code'])
                ->pluck('coef')
                ->toArray();
        return array_sum($coefs);
    }

    // get sum of coeficients for given student with $student_id
    public function getCoeficientsSumByStudent($student_id)
    {
        # code...
        $coefs = DB::table('student_classes')
                ->where('student_id', '=', $student_id)
                ->join('results', 'student_id', '=', $student_id)
                ->where('results.class_id', '=', 'student_classes.class_id')
                ->whereNotNull('results.score')
                ->join('subjects', 'subjects.id', '=', 'results.subject_id')
                ->distinct()
                ->get(['subjects.id as id', 'subjects.name as name', 'subjects.coef as coef', 'subjects.code as code'])
                ->pluck('coef')
                ->toArray();
        return array_sum($coefs);
    }

    // get student's score for subject with $student_id, and $subject_id in class with $class_id, in academic year with $year_id by filter[1st term : 1, 2nd term : 2, 3rd term : 3, annual : null; filter is built upon result.sequence
    public function getStudentSubjectScore($student_id, $subject_id, $class_id, $year_id = 0, $filter = 4)
    {
        # code...
        if ($year_id == null) {
            # code...
            $year_id = \App\Helpers\Helpers::instance()->getCurrentAccademicYear();
        }
        $builder = DB::table('results')
                ->where('batch_id', '=', $year_id)
                ->where('student_id', '=', $student_id)
                ->where('class_id', '=', $class_id)
                ->where('subject_id', '=', $subject_id);
        switch ($filter) {
            case '1':
                # code...
                return array_sum($builder->where('sequence', '=', 1)
                ->where('sequence', '=', 2)
                ->pluck('score')
                ->toArray());
                break;
            case '2':
                # code...
                return array_sum($builder->where('sequence', '=', 3)
                    ->where('sequence', '=', 4)
                    ->pluck('score')
                    ->toArray());
                break;
            case '3':
                # code...
                return array_sum($builder->where('sequence', '=', 5)
                    ->where('sequence', '=', 6)
                    ->pluck('score')
                    ->toArray());
                break;

            default:
                # code...
                return array_sum($builder
                    ->pluck('score')
                    ->toArray());
                break;
        }
    }

    // calculate student's average with specified term; 1,2,3; default : 4 for annual average
    public function getStudentAverage($student_id, $class_id = null, $year_id = null, $filter = null, $gradingSystem = 20)
    {
        $coeficientSum = 0;
        $subject_average = 0;
        $subjects = $this->getSubjectsByStudent($student_id);
        if ($class_id != null) {$coeficientSum = $this->getCoeficientsSumByClass($class_id);
        }else {$coeficientSum = $this->getCoeficientsSumByStudent($student_id);
        }
        foreach($subjects as $key => $subject){
            $subject_average += ($subject->coef / $coeficientSum) * $this->getStudentSubjectScore($student_id, $subject->id, $class_id, $year_id, $filter);
        }
        return $subject_average * $gradingSystem / $coeficientSum;
        
    }

    // get averages for all students in a class
    public function getClassStudentsAverageList($class_id, $year_id = null)
    {
        # code...
    }
}
