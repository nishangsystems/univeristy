<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResitPayment extends Model
{
    use HasFactory;
    protected $fillable = ['year_id', 'resit_id', 'student_id', 'amount', 'recorded_by'];
    protected $dates = ['created_at', 'updated_at'];

    public function year(){
        return $this->belongsTo(Batch::class, 'year_id');
    }

    public function resit(){
        return $this->belongsTo(Resit::class, 'resit_id');
    }

    public function student(){
        return $this->belongsTo(Students::class, 'student_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'recorded_by');
    }

}
