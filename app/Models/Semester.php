<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'background_id', 'sem', 'program_id', 'ca_latest_date', 'exam_latest_date'];

    public function background()
    {
        # code...
        return $this->belongsTo(Background::class, 'background_id');
    }

    public function sequences()
    {
        # code...
        return $this->hasMany(Sequence::class, 'term_id');
    }

    
    public function ca_is_late()
    {
        // return false;
        # code...
        if ($this->ca_latest_date != null)
            return now()->isAfter(Carbon::createFromDate($this->ca_latest_date));
        return false;
    }

    public function exam_is_late()
    {
        // return false;
        # code...
        if ($this->exam_latest_date != null)
            return now()->isAfter(Carbon::createFromDate($this->exam_latest_date));
        return false;
    }
}
