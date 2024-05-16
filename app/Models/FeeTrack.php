<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeTrack extends Model
{
    use HasFactory;

    protected $connection = "tracking_db";
    protected $table = 'fee_tracks';
    protected $fillable = ['payment_id', 'amount', 'class_id', 'batch_id', 'matric', 'student', 'student_id', 'action', 'actor', 'reason'];

    public function payment(){
        return $this->belongsTo(Payments::class, 'payment_id');
    }

    public function class(){
        return $this->belongsTo(ProgramLevel::class, 'class_id');
    }

    public function year(){
        return $this->belongsTo(Batch::class, 'batch_id');
    }

    public function student(){
        return $this->belongsTo(Students::class, 'student_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'actor');
    }
}
