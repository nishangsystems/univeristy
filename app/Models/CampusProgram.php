<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampusProgram extends Model
{
    use HasFactory;

    protected $fillable = ['campus_id', 'program_level_id', 'fees', 'max-credit'];

    public function program_level()
    {
        return $this->belongsTo(ProgramLevel::class);
    }

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function payment_items()
    {
        return $this->hasMany(PaymentItem::class);
    }

}

