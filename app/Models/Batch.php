<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    public function payIncomes()
    {
        return $this->hasMany(PayIncome::class, 'batch_id');
    }

    public function collectBoardingFees()
    {
        return $this->hasMany(CollectBoardingFee::class, 'batch_id');
    }
}
