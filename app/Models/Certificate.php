<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $table = 'certificates';
    protected $connection = 'mysql';
    protected $fillable = ['certi'];

    public function programs()
    {
        # code...
        return $this->belongsToMany(SchoolUnits::class, CertificateProgram::class, 'certificate_id', 'program_id');
    }
}

