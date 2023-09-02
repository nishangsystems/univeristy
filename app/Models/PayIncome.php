<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayIncome extends Model
{
    use HasFactory;

    protected $fillable = [
        'income_id',
        'batch_id',
        'class_id',
        'student_id',
        'user_id', 'paid_by', 'transaction_id'
    ];
    protected $connection = 'mysql';

    /**
     * relationship between payments made for an income and the income type
     */
    public function income()
    {
        return $this->belongsTo(Income::class, 'income_id');
    }

    /**
     * relationship between a class the payments mad e for an income(payincome)
     */
    public function studentClass()
    {
        return $this->belongsTo(StudentClass::class, 'student_class_id');
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class, 'batch_id');
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

    public function student()
    {
        # code...
        return $this->belongsTo(Students::class, 'student_id');
    }
}
