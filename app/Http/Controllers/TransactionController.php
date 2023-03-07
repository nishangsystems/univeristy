<?php

namespace App\Http\Controllers;

use App\Exceptions\CollectionRequestException;
use App\Helpers\Helpers;
use App\Models\Transaction;
use App\MomoapiProducts\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

class TransactionController extends Controller
{
    public function paymentForm()
    {
        return view('transaction.transaction_form'); // TODO: Change the autogenerated stub
    }

    public static function makePayments(Request $request)
    {

        /**
         * Algorithm
         * 1- Validate input request
         * 2- initiate transaction by calling requestToPay on collection instance
         * 3- Update transactions table with request data and set the status to pending
         * 3- Send response to the user,the response can either be an error or transaction id
         * 4- use the transaction id to check transaction status
         */


        // if ($request->tel == '') {
        //     throw new \Error("Phone number is required", 400);
        // }
        // if (strlen($request->tel) != 9) {
        //     throw new \Error("Phone number must be 9 digits", 400);

        // }
        // if (!$request->amount) {
        //     throw new \Error("Amount is required", 400);
        // }

        // if (!$request->redirect_route) {
        //     throw new \Error("Redirect route is required", 400);
        // }

        // if (!$request->student_id) {
        //     throw new \Error("Student Id is required", 400);
        // }
        // if (!$request->year_id) {
        //     throw new \Error("Year Id is required", 400);
        // }
//        $validator = Validator::make($request->all(), [
//            'tel'=>'required|numeric|min:9',
//            'amount'=>'required|numeric',
//            // 'redirect_route'=>'required|url',
//            'student_id'=>'required|numeric',
//            'year_id'=>'required|numeric',
//            'payment_purpose'=>'required',
//            'payment_id'=>'required|numeric'
//        ]);
//
//        if ($validator->fails()) {
//            # code...
//            return redirect(url()->previous())->with('error', $validator->errors()->first());
//        }

//        dd($request);

        //todo: remove try catch before pushing to life
        try {

            $collection = new Collection();

            $momoTransactionId = $collection->requestToPay(Uuid::uuid4()->toString(), '237' . $request->tel, $request->amount);
             dd($momoTransactionId);
            //save transaction
           $transaction = new Transaction();
           $transaction->payment_method = 'Mtn Mobile Money';
           $transaction->payment_purpose = $request->payment_purpose ?? '';
           $transaction->status = 'pending'; //pending,failed,completed
           $transaction->year_id = $request->year_id ?? Helpers::instance()->getCurrentAccademicYear();
           $transaction->amount = intval($request->amount);
           $transaction->reference = $request->reference ?? time().random_int(100000, 999999);
           $transaction->transaction_id = $momoTransactionId;
           $transaction->payment_id = $request->payment_id;
           $transaction->student_id = $request->student_id;
           $transaction->save();
           return $momoTransactionId;
        } catch (CollectionRequestException $e) {
             do {
                 printf("\n\r%s:%d %s (%d) [%s]\n\r",
                     $e->getFile(), $e->getLine(), $e->getMessage(), $e->getCode(), get_class($e));
             } while ($e = $e->getPrevious());
//            return redirect(url()->previous())->with('error', $e->getMessage());;
        }

        /**
         * Use the first return if you are making this request using php or use the second when making request using javascript
         */

//        return redirect()->route($request->redirect_route)->with('transaction_response',['transaction_id'=>$momoTransactionId , 'success'=>true,'transaction_status'=>'payment initiated']);
        // return response()->json( ['transaction_id'=>$momoTransactionId , 'success'=>true,'transaction_status'=>'payment initiated'] );
    }

    public function getTransactionStatus($transaction_id)
    {
        $collection = new Collection();
        $transaction_status = $collection->getTransactionStatus($transaction_id);
        // dd($transaction_status);
        return response()->json($transaction_status);

//        $transaction = Transaction::where('transaction_id',$transaction_id)->find();
//        if ($transaction){
//            $collection = new Collection();
//            $transaction_status =$collection->getTransactionStatus($transaction_id);
//            if ($transaction_status['status'] == 'SUCCESSFUL'){
//                //update transaction table
//                $transaction->status = 'completed';
//                $transaction->save();
//            }elseif ($transaction_status['status'] == 'FAILED'){
//                //update transaction table
//                $transaction->status = 'failed';
//                $transaction->save();
//            }
//            return response()->json(['status'=>$transaction_status['status']]);
//        }else{
//            Throw new \Error("Invalid transaction id",400);
//        }

    }


}
