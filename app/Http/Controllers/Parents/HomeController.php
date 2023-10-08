<?php

namespace App\Http\Controllers\Parents;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Charge;
use App\Models\Guardian;
use App\Models\NonGPACourse;
use App\Models\PayIncome;
use App\Models\PaymentItem;
use App\Models\Payments;
use App\Models\PlatformCharge;
use App\Models\SchoolContact;
use App\Models\Semester;
use App\Models\Students;
use App\Models\Subjects;
use App\Models\Transcript;
use App\Models\TranzakTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    //

    public function index()
    {
        # code...
        $phone = auth('parents')->user()->phone;
        $data['title'] = __('text.parents_dashboard');
        $data['children'] = Students::where('parent_phone_number', 'LIKE', "%{$phone}%")->join('student_classes', 'student_classes.student_id', '=', 'students.id')->where('student_classes.year_id', '=', Helpers::instance()->getCurrentAccademicYear())->get(['students.*']);
        // return $data;
        return view('parents.index', $data);
    }

    public function fees(Request $request, $child_id)
    {
        # code...
        $stud = Students::find($child_id);
        $year = Helpers::instance()->getCurrentAccademicYear();
        $data['title'] = $stud->name.' '.__('text.fee_record_for')." ".Batch::find($year)->name;
        $data['fee_paid'] = $stud->payments->where('batch_id', $year)->sum('amount');
        $data['student'] = $stud;
        return view('parents.fee', $data);
    }

    public function results(Request $request, $child_id)
    {
        $student = Students::find($child_id);
        $year = Batch::find($request->year ?? Helpers::instance()->getCurrentAccademicYear());
        $class = $student->_class($year->id);
        $semester = $request->semester ? Semester::find($request->semester) : Helpers::instance()->getSemester($class->id);
        // dd($year);
        
        // check if results are published
        if(!$semester->result_is_published($year->id, $child_id)){
            return back()->with('error', 'Results Not Yet Published For This Semester.');
        }

        // check if semester result fee is set && that student has payed 
        $plcharge = PlatformCharge::where(['year_id'=>$year->id])->first();
        $amount = $plcharge->parent_amount ?? null;
        if($amount != null && $amount > 0){
            $charge = Charge::where(['year_id'=>$year->id, 'semester_id'=>$semester->id, 'student_id'=>auth('student')->id(), 'type'=>'RESULTS'])->first();
            if($charge == null){
                return redirect(route('parents.tranzak.platform_charge.pay'))->with('error', 'Pay Platform Charges to continue');
            }
        }

        if($class == null){
            return back()->with('error', "No result found. Make sure you were admitted to this institution by or before the selected academic year");
        }

        $data['title'] = "My Exam Result";
        $data['user'] = $student;
        $data['semester'] = $semester;
        $data['class'] = $class;
        $data['year'] = $year;
        $data['title'] = ($student->name??null).' '.__('text.exam_results').' - '.($year->name??null);
        $data['ca_total'] = $class->program()->first()->ca_total;
        $data['exam_total'] = $class->program()->first()->exam_total;
        $data['grading'] = $class->program()->first()->gradingType->grading()->get() ?? [];
        $res = $student->result()->where('results.batch_id', '=', $year->id)->where('results.semester_id', $semester->id)->distinct()->pluck('subject_id')->toArray();
        $data['subjects'] = $class->subjects()->whereIn('subjects.id', $res)->get();
        $non_gpa_courses = Subjects::whereIn('code', NonGPACourse::pluck('course_code')->toArray())->pluck('id')->toArray();
        // $non_gpa_courses = [];
        // return $non_gpa_courses;
        $results = array_map(function($subject_id)use($data, $year, $semester, $student){
            $ca_mark = $student->result()->where('results.batch_id', '=', $year->id)->where('results.subject_id', '=', $subject_id)->where('results.semester_id', '=', $semester->id)->first()->ca_score ?? 0;
            $exam_mark = $student->result()->where('results.batch_id', '=', $year->id)->where('results.subject_id', '=', $subject_id)->where('results.semester_id', '=', $semester->id)->first()->exam_score ?? 0;
            $total = $ca_mark + $exam_mark;
            $rol = [
                'id'=>$subject_id,
                'code'=>Subjects::find($subject_id)->code ?? '',
                'name'=>Subjects::find($subject_id)->name ?? '',
                'status'=>Subjects::find($subject_id)->status ?? '',
                'coef'=>Subjects::find($subject_id)->coef ?? '',
                'ca_mark'=>$ca_mark,
                'exam_mark'=>$exam_mark,
                'total'=>$total
            ];
            foreach ($data['grading'] as $key => $value) {
                # code...
                if ($total >= $value->lower && $total <= $value->upper) {
                    # code...
                    $grade = $value;
                    $rol['grade'] = $grade->grade;
                    $rol['remark'] = $grade->remark;
                    $rol['weight'] = $grade->weight;
                }
            }
            if(!array_key_exists('grade', $rol)){
                $rol['grade'] = null;
                $rol['remark'] = null;
                $rol['weight'] = null;
            }
            return $rol; 
            
            // dd($grade);
        }, $res);
        // dd($res);
        $data['results'] = collect($results)->filter(function($el){return $el != null;});
        $sum_cv = $data['results']->sum('coef');
        $sum_earned_cv = collect($results)->filter(function($el){return ($el != null) && ($el['ca_mark']+$el['exam_mark'] >= 50);})->sum('coef');
        $gpa_cv = $data['results']->whereNotIn('id', $non_gpa_courses)->sum('coef');
        $gpa_cv_earned = $data['results']->whereNotIn('id', $non_gpa_courses)->filter(function($el){return ($el != null) && ($el['ca_mark']+$el['exam_mark'] >= 50);})->sum('coef');
        $sum_gpts = $data['results']->whereNotIn('id', $non_gpa_courses)->sum(function($item){
            return $item['coef'] * $item['weight'];
        });
        $gpa = $sum_gpts/$gpa_cv;
        // dd($sum_gpts);
        $gpa_data['sum_cv'] = $sum_cv;
        $gpa_data['gpa_cv'] = $gpa_cv;
        $gpa_data['sum_cv_earned'] = $sum_earned_cv;
        $gpa_data['gpa_cv_earned'] = $gpa_cv_earned;
        $gpa_data['gpa'] = $gpa;

        $data['gpa_data'] = $gpa_data;

        $student_id = auth('student')->id();
        $fee = [
            'total_debt'=>$student->total_debts($year->id),
            'total_paid'=>$student->total_paid($year->id),
            'total' => $student->total($year->id),
            'balance' => $student->bal($year->id),
            'total_balance' => $student->total_balance(),
            'fraction' => $semester->semester_min_fee
        ];
        // TOTAL PAID - TOTAL DEBTS FOR THIS YEAR = AMOUNT PAID FOR THIS YEAR
        $data['min_fee'] = $fee['total']*$fee['fraction'];
        $data['access'] = ($fee['total'] - $fee['total_balance']) >= $data['min_fee'] || $student->classes()->where(['year_id'=>$year->id, 'result_bypass_semester'=>$semester->id, 'bypass_result'=>1])->count() > 0;
        // dd($data);
        if ($class->program->background->background_name == "PUBLIC HEALTH") {
            # code...
            return view('student.public_health_exam_result')->with($data);
        }
        return view('parents.exam-result')->with($data);
    }

    public function results_index(Request $request, $child_id)
    {
        # code...
        $data['student'] = Students::find($child_id);
        $data['title'] = $data['student']->name.' '.__('text.exam_results').' - '.Batch::find(Helpers::instance()->getCurrentAccademicYear())->name??null;
        return view('parents.result')->with($data);
    }


    public function tranzak_processing(Request $request, $type)
    {
        # code...
        // return $type;
        $data['title'] = "Processing Transaction";
        $data['item_type'] = $type;
        switch ($type) {
            case 'TRANSCRIPT':
                # code...
                $data['cache_token_key'] = config('tranzak.tranzak.transcript_token');
                $data['tranzak_app_id'] = config('tranzak.tranzak.transcript_app_id');
                $data['tranzak_api_key'] = config('tranzak.tranzak.transcript_api_key');
                $data['transaction_data'] = config('tranzak.tranzak.transcript_transaction');
                $data['transaction'] = session()->get($data['transaction_data']);
                break;
                
            case 'TUTION':
                # code...
                $data['cache_token_key'] = config('tranzak.tranzak.tution_token');
                $data['tranzak_app_id'] = config('tranzak.tranzak.tution_app_id');
                $data['tranzak_api_key'] = config('tranzak.tranzak.tution_api_key');
                $data['transaction_data'] = config('tranzak.tranzak.tution_transaction');
                $data['transaction'] = session()->get($data['transaction_data']);
                break;
                    
            case 'OTHERS':
                # code...
                $data['cache_token_key'] = config('tranzak.tranzak.others_token');
                $data['tranzak_app_id'] = config('tranzak.tranzak.others_app_id');
                $data['tranzak_api_key'] = config('tranzak.tranzak.others_api_key');
                $data['transaction_data'] = config('tranzak.tranzak.others_transaction');
                $data['transaction'] = session()->get($data['transaction_data']);
                break;
                    
            case 'PLATFORM':
                # code...
                $data['cache_token_key'] = config('tranzak.tranzak.platform_token');
                $data['tranzak_app_id'] = config('tranzak.tranzak.platform_app_id');
                $data['tranzak_api_key'] = config('tranzak.tranzak.platform_api_key');
                $data['transaction_data'] = config('tranzak.tranzak.platform_transaction');
                $data['transaction'] = session()->get($data['transaction_data']);
                break;
            
        }
        // return $data;
        return view('parents.momo.processing', $data);
        
    }

    public function tranzak_complete(Request $request, $type)
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
                    $transaction_instance = new TranzakTransaction($transaction);
                    $transaction_instance->save();
    
                    if($type == 'TRANSCRIPT'){
                        $trans = session()->get(config('tranzak.tranzak.transcript_data'));
                        $trans['transaction_id'] = $transaction_instance->id;
                        $trans['paid'] = 1;
                        $instance = new Transcript($trans);
                        $instance->save();
                    }elseif($type == 'TUTION'){
                        $trans = session()->get(config('tranzak.tranzak.tution_data'));
                        $trans['transaction_id'] = $transaction_instance->id;
                        // $instance = new Payments($trans);
                        // $instance->save();

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
                                                'user_id' => auth('parents')->id(),
                                                'payment_year_id'=>Helpers::instance()->getCurrentAccademicYear(),
                                                'debt' => $debt,
                                                'transaction_id'=>$transaction_instance->id,
                                                'paid_by' => auth('parents')->id(),
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
                
                        } catch (\Throwable $th) {
                            DB::rollBack();
                            return back()->with('error', $th->getMessage());
                        }

                    }elseif($type == 'OTHERS'){
                        $trans = session()->get(config('tranzak.tranzak.others_data'));
                        $trans['transaction_id'] = $transaction_instance->id;
                        $instance = new PayIncome($trans);
                        $instance->save();
                    }elseif($type == 'PLATFORM'){
                        $trans = session()->get(config('tranzak.tranzak.platform_data'));
                        $trans['transaction_id'] = $transaction_instance->id;
                        $_trans = ['student_id'=>$trans['student_id'], 'year_id'=>$trans['payment_id'], 'type'=>$type, 'item_id'=>$trans['payment_id'], 'amount'=>$transaction_instance->amount??'', 'financialTransactionId'=>$transaction_instance->id??'', 'used'=>1, 'parent'=>1];
                        $instance = new Charge($_trans);
                        $instance->save();
                    }
                    DB::commit();
                    return redirect(route('parents.home'))->with('success', "Payment successful.");
                    break;
                
                case 'CANCELLED':
                    # code...
                    // notify user
                    return redirect(route('parents.home'))->with('message', 'Payment Not Made. The request was cancelled.');
                    break;
                
                case 'FAILED':
                    # code...
                    return redirect(route('parents.home'))->with('error', 'Payment failed.');
                    break;
                
                case 'REVERSED':
                    # code...
                    return redirect(route('parents.home'))->with('message', 'Payment failed. The request was reversed.');
                    break;
                
                default:
                    # code...
                    break;
            }

            return redirect(route('parents.home'))->with('error', 'Payment failed. Unrecognised transaction status.');
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    
    public function tranzak_pay_fee ($child_id)
    {
        # code...
        $student = Students::find($child_id);
        $data['title'] = "Pay Fee";
        $data['student'] = $student;
        $data['balance'] = $student->bal(auth('student')->id());
        $data['scholarship'] = Helpers::instance()->getStudentScholarshipAmount($student->id);
        $data['total_fee'] = $student->total();

        if ($data['total_fee'] <= 0) {

            return redirect(route('parents.home'))->with('error', 'Fee not set');
        }
        return view('parents.pay_fee', $data);
    }

    public function tranzak_pay_other_incomes ()
    {
        $data['title'] = "Pay Other Incomes";
        return view('parents.pay_others', $data);
    }

    public function tranzak_pay_fee_momo (Request $request, $student_id)
    {
        $validator = Validator::make($request->all(),
        [
            'tel'=>'required|numeric|min:9',
            'amount'=>'required|numeric',
            // 'callback_url'=>'required|url',
            'student_id'=>'required|numeric',
            'year_id'=>'required|numeric',
            'payment_purpose'=>'required',
            'payment_id'=>'required|numeric'
        ]);
        # code...
        if($validator->fails()){
            return back()->with('error', $validator->errors()->first());
        }

        $student = Students::find($student_id);
        $data = ["payment_id"=>$request->payment_id,"student_id"=>$student_id,"batch_id"=>$request->year_id,'unit_id'=>$student->_class()->id,"amount"=>$request->amount,"reference_number"=>'fee.tranzak_momo_payment_'.time().'_'.random_int(100000, 999999).'_'.$student_id, 'paid_by'=>'TRANZAK_MOMO'];
        if(Helpers::instance()->payChannel() == 'tranzak'){
            session()->put(config('tranzak.tranzak.tution_data'), $data);
            return $this->tranzak_pay($request->payment_purpose, $request, $student_id);
        }elseif(Helpers::instance()->payChannel() == 'momo'){

        }
    }

    public function tranzak_pay_other_incomes_momo (Request $request, $student_id)
    {
        $validator = Validator::make($request->all(),
        [
            'tel'=>'required|numeric|min:9',
            'amount'=>'required|numeric',
            // 'callback_url'=>'required|url',
            'student_id'=>'required|numeric',
            'year_id'=>'required|numeric',
            'payment_purpose'=>'required',
            'payment_id'=>'required|numeric'
        ]);
        # code...
        if($validator->fails()){
            return back()->with('error', $validator->errors()->first());
        }

        $student = Students::find($student_id);
        $data = ['income_id'=>$request->payment_id, 'batch_id'=>$request->year_id, 'class_id'=>$student->_class()->id, 'student_id'=>$student_id, 'paid_by'=>'TRANZAK_MOMO'];
        session()->put(config('tranzak.tranzak.others_data'), $data);

        return $this->tranzak_pay($request->payment_purpose, $request, $student_id);
    }

    public function tranzak_pay(string $purpose, $request, $student_id){

        $validator = Validator::make($request->all(),
        [
            'tel'=>'required|numeric|min:9',
            'amount'=>'required|numeric',
            // 'callback_url'=>'required|url',
            'student_id'=>'required|numeric',
            'year_id'=>'required|numeric',
            'payment_purpose'=>'required',
            'payment_id'=>'required|numeric'
        ]);

        if($validator->fails()){return back()->with('error', $validator->errors()->first());}

        // return cache('tranzak_credentials_token');

        try {
            //code...
            // check if token exist and hasn't expired or get new token otherwise
            $student = Students::find($student_id);
            switch($request->payment_purpose){
                case "TRANSCRIPT":
                    $cache_token_key = config('tranzak.tranzak.transcript_token');
                    $tranzak_app_id = config('tranzak.tranzak.transcript_app_id');
                    $tranzak_api_key = config('tranzak.tranzak.transcript_api_key');
                    $transaction_data = config('tranzak.tranzak.transcript_transaction');
                    break;
                    
                case "TUTION":
                    $cache_token_key = config('tranzak.tranzak.tution_token');
                    $tranzak_app_id = config('tranzak.tranzak.tution_app_id');
                    $tranzak_api_key = config('tranzak.tranzak.tution_api_key');
                    $transaction_data = config('tranzak.tranzak.tution_transaction');
                    break;
    
                case "OTHERS":
                    $cache_token_key = config('tranzak.tranzak.others_token');
                    $tranzak_app_id = config('tranzak.tranzak.others_app_id');
                    $tranzak_api_key = config('tranzak.tranzak.others_api_key');
                    $transaction_data = config('tranzak.tranzak.others_transaction');
                    break;
    
                case "PLATFORM":
                    $cache_token_key = config('tranzak.tranzak.platform_token');
                    $tranzak_app_id = config('tranzak.tranzak.platform_app_id');
                    $tranzak_api_key = config('tranzak.tranzak.platform_api_key');
                    $transaction_data = config('tranzak.tranzak.platform_transaction');
                    break;
    
            }
            // $tranzak_credentials = TranzakCredential::where('campus_id', $student->campus_id)->first();
            if(cache($cache_token_key) == null or Carbon::parse(cache($cache_token_key.'_expiry'))->isAfter(now())){
                // get and cache different token
                GENERATE_TOKEN:
                $response = Http::post(config('tranzak.tranzak.base').config('tranzak.tranzak.token'), ['appId'=>$tranzak_app_id, 'appKey'=>$tranzak_api_key]);
                if($response->status() == 200){
                    // cache token and token expirationtot session
                    cache([$cache_token_key => json_decode($response->body())->data->token]);
                    cache([$cache_token_key.'_expiry'=>Carbon::createFromTimestamp(time() + json_decode($response->body())->data->expiresIn)]);
                }
            }
            // Assumed there is a valid api token
            // Moving to performing the payment request proper
            $headers = ['Authorization'=>'Bearer '.cache($cache_token_key)];
            $request_data = ['mobileWalletNumber'=>'237'.$request->tel, 'mchTransactionRef'=>'_'.str_replace(' ', '_', $request->payment_purpose).'_payment_'.time().'_'.random_int(1, 9999), "amount"=> $request->amount, "currencyCode"=> "XAF", "description"=>"Payment for {$request->payment_purpose} - ST LOUIS UNIVERSITY INSTITUTE."];
            $_response = Http::withHeaders($headers)->post(config('tranzak.tranzak.base').config('tranzak.tranzak.direct_payment_request'), $request_data);
            if($_response->status() == 200){
                // save transaction and track it status
                if($_response->collect()->toArray()['success']){
                    session()->put($transaction_data, json_decode($_response->body())->data);
                    return redirect(route('parents.tranzak.processing', $purpose));
                    // return $_response->collect();
                }else{
                    goto GENERATE_TOKEN;
                }
            }
        } catch (\Throwable $th) {
            return back()->with('error', $th->getCode().'. '.$th->getMessage());
        }
    }

    public function contact_school()
    {
        # code...
        $data['title'] = __('text.contact_school');
        $data['contacts'] = SchoolContact::all();
        return view('parents.contact_school', $data);
    }
    
    public function tranzak_platform()
    {
        # code...
        $year = Batch::find(Helpers::instance()->getCurrentAccademicYear());
        $plcharge = PlatformCharge::where(['year_id'=>$year->id])->first();
        $data['title'] = __('text.PLATFORM_CHARGES')." - ".$year->name??null;
        $data['year'] = $year;
        $data['parent'] = auth('parents')->user();
        $data['amount'] = $plcharge->parent_amount??0;
        return view('parents.pay_platform', $data);
    }

    public function tranzak_platform_pay(Request $request)
    {
        # code...
        $validator = Validator::make($request->all(),
        [
            'tel'=>'required|numeric|min:9',
            'amount'=>'required|numeric',
            // 'callback_url'=>'required|url',
            'student_id'=>'required|numeric',
            'year_id'=>'required|numeric',
            'payment_purpose'=>'required',
            'payment_id'=>'required|numeric'
        ]);
        # code...
        if($validator->fails()){
            return back()->with('error', $validator->errors()->first());
        }

        $parent = Guardian::find($request->student_id);
        $data = ["payment_id"=>$request->payment_id,"student_id"=>$parent->id,"batch_id"=>$request->year_id,'unit_id'=>$parent->children()->first()->_class()->id,"amount"=>$request->amount,"reference_number"=>'platform.tranzak_momo_payment_'.time().'_'.random_int(100000, 999999).'_'.$parent->id, 'paid_by'=>'TRANZAK_MOMO'];
        session()->put(config('tranzak.tranzak.platform_data'), $data);
        return $this->tranzak_pay($request->payment_purpose, $request, $parent->id);
    }

}
