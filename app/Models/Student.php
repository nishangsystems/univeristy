<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'religion',
        'gender',
        'username',
        'dob',
        'pob',
        'batch_id',
        'password',
        'type',
        'parent_name',
        'parent_phone_number',
        'program_id'
    ];
}
