<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documentation extends Model
{
    use HasFactory;

    protected $table = 'documentations';
    protected $connection = 'mysql';
    protected $fillable = ['role', 'parent_id', 'title', 'content', 'permission'];
    

    public function parent()
    {
        # code...
        return $this->belongsTo(Documentation::class, 'parent_id');
    }

    public function document(Documentation $item = null)
    {
        # code...
        $_item = $item == null ? $this : $item;
        if($_item->parent_id == 0){ return $_item;}else{return $_item->document($_item->parent);}
    }

    public function children()
    {
        # code...
        return $this->hasMany(Documentation::class, 'parent_id');
    }

    public function fullname(Documentation $item = null)
    {
        # code...
        $_item = $item == null ? $this : $item;
        if($_item->parent_id == 0){return $_item->title;}
        else{return $this->fullname($_item->parent).' >> '.$_item->title;}
    }

    public function can_see()
    {
        # code...
        if(auth('student')->check() and $this->role_id == -1){return true;}
        elseif(auth()->user()->type == 'teacher' and $this->role_id == 0){return true;}
        elseif (auth()->user()->roleR()->where('role_id', $this->role_id)->count() > 0) {return true;}
        return false;
    }

}
