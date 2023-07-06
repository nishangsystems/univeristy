<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'title', 'message', 'unit_id', 'date', 'status', 'campus_id', 'visibility', 'school_unit_id', 'level_id'];
    protected $table = 'notifications';
    protected $connection = 'mysql';

    public function campus()
    {
        # code...
        return $this->belongsTo(Campus::class, 'campus_id');
    }

    public function schoolUnit()
    {
        # code...
        return $this->belongsTo(SchoolUnits::class, 'school_unit_id');
    }

    public function level()
    {
        # code...
        return $this->belongsTo(Level::class);
    }

    public function audience()
    {
        if ($this->schoolUnit() == null && $this->level() == null) {
            # code...
            return 'All';
        }
        $audience = '';
        # code...
        $audience .= $this->schoolUnit() == null ? '' : $this->schoolUnit()->first()->name ?? '';
        $audience .= $this->level()->first() == null ? '' :' - Level '.$this->level()->first()->level;
        return strlen($audience)==0 ? 'All' : $audience;
    }

    public function created_by()
    {
        # code...
        return $this->belongsTo(User::class, 'user_id');
    }

//     public function created_at()
//     {
//         # code...
//     }
}
