<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    use HasFactory;

    protected $fillable = [	"payment_id","student_id","batch_id","amount"];

    public function item(){
        return $this->belongsTo(PaymentItem::class, 'payment_id');
    }
}
