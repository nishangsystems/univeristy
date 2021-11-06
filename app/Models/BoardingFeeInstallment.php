<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoardingFeeInstallment extends Model
{
    use HasFactory;
    protected $fillabe = [
        'installment_name',
        'installment_amount',
        'boarding_fee_id'
    ];


    public function boardingFee()
    {
        return $this->belongsTo(BoardingFee::class);
    }

}
