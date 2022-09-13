<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'amount',
        'program_id',
        'batch_id'
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
