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
        'pay_online',
        'year_id'
    ];
    protected $connection = 'mysql';

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

    public function year()
    {
        # code...
        return $this->belongsTo(Batch::class, 'year_id');
    }
}
