<?php

use App\Http\Controllers\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('endpoints', function(){
    return response()->json([
        'initiate_payment_endpoint'=>[
            'url'=>route('make_payments'),
            'method'=>'post',
            'data'=>[
                'student_id'=>'required',
                'year_id'=>'required',
                'amount'=>'required',
                'payment_id'=>'required; payment_item id for fee, income id for others payments, transcript_ratings id for transcript, resit id for resit',
                'payment_purpose'=>'required; TUTION, OTHERS, TRANSCRIPT or RESIT',
                'tel'=>'required; the account number to request for payment',
            ],
            'response'=>'transaction_id'
            ],
        'getTransactionStatus_endpoint'=>[
            'url'=>route('get_transaction_status'),
            'method'=>'post',
            'data'=>['transaction_id'=>'required; reference to the transaction, saved to the database as transaction_id'],
            'response'=>'transaction status'
        ]
    ]);
})->name('api-endpoints');
Route::post('make-payments',[TransactionController::class,'makePayments'])->name('make_payments');
// Route::get('complete-transaction',[TransactionController::class,'complete_transaction'])->name('complete_transaction');
// Route::get('failed-transaction/{transaction_id}',[TransactionController::class,'failed_transaction'])->name('failed_transaction');
Route::post('get-transaction-status',[TransactionController::class,'getTransactionStatus'])->name('get_transaction_status');
Route::post('mtn-momo',[TransactionController::class,'mtnCallBack'])->name('mtn_callback');

