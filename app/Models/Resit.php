<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Resit extends Model
{
    use HasFactory;
    
    protected $fillable = ['year_id', 'background_id', 'start_date', 'end_date'];
    
    public function is_open()
    {
        return now()->isBetween($this->asDate($this->start_date), $this->asDate($this->end_date));
    }

    public function background()
    {
        # code...
        return $this->belongsTo(Background::class, 'background_id');
    }

    public function year()
    {
        # code...
        return $this->belongsTo(Batch::class, 'year_id');
    }

    public function to_string()
    {
        # code...
        return Batch::find($this->year_id)->name." Resit Registrattion For " . $this->background->background_name . " Open From ".$this->asDate($this->start_date)." To ".$this->asDate($this->end_date); 
    }
    
}
