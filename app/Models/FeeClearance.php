<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeClearance extends Model
{
    use HasFactory; 
    protected $table = "fee_clearance";
    
    protected $fillable = ['student_id', 'admission_year_id', 'final_year_id'];

    public function student()
    {
        # code...
        return $this->belongsTo(Students::class, 'student_id');
    }
}
