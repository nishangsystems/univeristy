<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Batch;
use App\Services\ManageSchoolDebtsService;

class SchoolDebtsController extends Controller
{
    private $manage_school_debt_service;

    public function __construct(ManageSchoolDebtsService $manage_school_debt_service)
    {
        $this->years = Batch::all();
        $this->manage_school_debt_service = $manage_school_debt_service;
    }
    public function index()
    {
        $data['years'] = $this->years;
        $data['schoolUnits'] = $this->manage_school_debt_service->getSchoolUnits();
        return view('admin.school_debts.index')->with($data);
    }

    public function getStudentsWithDebts()
    {
        return view('admin.school_debts.index');
    }

    public function getStudentDebts($id)
    {

    }

    public function collectStudentDebts($id)
    {

    }
}
