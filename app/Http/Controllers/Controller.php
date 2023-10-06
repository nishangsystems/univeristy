<?php

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use App\Http\Controllers\SMS\Helpers as SMSHelpers;
use App\Services\FocusTargetSms;
use App\Models\CampusProgram;
use App\Models\ClassSubject;
use App\Models\Config as ModelsConfig;
use App\Models\Message;
use App\Models\Students;
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
        $pl_list = \App\Models\ProgramLevel::join('school_units', 'school_units.id', '=', 'program_levels.program_id')->get(['program_levels.*']);
        foreach ($pl_list as $key => $value) {
            # code...
            $pls[] = [
                'id' => $value->id,
                'level_id'=>$value->level_id,
                'program_id'=>$value->program_id,
                'name' => $value->name()??'',
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

        $validity = Validator::make($request->all(), ['reg_no'=>'required']);
        if($validity->fails()){return back()->with('error', $validity->errors()->first());}

        if (Students::where('matric', $request->reg_no)->exists()) { 
            // if (Students::where('matric', $request->reg_no)->whereNotNull('email')->exists()){
            //     return redirect()->route('login')->with('error','Account already exist');   
            // }
            // else {
                $student_d = Students::where('matric', $request->reg_no)->first();
                return view('auth.registration_info',compact('student_d'));
            // }
        }
        else{
            return redirect()->route('registration')->with('error','Matricule '.$request->reg_no.' Not Found in the system. Contact the registry for rectification.');   
        }
     }

    public function createAccount(Request $request){
        
        if(User::where('phone', $request->phone)->orWhere('email', $request->email)->count() > 0){
            return redirect()->route('registration')->with('error', __('text.validation_phrase1'));
            }
        if (Students::where('matric', $request->username)->exists()) {  
            $update['phone'] = $request->phone;
            $update['email'] = $request->email;
            $update['password'] = Hash::make($request->password);
            
            $up = Students::where('matric', $request->username)->update($update);
            
            if(auth('student')->attempt(['matric'=>$request->username, 'password'=>$request->password])){return redirect(route('student.home'));}
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
                auth('student')->login($stud);
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
                auth()->login($user);
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
    public static function sendSmsNotificaition(String $message_text, $contacts, $message_id = null, $record = 0)
    {
        $sms_sender_address = env('SMS_SENDER_ADDRESS');
        // dd($contacts);

        
        if(!is_array($contacts)){
            $contacts = [$contacts];
        }
        $contacts_no_spaces = array_map(function($el){
            return str_replace([' ', '.', '-', '(', ')', '+'], '', $el);
        }, $contacts);
        $complete_contacts = array_map(function($el){
            return strlen($el) <= 9 ? '237'.$el : $el;
        }, $contacts_no_spaces);
        // dd($contacts_no_spaces);
        $cleaned_contacts = array_map(function($el){
            return explode('/',explode(',', $el)[0])[0];
        }, $complete_contacts);

        // dd($contacts);
        // SEND SMS PROPER
        $sent = Self::sendSMS($cleaned_contacts, $message_text);

        if($sent == true && $record > 0){
            // update sms counts record
            $config = ModelsConfig::where('year_id', Helpers::instance()->getCurrentAccademicYear())->first();
            $config->sms_sent += count($cleaned_contacts);
            $config->save();
            if($message_id != null){
                $msg = Message::find($message_id);
                $msg->status = 1;
                $msg->count = count($cleaned_contacts);
                $msg->save();
            }
            return true;
        }
        return $sent;
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

    

    private static function sendSMS($phone_numbers, $message)
    {

        if($message == null){return "Message must not be empty";}
        if($phone_numbers == null){return "Reciever IDs must not be empty";}
        
        return (new FocusTargetSms($phone_numbers, $message))->send();

    }

    public function notify_app($app_data)
    {
        $responseData = [];
            $server_key = env('FIREBASE_SERVER_KEY', "");
            $msg = array(
                'body'  => $app_data['body'],
                'title' => $app_data['title'],
            );

            $fields = array(
                'to'  => '/topics/'.$app_data['to'],
                'notification'  => $msg,
                "data"=> [
                    'action'=>"notfication",
                ]
            );
            $headers = array
            (
                'Authorization: key=' . $server_key,
                'Content-Type: application/json'
            );
            $ch = curl_init();
            curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
            curl_setopt( $ch,CURLOPT_POST, true );
            curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
            curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
            curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
            curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ));
            $result = curl_exec($ch );
            if ($result === FALSE)
            {
                die('FCM Send Error: ' . curl_error($ch));
            }
            $result = json_decode($result,true);
            $responseData['android'] =["result" =>$result ];
            curl_close( $ch );
    }
}
