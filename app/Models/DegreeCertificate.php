<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DegreeCertificate extends Model
{
    use HasFactory;

    protected $table = 'degree_certificates';
    protected $connection = 'mysql';
    protected $fillable = ['degree_id', 'certificate_id'];

    public function degree()
    {
        # code...
        return $this->belongsTo(Degree::class);
    }

    public function certificate()
    {
        # code...
        return $this->belongsTo(Certificate::class);
    }
}
