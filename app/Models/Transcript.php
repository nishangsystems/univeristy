<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transcript extends Model
{
    use HasFactory;
    /**
     * Summary of fillable
     * @var array $fillable 
     * ['config_id':ratings id
     * 'studebt_id':student who applied for the trancript
     * 'done': boolean, if transcript is already done or not
     * 'collected': date, if student has already collected the transcript, when?
     * 'giver_id': which user gave/signed out the transcript
     * 'user_id': who validates the transcript as 'done'
     */
    protected $fillable = ['config_id', 'student_id', 'delivery_format', 'tel', 'description', 'done', 'collected', 'giver_id', 'user_id'];

    public function done_by()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function given_by()
    {
        return $this->belongsTo(User::class, 'giver_id');
    }

    public function config(){
        return $this->belongsTo(TranscriptRating::class, 'config_id');
    }

    public function student()
    {
        # code...
        return $this->belongsTo(Students::class, 'student_id');
    }

    public function is_done()
    {
        # code...
        return $this->done == true;
    }

    public function is_collected()
    {
        # code...
        return $this->collected != null;
    }

}
