<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlatformCharge extends Model
{
    use HasFactory;

    protected $fillable = ['year_id', 'yearly_amount', 'transcript_amount', 'result_amount', 'parent_amount'];
    protected $table = 'platform_charges';
    protected $connection = 'mysql';
}
