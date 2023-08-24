<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Degree extends Model
{
    use HasFactory;

    protected $table = 'degrees';
    protected $connection = 'mysql';

    protected $fillable = ['deg_name', 'amount'];

    public function campuses()
    {
        # code...
        return $this->belongsToMany(Campus::class, CampusDegree::class);
    }

    public function certificates()
    {
        return $this->belongsToMany(Certificate::class, DegreeCertificate::class);
    }
    
}
