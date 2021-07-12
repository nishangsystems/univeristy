<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scholarship extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'amount',
        'type',
        'description',
        'status'
    ];

    /**
     * relationship between users(student) and scholarship
     */
    public function student()
    {
        return $this->belongsToMany(User::class, 'student_scholarships', 'student_id', 'scholarship_id');
    }
}
