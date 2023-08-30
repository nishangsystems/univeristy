<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TranzakTransaction extends Model
{
    use HasFactory;

    protected $connection = "mysql";
    protected $table = 'tranzak_transactions';
    protected $fillable = ['request_id', 'amount', 'currency_code', 'purpose', 'mobile_wallet_number', 'transaction_ref', 'app_id', 'transaction_id', 'transaction_time', 'payment_method', 'payer_user_id', 'payer_name', 'payer_account_id', 'merchant_fee', 'merchant_account_id', 'net_amount_recieved'];

    
}
