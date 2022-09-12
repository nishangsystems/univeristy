<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{

    protected $fillable = [
        'name','slug',
    ];

    public function permissions() {

        return $this->belongsToMany(Permission::class,'roles_permissions');

    }

    public function permissionsR() {

        return $this->hasMany(RolePermissions::class,'role_id');

    }

    public function users() {

        return $this->belongsToMany(User::class,'users_roles');
    }

    public function byLocale()
    {
        if (\App::getLocale() == "fr") {
            $this->name = $this->name_fr != null ? $this->name_fr : $this->name;
        }
        return $this;

    }
}
