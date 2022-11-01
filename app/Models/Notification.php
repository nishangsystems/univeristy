<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'name', 'message', 'date', 'status', 'campus_id', 'department_id', 'program_id', 'level_id'];
    protected $table = 'notifications';

    public function campus()
    {
        # code...
        return $this->belongsTo(Campus::class, 'campus_id');
    }

    public function department()
    {
        # code...
        return $this->belongsTo(SchoolUnits::class, 'department_id');
    }

    public function program()
    {
        # code...
        return $this->belongsTo(SchoolUnits::class, 'program_id');
    }

    public function level()
    {
        # code...
        return $this->belongsTo(Level::class);
    }

    public function audience()
    {
        $audience = '';
        # code...
        $audience .= $this->department() == null ? '' : $this->department()->first()->name;
        $audience .= $this->program() == null ? '' : ' - '.$this->program()->first()->name;
        $audience .= $this->level() == null ? '' : ' - '.$this->level()->first()->level;
    }

    public function created_by()
    {
        # code...
        return $this->belongsTo(User::class);
    }
}
