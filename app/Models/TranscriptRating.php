<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TranscriptRating extends Model
{
    use HasFactory;
    protected $fillable = ['mode', 'duration', 'current_price', 'former_price', 'user_id'];

    protected $connection = 'mysql';
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function modes(){return ['SUPER FAST MODE', 'FAST MODE', 'NORMAL MODE'];}

}
