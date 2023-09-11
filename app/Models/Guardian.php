<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use Laravel\Passport\HasApiTokens;

class Guardian extends User
{
    use HasFactory, HasApiTokens;

    protected $table = 'guardian';
    protected $fillable = ['phone', 'password'];
}
