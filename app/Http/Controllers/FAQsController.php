<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\ClassMaster;
use App\Models\SchoolUnits;
use App\Models\Term;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Campus;
use App\Models\FAQ;
use App\Models\Material;
use App\Models\ProgramLevel;
use App\Models\StudentClass;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use \Session;

class FAQsController extends Controller
{

    public function index(Request $request)
    {
        // base64_encode() && base64_decode() will be used to handle query strings
        $data['title'] = "FAQs";
        $cmps = auth('student')->check() ? auth('student')->user()->campus_id ?? 0 : auth()->user()->campus_id ?? 0;

        // return $cmps;
        $data['faqs'] = FAQ::where(function($q) use($cmps){
                $cmps == 0 ? null : $q->where(['campus_id'=>$cmps]);
            })->where(function($q){
                auth('student')->check() ? $q->where('status','=', 1) : null;
            })->orderBy('created_at', 'DESC')->get();
        return auth('student')->check() ? view('student.faqs.index', $data) : view('admin.faqs.index', $data);
    }

    public function publish(Request $request, $id)
    {
        # code...
        $faq = FAQ::find($id);
        $faq->status = ($faq->status + 1)%2;
        $faq->save();
        return back()->with('success', 'Done');
    }

    public function create()
    {
        # code...
        $data['title'] = "Create FAQ";
        return view('admin.faqs.create', $data);
    }

    public function save(Request $request)
    {
        // return $request->file('file')->getClientOriginalExtension();
        $validate = Validator::make($request->all(), [
            'question'=>'required',
            'answer'=>'required',
        ]);

        if ($validate->fails()) {
            # code...
            return back()->with('error', $validate->errors()->first());
        }
        try {
            FAQ::create([
                'question'=>$request->question,
                'answer'=>$request->answer,
                'user_id'=>auth()->id(),
                'campus_id'=>auth()->user()->campus_id ?? '',
            ]);
            return redirect(route('faqs.index'))->with('success', 'Done');

        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('error', 'Operation failed. '.$th->getMessage());
        }
    }

    public function edit($id)
    {
        # code...
        $data['item'] = FAQ::find($id);
        $data['title'] = 'Edit FAQ.';
        return view('admin.faqs.edit', $data);
    }
    
    public function update(Request $request, $id)
    {
        // return $request->file('file')->getClientOriginalExtension();
        $validate = Validator::make($request->all(), [
            'question'=>'required',
            'answer'=>'required',
        ]);

        if ($validate->fails()) {
            # code...
            return back()->with('error', $validate->errors()->first());
        }
        try {
            FAQ::find($id)->update([
                'question'=>$request->question,
                'answer'=>$request->answer,
                'user_id'=>auth()->id(),
                'campus_id'=>auth()->user()->campus_id ?? '',
            ]);
            return redirect(route('faqs.index'))->with('success', 'Done');

        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('error', 'Operation failed. '.$th->getMessage());
        }
    }
    
    public function show($id)
    {
        # code...
        $data['item'] = FAQ::find($id);
        $data['title'] = $data['item']->title;
        return auth('student')->check() ? view('student.faqs.show', $data) : view('admin.faqs.show', $data);
    }

    public function drop(Request $request, $id)
    {
        # code...
        $material = FAQ::find($id);
        $material->delete();
        return back()->with('success', 'Done');
    }
}
