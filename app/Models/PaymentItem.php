<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentItem extends Model
{
    use HasFactory;

    protected $fillable = ["name", "amount", "slug", "unit", "year_id"];
    protected $connection = 'mysql';

    public function payments(){
        return  $this->hasMany(Payments::class,'payment_id');
    }

    public function campusProgram(){
        return  $this->belongsTo(CampusProgram::class,'campus_program_id');
    }

    public function year(){
        return  $this->belongsTo(Batch::class,'year_id');
    }
}
