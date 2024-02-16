<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Campus;
use App\Services\ClassDelegateService;
use Illuminate\Http\Request;

class ClassDelegateController extends Controller
{
    //
    protected $classDelegateService;
    public function __construct(ClassDelegateService $classDelegateService)
    {
        # code...
        $this->classDelegateService = $classDelegateService;
    }

    public function index()
    {
        # code...
        try{
            $data['title'] = "All Class Delegates";
            $data['delegates'] = $this->classDelegateService->getAll();
            return view('admin.delegates.index', $data);
        }catch(\Throwable $th){
            return back()->with('error', $th->getMessage());
        };
    }
        

    public function create(Request $request)
    {
        # code...
        try{
            $data['title'] = "Create Class Delegate";
            $data['campuses'] = Campus::orderBy('name')->get();
            $data['years'] = Batch::all();
            return view('admin.delegates.create', $data);
        }catch(\Throwable $th){
            return back()->with('error', $th->getMessage());
        }
    }


    public function store(Request $request)
    {
        # code...
        try{
            $delegate = $this->classDelegateService->store($request->all());
            $data['title'] =  "Create Class Delegate for ".$delegate->class->name().', '.$delegate->campus->name??''.' | '.$delegate->year->name??'';
            return view('admin.delegates.set_student', $data);
        }catch(\Throwable $th){
            session()->flash('error', $th->getMessage());
            return back()->withInput();
        }
    }

    public function update(Request $request, $delegate_id)
    {
        # code...
        try{
            $this->classDelegateService->update($delegate_id, $request->all());
            return redirect(route('admin.delegates.index'))->with('success', "Done");
        }catch(\Throwable $th){
            session()->flash('error', $th->getMessage());
            return back()->withInput();
        }
    }
}
