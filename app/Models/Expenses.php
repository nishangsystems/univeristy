<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expenses extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'amount_spend',
        'date',
        'user_id'//who recorded the expenses
    ];

    public function recordedBy()
    {
        return $this->belongsTo(User::class);
    }
}
