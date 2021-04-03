<?php
namespace App\Helpers;

class Helpers
{

    public function getCurrentAccademicYear()
    {
        $config = \App\Models\Config::all()->last();
       return $config->year_id;
    }

    public function getCurrentSemester()
    {
        $config = \App\Models\Config::all()->last();
          return $config->semester_id;
    }



    public static function instance()
    {
        return new Helpers();
    }
}
