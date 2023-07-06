<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $fillable = [	"payment_id","student_id","batch_id",'unit_id',"amount","reference_number","import_reference", 'user_id', 'debt'];

    public function item(){
        return $this->belongsTo(PaymentItem::class, 'payment_id');
    }

    public function class(){
        return $this->belongsTo(ProgramLevel::class, 'unit_id');
    }

    public function student(){
        return $this->belongsTo(Students::class, 'student_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }


}
