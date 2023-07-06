<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoardingFee extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount_new_student',
        'amount_old_student',
        'boarding_type',
        'parent_id'
    ];
    protected $connection = 'mysql';

    public function  boardingFeeInstallments()
    {
        return $this->hasMany(BoardingFeeInstallment::class);
    }

    public function schoolUnit()
    {
        return $this->belongsTo(SchoolUnits::class, 'boarding_type');
    }

    public function schoolUnitParent()
    {
        return $this->belongsTo(SchoolUnits::class, 'parent_id');
    }
}
