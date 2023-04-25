<?php

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use App\Http\Controllers\SMS\Helpers as SMSHelpers;
use App\Models\Campus;
use App\Models\CampusProgram;
use App\Models\CampusSemester;
use App\Models\ClassSubject;
use App\Models\Semester;
use App\Models\Students;
use App\Models\Subjects;
use App\Models\TeachersSubject;
use App\Models\User;
use App\Models\Wage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Throwable;

/**
 * Summary of Controller
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    var $current_accademic_year;
    public function __construct()
    {
        # code...
        $this->current_accademic_year = Helpers::instance()->getCurrentAccademicYear();
        ini_set('max_execution_time', 360);

    }

    public function set_local(Request $request, $lang)
    {
        # code...
        // return $lang;
        if (array_key_exists($lang, Config::get('languages'))) {
            Session::put('appLocale', $lang);
            App::setLocale($lang);
        }
        return back();
    }

    public static function sorted_program_levels()
    {
        $pls = [];
        # code...
        foreach (\App\Models\ProgramLevel::all() as $key => $value) {
            # code...
            $pls[] = [
                'id' => $value->id,
                'level_id'=>$value->level_id,
                'program_id'=>$value->program_id,
                'name' => $value->program()->first()->name.': LEVEL '.$value->level()->first()->level,
                'department'=> $value->program->parent_id
            ];
        }
        $pls = collect($pls)->sortBy(['name', 'level_id']);
        // $pls->where('id')
        return $pls;
    }
    public static function sorted_campus_program_levels($campus)
    {
        $pls = [];
        # code...
        $program_level_ids = CampusProgram::where(['campus_id'=>$campus])->pluck('program_level_id');
        foreach (\App\Models\ProgramLevel::whereIn('id', $program_level_ids)->get() as $key => $value) {
            # code...
            $pls[] = [
                'id' => $value->id,
                'level_id'=>$value->level_id,
                'program_id'=>$value->program_id,
                'name' => $value->program()->first()->name.': LEVEL '.$value->level()->first()->level
            ];
        }
        $pls = collect($pls)->sortBy('si');
        return $pls;
    }

    public function registration(){
        return view('auth.registration');
    }

    public function check_matricule(Request $request){
        // if (Students::where('matric', $request->reg_no)->exists()) { 
        //        if (User::where('username', $request->reg_no)->exists()) {   
        //           return redirect()->route('registration')->with('error','Matricule Number has being used already. Contact the System Administrator.');   
        //        }else{
        //          $student_d = Students::where('matric', $request->reg_no)->first();   
        //          return view('auth.registration_info',compact('student_d'));
        //        }
             
        //    }
        //    else{
        //      return redirect()->route('registration')->with('error','Invalid Registration Number.');   
        //    }

           if (Students::where('matric', $request->reg_no)->exists()) { 
                if (Students::where('matric', $request->reg_no)->whereNotNull('email')->exists()){
                    return redirect()->route('login')->with('error','Account already exist');   
                }
                else {
                    $student_d = Students::where('matric', $request->reg_no)->first();
                    return view('auth.registration_info',compact('student_d'));
                }
           }
           else{
             return redirect()->route('registration')->with('error','Matricule '.$request->reg_no.' Not Found in the system. Contact the registry for rectification.');   
           }
     }

     public function createAccount(Request $request){
        
        if(User::where('phone', $request->phone)->count() > 0){
            return redirect()->route('registration')->with('error', __('text.validation_phrase1'));
            //  return back()->with('error', 'text.validattion_phrase1');
            }
            // return $request->all();
        if (Students::where('matric', $request->username)->exists()) {  
            $update['phone'] = $request->phone;
            $update['email'] = $request->email;
            $update['password'] = Hash::make($request->password);
            
            $up = Students::where('matric', $request->username)->update($update);
            //  if (User::where('username', $request->username)->exists()) {  
            // $update1['name'] = $request->name;
            // $update1['email'] = $request->email;
            // $update1['username'] = $request->username;
            // $update1['type'] = 'student';
            // $update1['password'] = Hash::make($request->password);
            
            // $up1 = User::where('username', $request->username)->update($update1);
            //  }else{
            //      $insert['name'] = $request->name;
            //     $insert['email'] = $request->email;
            //     $insert['username'] = $request->username;
            //     $insert['type'] = 'student';
            //     $insert['gender'] = '';
            //     $insert['password'] = Hash::make($request->password);
            
            // $up2 = User::create($insert);
            //  }
        //      if( Auth::guard('student')->attempt(['matric'=>$request->username,'password'=>$request->password], $request->remember)){
        //     // return "Spot 1";
        //     return redirect()->intended(route('student.home'));
        // }else{
        //     return redirect()->route('login')->with('s','Account created successfully.');   
        // }
            if(auth('student')->attempt(['matric'=>$request->username, 'password'=>$request->password])){return redirect(route('login'));}
            return redirect()->route('login')->with('s','Account created successfully.');   
            //return redirect()->route('student.home')->with('s','Account created successfully.');   
            
          }

    }

    public function reset_password(Request $request, $id= null)
    {
        # code...
        $data['title'] = "Reset Password";
        if (auth()->guard('student')->check()) {
            return view('student.reset_password', $data);
        }
        else {
            if (auth()->user()->type == 'admin') {
                return view('admin.reset_password', $data);
            }else{
                return view('teacher.reset_password', $data);
            }
        }
    }

    public function reset_password_save(Request $request)
    {
        # code...
        $validator = Validator::make($request->all(), [
            'current_password'=>'required',
            'new_password_confirmation'=>'required_with:new_password|same:new_password|min:6',
            'new_password'=>'required|min:6',
        ]);
        if($validator->fails()){
            return back()->with('error', $validator->errors()->first());
        }
        if (auth()->guard('student')->check()) {
            if(Hash::check($request->current_password, auth('student')->user()->getAuthPassword())){
                $stud = Students::find(auth('student')->id());
                $stud->password = Hash::make($request->new_password);
                $stud->password_reset = true;
                $stud->save();
                return back()->with('success', 'Done');
            }else{
                return back()->with('error', 'Operation failed. Make sure you entered the correct password');
            }
        }
        else{
            if(Hash::check($request->current_password, auth()->user()->getAuthPassword())){
                $user = User::find(auth()->id());
                $user->password = Hash::make($request->new_password);
                $user->password_reset = true;
                $user->save();
                return back()->with('success', 'Done');
            }else{
                return back()->with('error', 'Operation failed. Make sure you entered the correct password');
            }
        }

    }

    /**
     * Summary of sendSmsNotificaition
     * @param string $message_text
     * @param array|Collection $contacts
     * @return bool
     */
    public static function sendSmsNotificaition(String $message_text, $contacts)
    {
        $sms_sender_address = env('SMS_SENDER_ADDRESS');
        // dd($contacts);
        $contacts_no_spaces = array_map(function($el){
            return str_replace([' ', '.', '-', '(', ')', '+'], '', $el);
        }, $contacts);
        // dd($contacts_no_spaces);
        $cleaned_contacts = array_map(function($el){
            return explode('/',explode(',', $el)[0])[0];
        }, $contacts_no_spaces);
        // dd($cleaned_contacts);
        // $basic  = new \Vonage\Client\Credentials\Basic('8d8bbcf8', '04MLvso1he1b8ANc');
        // $client = new \Vonage\Client($basic);


        // SEND SMS PROPER
        SMSHelpers::sendSMS($message_text, $cleaned_contacts);

        // foreach ($contacts as $key => $contact) {
        //     # code...
        //     $message = new \Vonage\SMS\Message\SMS($contact, $sms_sender_address, $message_text);
        //     $client->sms()->send($message);
        // }
        return true;
    }

    public function search_user(Request $request)
    {
        # code...
        $search_key = $request->key;
        if($search_key == null){
            return null;
        }
        $users = User::where('name', 'LIKE', '%'.$search_key.'%')
                ->orWhere('matric', 'LIKE', '%'.$search_key.'%')
                ->orWhere('email', 'LIKE', '%'.$search_key.'%')
                ->orWhere('username', 'LIKE', '%'.$search_key.'%')
                ->take(10)->get();
        return response()->json(['users'=>$users]);
    }

    public static function get_payment_rate($teacher_id, $level_id){
        if($teacher_id != null){
            $rate = Wage::where(['teacher_id'=>$teacher_id, 'level_id'=>$level_id])->first();
            return $rate->price??0;
        }
        return null;
    }

    public function populateCampusSemesterTable()
    {
        # code...
        $campuses = Campus::pluck('id')->toArray();
        $semesters = Semester::pluck('id')->toArray();
        foreach ($campuses as $campus) {
            # code...
            foreach ($semesters as $sem) {
                # code...
                if(CampusSemester::where(['campus_id'=>$campus, 'semester_id'=>$sem])->count() == 0){
                    CampusSemester::create(['campus_id'=>$campus, 'semester_id'=>$sem]);
                }
            }
        }
    }

    
    // Search course by name or course code as req
    public function search_course(Request $request)
    {
        # code...
        $validate = Validator::make($request->all(), ['value'=>'required']);
        // return $request->value;

        try{
            // $pl = DB::table('students')->find(auth('student')->id())->program_id;
            // $program = ProgramLevel::find($pl);
            $subjects = Subjects::
                        // where(['program_levels.program_id'=>$program->program_id])->where('program_levels.level_id', '<=', $program->level_id)
                        // ->join('class_subjects', ['class_subjects.class_id'=>'program_levels.id'])
                        // ->join('subjects', ['subjects.id'=>'class_subjects.subject_id'])
                        where(function($q)use($request){
                            $q->where('subjects.code', 'like', '%'.$request->value.'%')
                            ->orWhere('subjects.name', 'like', '%'.$request->value.'%');
                        })
                        ->select(['subjects.*', 'subjects.coef as cv', 'subjects.status as status'])->orderBy('name')->distinct()->paginate(15);
            return $subjects;
        }
        catch(Throwable $th){return $th->getLine() . '  '.$th->getMessage();}
    }
}
