<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CAResultDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'c_a_result_id',
        'student_id',
        'mark',
    ];
}
