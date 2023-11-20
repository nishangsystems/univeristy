<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendingTranzakTransaction extends Model
{
    use HasFactory;

    protected $fillable = ["request_id","amount","currency_code","description","transaction_ref","app_id", 'transaction_time'];
}
