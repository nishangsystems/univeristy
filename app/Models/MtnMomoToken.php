<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MtnMomoToken extends Model
{
    use HasFactory;

    protected $fillable = ['access_token', 'refresh_token', 'token_type', 'product', 'expires_at'];
    protected $connection = 'mysql';
}
