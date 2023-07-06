<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificateProgram extends Model
{
    use HasFactory;

    protected $fillable = ['certificate_id', 'program_id'];
    protected $connection = 'mysql';
    protected $table = 'certificate_programs';

    public function program()
    {
        # code...
        return $this->belongsTo(SchoolUnits::class, 'program_id');
    }

    public function certificate()
    {
        # code...
        return $this->belongsTo(Certificate::class, 'certificate_id');
    }
}
