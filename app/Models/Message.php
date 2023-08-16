<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    protected $fillable = ['year_id', 'item_id', 'recipients', 'message', 'count'];
    protected $connection = 'mysql';

}
