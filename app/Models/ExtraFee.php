<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtraFee extends Model
{
    use HasFactory;
    protected $fillable = ['student_id', 'year_id', 'amount'];
    protected $table = 'extra_fees';

    public function student()
    {
        # code...
        return $this->belongsTo(Students::class, 'student_id');
    }
    public function Batch()
    {
        # code...
        return $this->belongsTo(Batch::class, 'year_id');
    }
}
