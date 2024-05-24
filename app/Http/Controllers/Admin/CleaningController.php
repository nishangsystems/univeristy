<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Subjects;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CleaningController extends Controller
{
    //

    public function courses(){
        $courses = Subjects::select(['id', 'code'])->get()->each(function($rec){$rec->code = str_replace(' ', '', $rec->code);})->groupBy('code')->map(function($colc){
            return $colc->count() > 1 ? $colc : null;
        })->filter(function($row){return $});
        dd($courses);
    }
}
