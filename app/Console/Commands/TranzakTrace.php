<?php

namespace App\Console\Commands;

use App\Http\Resources\TranzakTrace as ResourcesTranzakTrace;
use App\Models\PendingTranzakTransaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TranzakTrace extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tranzak:trace';

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
            // call webhook to verify the status of a transaction
            $request_data = [
                "tpnId"=>random_bytes(24).time(),
                "appId"=>$record->app_id,
                "eventType"=>"REQUEST.COMPLETED",
                "resourceId"=>$record->request_id,
                "webhookId"=>'WHTNDMDXJJS085ELFP3281',
                "authKey"=>'cp6{Cw_Lh>Dd{{k*lwyBieU3lTZnKRLd_Z}',
                "isSuccess"=>true,
                "url"=> "https://staging-api.tranzak.me/fapi/payment-callback/trace126765",
                "responseStatusCode"=> "200",
                "errorDiagnosis"=> "SUCCESS",
                "dispatchTime"=> $record->transaction_time
            ];
            // 
            
            Http::withBody(ResourcesTranzakTrace::collection([$request_data]), ResourcesTranzakTrace::class)->get(config('tranzak.tranzak.base').config('tranzak.tranzak.trace_transaction'));
        });
        return 0;
    }
}
