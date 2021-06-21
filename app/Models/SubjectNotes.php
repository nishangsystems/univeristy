<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubjectNotes extends Model
{
    use HasFactory;
    protected $fillable = [
        'subject_id',
        'note_name',
        'note_path'
    ];

    /**
     * relationshipe between subject and notes
     */
    public function subject()
    {
        return $this->belongsTo(Subjects::class);
    }
}
