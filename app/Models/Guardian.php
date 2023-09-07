<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;

class Guardian extends User
{
    use HasFactory;

    protected $table = 'guardian';
    protected $fillable = ['phone', 'password'];

    public function platformCharges()
    {
        # code...
        return $this->hasMany(Charge::class, 'student_id')->where('parent', 1);
    }

    public function children()
    {
        # code...
        return Students::where('parent_phone_number', $this->phone)->get();
    }
}
