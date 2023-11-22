<?php

namespace App\Console\Commands;

use App\Http\Controllers\Controller;
use App\Http\Resources\TranzakTrace as ResourcesTranzakTrace;
use App\Models\PendingTranzakTransaction;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TranzakTrace extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tranzaktrace';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Trace a transaction performed with tranzak';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        PendingTranzakTransaction::all()->each(function($record){
            // refresh all pending transactions
            $url = config('tranzak.tranzak.base').config('tranzak.tranzak.transaction_details').$record->requestId;
            $response = Http::withHeaders(['Access-Control-Allow-Origin'=> '*',  'Authorization' => "Bearer ". cache(config('tranzak.tranzak.resit_api_key'))])->get($url);
            if($response->successful()){
                $data = $response->collect();
                if($data->transactionStatus == "SUCCESSFUL"){
                    
                }
            }


            $payment_data = ["payment_id"=>$record->payment_id, "student_id"=>$record->student_id,"batch_id"=>$record->batch_id,'unit_id'=>$record->unit_id,"amount"=>$record->amount,"reference_number"=>$record->reference_number, 'paid_by'=>$record->paid_by, 'payment_purpose'=>$record->payment_type??$record->purpose];
            
            switch($payment_data['payment_purpose']){
                case 'TUTION': 
                    $cache_token_key = config('tranzak.tranzak.tution_token'); 
                    $tranzak_app_id = config('tranzak.tranzak.tution_app_id'); 
                    $tranzak_api_key = config('tranzak.tranzak.tution_api_key'); 
                    break;
                case 'PLATFORM': 
                    $cache_token_key = config('tranzak.tranzak.platform_token'); 
                    $tranzak_app_id = config('tranzak.tranzak.platform_app_id'); 
                    $tranzak_api_key = config('tranzak.tranzak.platform_api_key');
                    break;
                case 'RESIT': 
                    $cache_token_key = config('tranzak.tranzak.resit_token'); 
                    $tranzak_app_id = config('tranzak.tranzak.resit_app_id'); 
                    $tranzak_api_key = config('tranzak.tranzak.resit_api_key');
                    break;
                case 'TRANSCRIPT': 
                    $cache_token_key = config('tranzak.tranzak.transcript_token'); 
                    $tranzak_app_id = config('tranzak.tranzak.transcript_app_id'); 
                    $tranzak_api_key = config('tranzak.tranzak.transcript_api_key');
                    break;
                case 'OTHERS': 
                    $cache_token_key = config('tranzak.tranzak.others_token'); 
                    $tranzak_app_id = config('tranzak.tranzak.others_app_id'); 
                    $tranzak_api_key = config('tranzak.tranzak.others_api_key');
                    break;
            }
            if(cache($cache_token_key) == null){
                GEN_TOKEN:
                $response = Http::post(config('tranzak.tranzak.base').config('tranzak.tranzak.token'), ['appId'=>$tranzak_app_id, 'appKey'=>$tranzak_api_key]);
                if($response->status() == 200){
                    // cache token and token expirationtot session
                    cache([$cache_token_key => json_decode($response->body())->data->token]);
                    cache([$cache_token_key.'_expiry'=>Carbon::createFromTimestamp(time() + json_decode($response->body())->data->expiresIn)]);
                }
            }
            $url = config('tranzak.tranzak.base').config('tranzak.tranzak.transaction_details').$record->request_id;
            $response = Http::withHeaders(['Access-Control-Allow-Origin'=> '*',  'Authorization' => "Bearer ". cache(config('tranzak.tranzak.resit_api_key'))])->get($url);
            if($response->successful()){
                $data = $response->collect()->toArray;
                if($data->transactionStatus == "SUCCESSFUL" || $data->transactionStatus == "CANCELLED" || $data->transactionStatus == "FAILED" || $data->transactionStatus == "REVERSED"){
                    $req = new Request($data);
                    Controller::_hook_tranzak_complete($req, $payment_data, $payment_data['payment_purpose']);
                    return $this->hook_tranzak_complete($req, $payment_data, $payment_data['payment_purpose']);
                }
            }else{goto GEN_TOKEN;}
        });
        return 0;
    }
}
