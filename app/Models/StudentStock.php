<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentStock extends Model
{
    use HasFactory;

    protected $fillable = ['student_id', 'stock_id', 'quantity', 'type', 'campus_id', 'year_id'];
    protected $table = 'student_stock';
    protected $connection = 'mysql';

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

    public function campus()
    {
        # code...
        return $this->belongsTo(Campus::class, 'campus_id');
    }

    public function year()
    {
        # code...
        return $this->belongsTo(Batch::class, 'year_id');
    }
}
