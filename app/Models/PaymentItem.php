<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentItem extends Model
{
    use HasFactory;

    protected $fillable = ["name", "amount", "slug", "unit", "year_id"];

    public function payments(){
        return  $this->hasMany(Payments::class,'payment_id');
    }
}
