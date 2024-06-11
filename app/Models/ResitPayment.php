<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResitPayment extends Model
{
    use HasFactory;
    protected $fillable = ['year_id', 'resit_id', 'student_id', 'amount'];
}
