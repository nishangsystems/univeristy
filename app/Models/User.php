<?php

namespace App\Models;

use App\Permissions\HasPermissionsTrait;
use App\Models\UserRole;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable,  HasPermissionsTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'gender',
        'username',
        'type',
        'password',
        'campus_id',
        'school_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected function campus(){
        return $this->belongsTo(Campus::class);
    }

    public function subject()
    {
        return $this->hasMany(TeachersSubject::class, 'teacher_id');
    }

    public function subjectR($year)
    {
        return $this->hasMany(TeachersSubject::class, 'teacher_id')
            ->where('batch_id', $year)->get();
    }

    public function classR($year)
    {
        return $this->belongsToMany(SchoolUnits::class, 'teachers_subjects', 'teacher_id', 'class_id')->where('batch_id', $year)->distinct('school_units.id')->get();
    }

    public function classes()
    {
        return $this->belongsToMany(SchoolUnits::class, 'class_masters', 'user_id', 'class_id');
    }

    public function roleR()
    {
        return $this->hasMany(UserRole::class);
    }


    public function isMaster($year, $class)
    {
        return ClassMaster::where([
            'batch_id' => $year,
            'class_id' => $class,
            'user_id' => $this->id
        ])->count() > 0;
    }


}
