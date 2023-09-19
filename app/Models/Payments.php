<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'payments';
    protected $fillable = [	"payment_id","student_id","batch_id",'unit_id',"amount","reference_number","import_reference", 'user_id', 'debt', 'paid_by', 'transaction_id', 'payment_year_id'];

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

    public function transaction()
    {
        # code...
        if($this->paid_by == 'TRANZAK_MOMO'){
            return $this->belongsTo(TranzakTransaction::class, 'transaction_id');
        }else{
            return $this->hasOne(Transaction::class, 'payment_id');
        }
    }

}
