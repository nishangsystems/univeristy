<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use PendingPromotionStudents;

class PendingPromotion extends Model
{
    use HasFactory;

    protected $table = 'pending_promotions';
    protected $connection = 'mysql';
    protected $fillable = [
        'from_year', 'to_year', 'from_class', 'to_class', 'type', 'students', 'user_id'
    ];

    function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    function fromYear()
    {
        # code...
        return $this->belongsTo(Batch::class, 'from_year');
    }
    function toYear()
    {
        # code...
        return $this->belongsTo(Batch::class, 'to_year');
    }
    function fromClass(){
        return $this->belongsTo(ProgramLevel::class, 'from_class');
    }
    function toClass(){
        return $this->belongsTo(ProgramLevel::class, 'to_class');
    }

    function students(){
        return $this->hasMany(PendingPromotionStudents::class, 'pending_promotion_id');
    }
}
