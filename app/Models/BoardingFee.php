<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoardingFee extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount_new_student',
        'amount_old_student'
    ];
}
