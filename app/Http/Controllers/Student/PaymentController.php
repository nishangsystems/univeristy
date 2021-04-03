<?php

namespace App\Http\Controllers\Student;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Auth;
class PaymentController extends Controller
{
  
    public function method(){
         return view('student.payment.method');
    }


    public function info($type,$year,$semester){
       $data['type'] = \App\TransactionType::find($type);
       $data['year_id'] =$year;
       $data['semester_id'] = $semester;
      return view('student.payment.info')->with($data);
    }
    
    public function index(){
      $year =  \App\Helpers\Helpers::instance()->getCurrentAccademicYear(\Auth::guard('student')->user()->studentInfo->level->name);
      $data['payments'] = \Auth::guard('student')->user()->myFeesPayment($year);
       $data['year'] =  $year;
      return view('student.payment.index')->with($data);
    }

    public function index_post(Request $request){
      $year = $request->year;
      $data['payments'] = \Auth::guard('student')->user()->myFeesPayment($year);
       $data['year'] =  $year;
      return view('student.payment.index')->with($data);
    }



    public function __construct(){
        $this->middleware('auth:student');
    }

  public function send(Request $request){
    $transaction = \App\TransactionType::find($request->type);
    $url = "https://api.monetbil.com/widget/v2.1/6SvXeleSgTEqVsdZQtUuQJ7BHdvIbo6b";
    $data = [
        'year_id' => $request->year_id,
        'semester_id' => $request->semester_id,
        'user_id' => \Auth::guard('student')->user()->id,
        'type_id' => $request->type,
        'amount' =>$transaction->amount

    ];

    $post = [
        'phone' => $request->phone,
        'amount' => $transaction->amount,
        'locale'   => 'en',
        'country' => 'CM',
        'user'=>Auth::guard('student')->user()->id,
        'first_name'=>Auth::guard('student')->user()->name,
        'item_ref' => json_encode($data)
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch,CURLOPT_POST, 1);    
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    $response = curl_exec($ch);
    curl_close($ch);
    $response = json_decode($response);
    if($response->success){
         $url = $response->payment_url;
         return redirect()->to($url);
    }else{
        return redirect()->back()->with("error", "Something went wrong");
    }
  }

  public function success(Request $request){
     if($request->status == "success"){
        $transaction = new \App\Transactions();
        $data = json_decode($request->item_ref);
        $transaction->amount = $data->amount;
        $transaction->year_id = $data->year_id;
        $transaction->semester_id = $data->semester_id;
        $transaction->reference = $request->transaction_id;
        $transaction->user_id = $data->user_id;
        $transaction->type_id = $data->type_id;
        $transaction->save();

        Session::flash('s','Payment Successfull');

        $year_id =  $data->year_id;
        $semester_id = $data->semester_id;
        $re['year_id'] = $year_id;
        $re['semester_id'] = $semester_id;
        if(\Auth::guard('student')->user()->studentInfo->options->department_id == 9){
            $re['results'] = \Auth::guard('student')->user()->p_result($year_id,$semester_id);
            $re['title'] = "results for ".\App\Year::find($year_id)->name."  ".\App\Semesters::find($semester_id)->byLocale()->name;
            return view('student.result.p_index')->with($re);
        }else{
            $re['results'] = \Auth::guard('student')->user()->result($year_id,$semester_id);
            $re['title'] = "results for ".\App\Year::find($year_id)->name."  ".\App\Semesters::find($semester_id)->byLocale()->name;
            return view('student.result.index')->with($re);
        }
     }else{
       Session::flash('error','Transaction not successfull');
       return redirect()->to(route('student.result.index'));
     }
  }

}
