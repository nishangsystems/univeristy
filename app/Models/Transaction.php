<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['student_id', 'amount', 'year_id', 'tel', 'status','payment_purpose','payment_method','reference'];

    protected $table = 'transactions';
}
