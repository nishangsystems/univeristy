<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    use HasFactory;

    protected $fillable = [	"payment_id","student_id","batch_id",'unit_id',"amount"];

    public function item(){
        return $this->belongsTo(PaymentItem::class, 'payment_id');
    }

    public function class(){
        return $this->belongsTo(SchoolUnits::class, 'unit_id');
    }

    public function student(){
        return $this->belongsTo(Students::class, 'student_id');
    }

    public function user(){
        return $this->belongsTo(Students::class, 'student_id');
    }
}
