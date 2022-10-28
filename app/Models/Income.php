<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',

        'amount',

    ];

    /**
     * relationship between student and income
     */
    public function students()
    {
        return $this->belongsToMany(Students::class);
    }

    /**
     * relationship between an income and payments on the income
     */
    public function payIncomes()
    {
        return $this->hasMany(PayIncome::class, 'income_id');
    }
}
