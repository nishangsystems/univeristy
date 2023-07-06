<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matriculation extends Model
{
    use HasFactory;

    protected $table = 'matriculation';
    protected $fillable = ['pattern', 'last_number'];
    protected $connection = 'mysql';
}
