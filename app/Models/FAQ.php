<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FAQ extends Model
{
    use HasFactory;
    protected $fillable = ['question', 'answer', 'status', 'campus_id', 'user_id',];
    protected $connection = 'mysql';

    protected $table = 'faqs';
    
    public function created_by()
    {
        # code...
        return $this->belongsTo(User::class, 'user_id');
    }

    public function campus()
    {
        # code...
        return $this->belongsTo(Campus::class, 'campus_id');
    }

    public function status()
    {
        # code...
        return $this->status == true;
    }
}
