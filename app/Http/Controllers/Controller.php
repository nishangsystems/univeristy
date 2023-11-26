<?php

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use App\Http\Controllers\SMS\Helpers as SMSHelpers;
use App\Models\Batch;
use App\Services\FocusTargetSms;
use App\Models\CampusProgram;
use App\Models\Charge;
use App\Models\ClassSubject;
use App\Models\Config as ModelsConfig;
use App\Models\Message;
use App\Models\PayIncome;
use App\Models\PaymentItem;
use App\Models\Payments;
use App\Models\PendingTranzakTransaction;
use App\Models\Students;
use App\Models\StudentSubject;
use App\Models\TeachersSubject;
use App\Models\Transcript;
use App\Models\TranzakTransaction;
use App\Models\User;
use App\Models\Wage;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
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

    public function payments_hook_listener(Request $request)
    {
        # code...
        try{
            if(($notf = $request->collect()) != null){
                $resource_id = $request->resourceId;
                $resource = $request->resource;
                $pending_data = PendingTranzakTransaction::where('request_id', $resource_id)->first();
                if($pending_data != null){
                    // --------
                    $path = public_path('hooks/debug.php');
                    $fwriter = fopen($path, 'w+');
                    fputs($fwriter, "______________________RESOURCE______________");
                    fputs($fwriter, $path);
                    
                    fclose($fwriter);
                    // ---------
                    $payment_data = ["payment_id"=>$pending_data->payment_id, "student_id"=>$pending_data->student_id,"batch_id"=>$pending_data->batch_id,'unit_id'=>$pending_data->unit_id,"amount"=>$pending_data->amount,"reference_number"=>$pending_data->reference_number, 'paid_by'=>$pending_data->paid_by, 'payment_purpose'=>$pending_data->payment_type??$pending_data->purpose];
                    
                    if($resource['transactionStatus'] == "SUCCESSFUL" || $resource['transactionStatus'] == "CANCELLED" || $resource['transactionStatus'] == "FAILED" || $resource['transactionStatus'] == "REVERSED"){
                        $req = new Request($resource);
                        
                        // return $request;
                        return $this->hook_tranzak_complete($req, $payment_data, $payment_data['payment_purpose']);
                    }
                }
                return response()->json(['error'=>"tranzaction not found"]);
            }
        }catch(Throwable $th){
            return $th->getMessage();
        }

    }

    public function hook_tranzak_complete(Request $request, $payment_data, $type)
    {
        # code...
        try {
            //code...
            // return $request->all();
            switch ($request->status) {
                case 'SUCCESSFUL':
                    # code...
                    // save transaction and update application_form
                    DB::beginTransaction();
                    $transaction = ['request_id'=>$request->requestId??'', 'amount'=>$request->amount??'', 'currency_code'=>$request->currencyCode??'', 'purpose'=>$request->payment_purpose??'', 'mobile_wallet_number'=>$request->mobileWalletNumber??'', 'transaction_ref'=>$request->mchTransactionRef??'', 'app_id'=>$request->appId??'', 'transaction_id'=>$request->transactionId??'', 'transaction_time'=>$request->transactionTime??'', 'payment_method'=>$request->payer['paymentMethod']??'', 'payer_user_id'=>$request->payer['userId']??'', 'payer_name'=>$request->payer['name']??'', 'payer_account_id'=>$request->payer['accountId']??'', 'merchant_fee'=>$request->merchant['fee']??'', 'merchant_account_id'=>$request->merchant['accountId']??'', 'net_amount_recieved'=>$request->merchant['netAmountReceived']??''];
                    if(TranzakTransaction::where($transaction)->count() == 0){
                        $transaction_instance = new TranzakTransaction($transaction);
                        $transaction_instance->save();
                    }else{
                        $transaction_instance = TranzakTransaction::where($transaction)->first();
                    }
    
                    if($type == 'TRANSCRIPT'){
                        $trans = $payment_data;
                        $trans['transaction_id'] = $transaction_instance->id;
                        $trans['paid'] = 1;
                        if(Transcript::where($trans)->count() == 0)
                            (new Transcript($trans))->save();
                        $message = "Hello ".(auth('student')->user()->name??'').", You have successfully applied for transcript with ST. LOUIS UNIVERSITY INSTITUTE. You paid ".($transaction_instance->amount??'')." for this operation";
                        $this->sendSmsNotificaition($message, [auth('student')->user()->phone]);
                    }elseif($type == 'TUTION'){
                        $trans = $payment_data;
                        $trans['transaction_id'] = $transaction_instance->id;
                        // (new Payments($trans))->save();

                        try {
                            //code...
                            DB::beginTransaction();
                            // return $request->all();
                            $student = Students::find($trans['student_id']);
                            $total_fee = $student->total($trans['student_id']);
                            $balance =  $student->bal($trans['student_id']);
                            $debt = 0;
                            $_data = [];
                            
                            $__amount = $transaction['amount'];
                            
                            foreach (Batch::orderBy('name')->pluck('id')->toArray() as $key => $year_id) {
                                # code...
                                if($year_id > Helpers::instance()->getCurrentAccademicYear()) break;
                                $class = $student->_class($year_id);
                                if($class != null){
                                    $cpid = $class->campus_programs->where('campus_id', $student->campus_id)->first();
                                    if($cpid != null){
                                        $payment_id = $year_id == Helpers::instance()->getCurrentAccademicYear() ? $trans['payment_id'] : PaymentItem::where(['campus_program_id'=>$cpid->id, 'year_id'=>$year_id])->first()->id??null;
                                        $total_balance = $student->total_balance($student->id, $year_id);
                                        if($total_balance > 0){
                                            $amount = 0; $debt = 0;
                                            if($__amount >= $total_balance){
                                                $__amount -= $total_balance;
                                                $amount = $total_balance;
                                            }else{
                                                $amount = $__amount;
                                                $__amount = 0;
                                            }
                                            if($year_id == Helpers::instance()->getCurrentAccademicYear()){
                                                $debt = $__amount > 0 ? -$__amount : 0;
                                            }else{$debt = 0;}
                
                                            $data = [
                                                "payment_id" => $payment_id,
                                                "student_id" => $student->id,
                                                "unit_id" => $class->id,
                                                "batch_id" => $year_id,
                                                "amount" => $amount,
                                                'reference_number' => $request->reference_number.time().'_'.random_int(1000000, 99999999),
                                                'user_id' => auth('student')->id(),
                                                'payment_year_id'=>Helpers::instance()->getCurrentAccademicYear(),
                                                'debt' => $debt,
                                                'transaction_id'=>$transaction_instance->id,
                                                'paid_by' => auth('student')->id(),
                                                'created_at'=>date(DATE_ATOM, time()),
                                                'updated_at'=>date(DATE_ATOM, time())
                                            ];
                                            if ($data['reference_number'] == null || (Payments::where(['reference_number' => $data['reference_number']])->count() == 0)) {
                                                $_data[] = $data;
                                            }else{return back()->with('error', __('text.reference_already_exist'));}
                                        };
                                    }
                                }
                            }
                            // dd($_data);
                            Payments::insert($_data);
                            DB::commit();
                            $message = "Hello ".(auth('student')->user()->name??'').", You have successfully paid a sum of ".($transaction_instance->amount??'')." as part/all of TUTION for ".($transaction_instance->year->name??'')." ST. LOUIS UNIVERSITY INSTITUTE.";
                            $this->sendSmsNotificaition($message, [auth('student')->user()->phone]);
                
                        } catch (\Throwable $th) {
                            DB::rollBack();
                            // throw $th;
                            return back()->with('error', $th->getMessage().'('.$th->getLine().')');
                        }
                    }elseif($type == 'OTHERS'){
                        $trans = $payment_data;
                        $trans['transaction_id'] = $transaction_instance->id;
                        if(PayIncome::where($trans)->count() == 0)
                        ($instance = new PayIncome($trans))->save();
                        $message = "Hello ".(auth('student')->user()->name??'').", You have successfully paid a sum of ".($transaction_instance->amount??'')." as ".($instance->income->name??'')." for ".($transaction_instance->year->name??'')." ST. LOUIS UNIVERSITY INSTITUTE.";
                        $this->sendSmsNotificaition($message, [auth('student')->user()->phone]);
                    }elseif($type == 'RESIT'){
                        $trans = $payment_data;
                        StudentSubject::where(['resit_id'=>$trans['payment_id'], 'student_id'=>$trans['student_id'], 'year_id'=>$trans['year_id']])->update(['paid'=>$transaction_instance->id]);
                        $message = "Hello ".(auth('student')->user()->name??'').", You have successfully paid a sum of ".($transaction_instance->amount??'')." as ".($trans['payment_purpose']??'')." for ".($transaction_instance->year->name??'')." ST. LOUIS UNIVERSITY INSTITUTE.";
                        $this->sendSmsNotificaition($message, [auth('student')->user()->phone]);
                    }elseif($type == 'PLATFORM'){
                        $trans = $payment_data;
                        $data = ['student_id'=>$trans['student_id'], 'year_id'=>$trans['year_id'], 'type'=>'PLATFORM', 'item_id'=>$trans['payment_id'], 'amount'=>$transaction_instance->amount, 'financialTransactionId'=>$transaction_instance->transaction_id, 'used'=>1];
                        $instance = new Charge($data);
                        $instance->save();
                        $message = "Hello ".(auth('student')->user()->name??'').", You have successfully paid a sum of ".($transaction_instance->amount??'')." as ".($trans['payment_purpose']??'')." for ".($transaction_instance->year->name??'')." ST. LOUIS UNIVERSITY INSTITUTE.";
                        $this->sendSmsNotificaition($message, [auth('student')->user()->phone]);
                    }elseif($type == '_TRANSCRIPT'){
                        $trans = $payment_data;
                        $data = ['student_id'=>$trans['student_id'], 'year_id'=>$trans['year_id'], 'type'=>'TRANSCRIPT', 'item_id'=>$trans['payment_id'], 'amount'=>$transaction_instance->amount, 'financialTransactionId'=>$transaction_instance->transaction_id, 'used'=>1];
                        $instance = new Charge($data);
                        $instance->save();
                        $message = "Hello ".(auth('student')->user()->name??'').", You have successfully paid a sum of ".($transaction_instance->amount??'')." as ".($trans['payment_purpose']??'')." for ".($transaction_instance->year->name??'')." ST. LOUIS UNIVERSITY INSTITUTE.";
                        $this->sendSmsNotificaition($message, [auth('student')->user()->phone]);
                    }
                    ($pending = PendingTranzakTransaction::where('request_id', $request->requestId)->first()) != null ? $pending->delete() : null;
                    DB::commit();
                    return redirect(route('student.home'))->with('success', "Payment successful.");
                    break;
                
                case 'CANCELLED':
                    # code...
                    // notify user
                    ($pending = PendingTranzakTransaction::where('request_id', $request->requestId)->first()) != null ? $pending->delete() : null;
                    return redirect(route('student.home'))->with('message', 'Payment Not Made. The request was cancelled.');
                    break;
                
                case 'FAILED':
                    # code...
                    ($pending = PendingTranzakTransaction::where('request_id', $request->requestId)->first()) != null ? $pending->delete() : null;
                    return redirect(route('student.home'))->with('error', 'Payment failed.');
                    break;
                
                case 'REVERSED':
                    # code...
                    ($pending = PendingTranzakTransaction::where('request_id', $request->requestId)->first()) != null ? $pending->delete() : null;
                    return redirect(route('student.home'))->with('message', 'Payment failed. The request was reversed.');
                    break;
                
                default:
                    # code...
                    break;
            }

            return redirect(route('student.home'))->with('error', 'Payment failed. Unrecognised transaction status.');
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public static function _hook_tranzak_complete(Request $request, $payment_data, $type)
    {
        # code...
        try {
            //code...
            // return $request;
            switch ($request->status) {
                case 'SUCCESSFUL':
                    # code...
                    // save transaction and update application_form
                    DB::beginTransaction();
                    $transaction = ['request_id'=>$request->requestId??'', 'amount'=>$request->amount??'', 'currency_code'=>$request->currencyCode??'', 'purpose'=>$request->payment_purpose??'', 'mobile_wallet_number'=>$request->mobileWalletNumber??'', 'transaction_ref'=>$request->mchTransactionRef??'', 'app_id'=>$request->appId??'', 'transaction_id'=>$request->transactionId??'', 'transaction_time'=>$request->transactionTime??'', 'payment_method'=>$request->payer['paymentMethod']??'', 'payer_user_id'=>$request->payer['userId']??'', 'payer_name'=>$request->payer['name']??'', 'payer_account_id'=>$request->payer['accountId']??'', 'merchant_fee'=>$request->merchant['fee']??'', 'merchant_account_id'=>$request->merchant['accountId']??'', 'net_amount_recieved'=>$request->merchant['netAmountReceived']??''];
                    if(TranzakTransaction::where($transaction)->count() == 0){
                        $transaction_instance = new TranzakTransaction($transaction);
                        $transaction_instance->save();
                    }else{
                        $transaction_instance = TranzakTransaction::where($transaction)->first();
                    }
    
                    if($type == 'TRANSCRIPT'){
                        $trans = $payment_data;
                        $trans['transaction_id'] = $transaction_instance->id;
                        $trans['paid'] = 1;
                        if(Transcript::where($trans)->count() == 0)
                            (new Transcript($trans))->save();
                        $message = "Hello ".(auth('student')->user()->name??'').", You have successfully applied for transcript with ST. LOUIS UNIVERSITY INSTITUTE. You paid ".($transaction_instance->amount??'')." for this operation";
                        Self::sendSmsNotificaition($message, [auth('student')->user()->phone]);
                    }elseif($type == 'TUTION'){
                        $trans = $payment_data;
                        $trans['transaction_id'] = $transaction_instance->id;
                        // (new Payments($trans))->save();

                        try {
                            //code...
                            DB::beginTransaction();
                            // return $request->all();
                            $student = Students::find($trans['student_id']);
                            $total_fee = $student->total($trans['student_id']);
                            $balance =  $student->bal($trans['student_id']);
                            $debt = 0;
                            $_data = [];
                            
                            $__amount = $transaction['amount'];
                            
                            foreach (Batch::orderBy('name')->pluck('id')->toArray() as $key => $year_id) {
                                # code...
                                if($year_id > Helpers::instance()->getCurrentAccademicYear()) break;
                                $class = $student->_class($year_id);
                                if($class != null){
                                    $cpid = $class->campus_programs->where('campus_id', $student->campus_id)->first();
                                    if($cpid != null){
                                        $payment_id = $year_id == Helpers::instance()->getCurrentAccademicYear() ? $trans['payment_id'] : PaymentItem::where(['campus_program_id'=>$cpid->id, 'year_id'=>$year_id])->first()->id??null;
                                        $total_balance = $student->total_balance($student->id, $year_id);
                                        if($total_balance > 0){
                                            $amount = 0; $debt = 0;
                                            if($__amount >= $total_balance){
                                                $__amount -= $total_balance;
                                                $amount = $total_balance;
                                            }else{
                                                $amount = $__amount;
                                                $__amount = 0;
                                            }
                                            if($year_id == Helpers::instance()->getCurrentAccademicYear()){
                                                $debt = $__amount > 0 ? -$__amount : 0;
                                            }else{$debt = 0;}
                
                                            $data = [
                                                "payment_id" => $payment_id,
                                                "student_id" => $student->id,
                                                "unit_id" => $class->id,
                                                "batch_id" => $year_id,
                                                "amount" => $amount,
                                                'reference_number' => $request->reference_number.time().'_'.random_int(1000000, 99999999),
                                                'user_id' => auth('student')->id(),
                                                'payment_year_id'=>Helpers::instance()->getCurrentAccademicYear(),
                                                'debt' => $debt,
                                                'transaction_id'=>$transaction_instance->id,
                                                'paid_by' => auth('student')->id(),
                                                'created_at'=>date(DATE_ATOM, time()),
                                                'updated_at'=>date(DATE_ATOM, time())
                                            ];
                                            if ($data['reference_number'] == null || (Payments::where(['reference_number' => $data['reference_number']])->count() == 0)) {
                                                $_data[] = $data;
                                            }else{return back()->with('error', __('text.reference_already_exist'));}
                                        };
                                    }
                                }
                            }
                            // dd($_data);
                            Payments::insert($_data);
                            DB::commit();
                            $message = "Hello ".(auth('student')->user()->name??'').", You have successfully paid a sum of ".($transaction_instance->amount??'')." as part/all of TUTION for ".($transaction_instance->year->name??'')." ST. LOUIS UNIVERSITY INSTITUTE.";
                            $this->sendSmsNotificaition($message, [auth('student')->user()->phone]);
                
                        } catch (\Throwable $th) {
                            DB::rollBack();
                            // throw $th;
                            return back()->with('error', $th->getMessage().'('.$th->getLine().')');
                        }
                    }elseif($type == 'OTHERS'){
                        $trans = $payment_data;
                        $trans['transaction_id'] = $transaction_instance->id;
                        if(PayIncome::where($trans)->count() == 0)
                        ($instance = new PayIncome($trans))->save();
                        $message = "Hello ".(auth('student')->user()->name??'').", You have successfully paid a sum of ".($transaction_instance->amount??'')." as ".($instance->income->name??'')." for ".($transaction_instance->year->name??'')." ST. LOUIS UNIVERSITY INSTITUTE.";
                        Self::sendSmsNotificaition($message, [auth('student')->user()->phone]);
                    }elseif($type == 'RESIT'){
                        $trans = $payment_data;
                        StudentSubject::where(['resit_id'=>$trans['payment_id'], 'student_id'=>$trans['student_id'], 'year_id'=>$trans['year_id']])->update(['paid'=>$transaction_instance->id]);
                        $message = "Hello ".(auth('student')->user()->name??'').", You have successfully paid a sum of ".($transaction_instance->amount??'')." as ".($trans['payment_purpose']??'')." for ".($transaction_instance->year->name??'')." ST. LOUIS UNIVERSITY INSTITUTE.";
                        Self::sendSmsNotificaition($message, [auth('student')->user()->phone]);
                    }elseif($type == 'PLATFORM'){
                        $trans = $payment_data;
                        $data = ['student_id'=>$trans['student_id'], 'year_id'=>$trans['year_id'], 'type'=>'PLATFORM', 'item_id'=>$trans['payment_id'], 'amount'=>$transaction_instance->amount, 'financialTransactionId'=>$transaction_instance->transaction_id, 'used'=>1];
                        $instance = new Charge($data);
                        $instance->save();
                        $message = "Hello ".(auth('student')->user()->name??'').", You have successfully paid a sum of ".($transaction_instance->amount??'')." as ".($trans['payment_purpose']??'')." for ".($transaction_instance->year->name??'')." ST. LOUIS UNIVERSITY INSTITUTE.";
                        Self::sendSmsNotificaition($message, [auth('student')->user()->phone]);
                    }elseif($type == '_TRANSCRIPT'){
                        $trans = $payment_data;
                        $data = ['student_id'=>$trans['student_id'], 'year_id'=>$trans['year_id'], 'type'=>'TRANSCRIPT', 'item_id'=>$trans['payment_id'], 'amount'=>$transaction_instance->amount, 'financialTransactionId'=>$transaction_instance->transaction_id, 'used'=>1];
                        $instance = new Charge($data);
                        $instance->save();
                        $message = "Hello ".(auth('student')->user()->name??'').", You have successfully paid a sum of ".($transaction_instance->amount??'')." as ".($trans['payment_purpose']??'')." for ".($transaction_instance->year->name??'')." ST. LOUIS UNIVERSITY INSTITUTE.";
                        Self::sendSmsNotificaition($message, [auth('student')->user()->phone]);
                    }
                    ($pending = PendingTranzakTransaction::where('request_id', $request->requestId)->first()) != null ? $pending->delete() : null;
                    DB::commit();
                    return redirect(route('student.home'))->with('success', "Payment successful.");
                    break;
                
                case 'CANCELLED':
                    # code...
                    // notify user
                    ($pending = PendingTranzakTransaction::where('request_id', $request->requestId)->first()) != null ? $pending->delete() : null;
                    return redirect(route('student.home'))->with('message', 'Payment Not Made. The request was cancelled.');
                    break;
                
                case 'FAILED':
                    # code...
                    ($pending = PendingTranzakTransaction::where('request_id', $request->requestId)->first()) != null ? $pending->delete() : null;
                    return redirect(route('student.home'))->with('error', 'Payment failed.');
                    break;
                
                case 'REVERSED':
                    # code...
                    ($pending = PendingTranzakTransaction::where('request_id', $request->requestId)->first()) != null ? $pending->delete() : null;
                    return redirect(route('student.home'))->with('message', 'Payment failed. The request was reversed.');
                    break;
                
                default:
                    # code...
                    break;
            }

            return redirect(route('student.home'))->with('error', 'Payment failed. Unrecognised transaction status.');
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

}
