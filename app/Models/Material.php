<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'school_unit_id', 'unit_id', 'file', 'campus_id', 'visibility', 'user_id', 'level_id'];
    protected $table = 'material';
    protected $connection = 'mysql';
    
    public function created_by()
    {
        # code...
        return $this->belongsTo(User::class, 'user_id');
    }

    public function schoolUnit()
    {
        # code...
        return $this->belongsTo(SchoolUnits::class, 'school_unit_id');
    }

    public function campus()
    {
        # code...
        return $this->belongsTo(Campus::class, 'campus_id');
    }

    public function level()
    {
        # code...
        return $this->belongsTo(Level::class, 'level_id');
    }
    public function audience()
    {
        if ($this->schoolUnit() == null && $this->level() == null) {
            # code...
            return 'All';
        }
        $audience = '';
        # code...
        $audience .= $this->schoolUnit() == null ? '' : $this->schoolUnit()->first()->name;
        $audience .= $this->level()->first() == null ? '' :' - Level '.$this->level()->first()->level;
        return $audience;
    }
}
