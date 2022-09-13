<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DegreeSemester extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'degree_id'
    ];

    public function degree()
    {
        return $this->belongsTo(Degree::class);
    }
}