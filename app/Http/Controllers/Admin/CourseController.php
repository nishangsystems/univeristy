<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    //

    public function index()
    {
        # code...
        if(request()->has('program_id')){
            $data['title'] =  \App\Models\Program::find(request('program_id'))->name . ' courses';
            $data['courses'] = \App\Models\Program::find(request('program_id'))->courses();
        }
        else{
            $data['title'] = 'Courses';
            $data['courses'] = \App\Models\Course::all();
        }


    }
}
