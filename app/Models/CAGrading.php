<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CAGrading extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'totol_mark'
    ];
}
