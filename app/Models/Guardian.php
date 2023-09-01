<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;

class Guardian extends User
{
    use HasFactory;

    protected $table = 'guardian';
    protected $fillable = ['phone', 'password'];
}
