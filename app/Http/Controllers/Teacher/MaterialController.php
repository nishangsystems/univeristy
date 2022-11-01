<?php

namespace App\Http\Controllers\Teacher;

use App\Models\Batch;
use App\Models\ClassMaster;
use App\Models\SchoolUnits;
use App\Models\Term;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ProgramLevel;
use App\Models\StudentClass;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use \Session;

class MaterialController extends Controller
{

    public function index(Request $request)
    {
        return view('teacher.material.index');
    }

    public function create()
    {
        # code...
        return view('teacher.material.create');
    }

    public function save(Request $request)
    {
        # code...
    }

    public function drop(Request $request)
    {
        # code...
    }
}
