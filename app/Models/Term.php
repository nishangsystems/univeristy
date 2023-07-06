<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    public function sequences(){
        return $this->hasMany(Sequence::class, 'term_id');
    }

}
