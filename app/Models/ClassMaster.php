<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassMaster extends Model
{
    use HasFactory;
    protected $connection = 'mysql';

    public function class(){
        return  $this->belongsTo(SchoolUnits::class, 'department_id');
    }

    public function user(){
        return  $this->belongsTo(User::class, 'user_id');
    }

    public function batch(){
        return $this->belongsTo(Batch::class, 'batch_id');
    }
}
