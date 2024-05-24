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
        $courses = Subjects::select(['id', 'code', DB::raw('SELECT COUNT(*) as recs')])->groupBy('code')->having('recs', '>', 1)->get();
        dd($courses);
    }
}
