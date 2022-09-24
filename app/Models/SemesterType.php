<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SemesterType extends Model
{
    use HasFactory;
     protected $fillable = ['background_name'];

     protected $table = 'backgrounds';
}
