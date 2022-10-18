<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Income;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ImportCenter extends Controller
{
    public function import_ca()
    {
        # code...
        $data['title'] = 'Import CA';
        return view('admin.imports.import_ca', $data);
    }
    
    public function import_ca_save(Request $request)
    {
        # code...
    }

    public function import_exam()
    {
        # code...
        $data['title'] = 'Import Exams';
        return view('admin.imports.import_exam', $data);
    }
    
    public function import_exam_save(Request $request)
    {
        # code...
    }

    public function clear_ca()
    {
        # code...
        $data['title'] = 'Clear CA';
        return view('admin.imports.clear_ca', $data);
    }

    public function clear_ca_save(Request $request)
    {
        # code...
    }
    
    public function clear_exam()
    {
        # code...
        $data['title'] = 'Clear Exams';
        return view('admin.imports.clear_exam', $data);
    }

    public function clear_exam_save(Request $request)
    {
        # code...
    }

    public function clear_fee()
    {
        # code...
        $data['title'] = 'Clear Fees';
        return view('admin.imports.clear_fee', $data);
    }

    public function clear_fee_save(Request $request)
    {
        # code...
    }
}
