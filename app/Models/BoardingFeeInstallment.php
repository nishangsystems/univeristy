<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoardingFeeInstallment extends Model
{
    use HasFactory;
    protected $fillable = [
        'installment_name',
        'installment_amount',
    ];


    public function boardingFee()
    {
        return $this->belongsTo(BoardingFee::class, 'boarding_fee_id');
    }

}
