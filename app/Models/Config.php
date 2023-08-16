<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    use HasFactory;

    protected $fillable = ['year_id','seq_id', 'start_date', 'end_date', 'sms_sent'];
    protected $dates =  ['start_date', 'end_date'];
    protected $connection = 'mysql';

    public function batch(){
        return $this->belongsTo(Batch::class, 'year_id');
    }

    public function sequence(){
        return $this->belongsTo(Sequence::class, 'seq_id');
    }
}
