<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentStock extends Model
{
    use HasFactory;

    protected $fillable = ['student_id', 'stock_id', 'quantity', 'type', 'campus_id'];
    protected $table = 'student_stock';
}
