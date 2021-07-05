<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoardingAmount extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount_payable',
        'total_amount',
        'status',
        'collect_boarding_fee_id',
        'balance'
    ];

    public function collect_boarding_fee()
    {
        return $this->belongsTo(CollectBoardingFee::class, 'collect_boarding_fee_id');
    }
}
