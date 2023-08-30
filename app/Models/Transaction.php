<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $fillable = ['student_id', 'amount', 'year_id', 'tel', 'status','payment_purpose','payment_method','reference', 'transaction_id', 'payment_id', 'financialTransactionId', 'used', 'is_charges', 'semester_id'];

    protected $table = 'transactions';

    public function year()
    {
        # code...
        return $this->belongsTo(Batch::class, 'year_id');
    }

    public function student()
    {
        # code...
        return $this->belongsTo(Students::class, 'student_id');
    }

}
