<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentStock extends Model
{
    use HasFactory;

    protected $fillable = ['student_id', 'stock_id', 'quantity', 'type', 'campus_id', 'year_id'];
    protected $table = 'student_stock';

    public function student()
    {
        # code...
        return $this->belongsTo(Students::class, 'student_id');
    }
    public function stock()
    {
        # code...
        return $this->belongsTo(Stock::class, 'stock_id');
    }
}
