<?php
namespace App\Helpers;


use Illuminate\Contracts\Session\Session;

class Helpers
{
    public function getYear()
    {
        return session()->get('mode', $this->getCurrentAccademicYear());
    }

    public function getCurrentAccademicYear(){
        $config = \App\Models\Config::all()->last();
        return $config->year_id;
    }

    public function getCurrentSemester(){
        $config = \App\Models\Config::all()->last();
        return $config->semester_id;
    }

    public static function instance()
    {
        return new Helpers();
    }


}

