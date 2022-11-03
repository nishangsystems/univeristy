<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Campus;
use App\Models\Notification;
use App\Models\ProgramLevel;

class NotificationsController extends Controller
{

    
    public function index(Request $request)
    {
        # code...
        // dd($request->query);
        $data['title'] = ($request->has('type') ? "Departmental Notifications" : '')
                        .($request->has('program_level_id') ? "Notifications For ".ProgramLevel::find(request('program_level_id'))->program()->first()->name.' : Level '.ProgramLevel::find(request('program_level_id'))->level()->first()->level : '')
                        .($request->has('campus_id') ? ' : '.Campus::find(request('campus_id'))->name.' Campus' :'');
        return view('teacher.notification.index', $data);
    }

    public function create()
    {
        # code...
        $data['title'] = (request('type') != null ? auth()->user()->classes()->first()->name : '')
                        .(request('program_level_id') != null ? ProgramLevel::find(request('program_level_id'))->program()->first()->name.' : Level '.ProgramLevel::find(request('program_level_id'))->level()->first()->level : '')
                        .(request('campus_id') != null ? Campus::find(request('campus_id'))->name : '');
        return view('teacher.notification.create', $data);
    }

    public function save(Request $request)
    {
        # code...
        $request->validate([
            'title'=>'required',
            'date'=>'required',
            'visibility'=>'required|in:general,students,teachers,admins',
            'message'=>'required'
        ]);
        try {
            //code...
            Notification::create($request->all());
            return redirect(route('notifications.index').'?'.(request('type') ? 'type='.request('type') : '').(request('program_level_id') ? 'program_level_id='.request('program_level_id') : '').(request('campus_id') ? 'campus_id='.request('campus_id') : ''))->with('success', 'Done');
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('error', 'Operation failed '.$th->getMessage());
        }
    }
    
    public function edit($id)
    {
        # code...
        $data['item'] = Notification::find($id);
        $data['title'] = 'Edit '.$data['item']->title;
        return view('teacher.notification.edit', $data);
    }
    
    public function update(Request $request, $id)
    {
        # code...
        $request->validate([
            'title'=>'required',
            'date'=>'required',
            'visibility'=>'required|in:general,students,teachers,admins',
            'message'=>'required'
        ]);
        try {
            //code...
            $not = Notification::find($id);
            $not->fill($request->all());
            $not->save();
            return redirect(route('notifications.index').'?'.(request('type') ? 'type='.request('type') : '').(request('program_level_id') ? 'program_level_id='.request('program_level_id') : '').(request('campus_id') ? 'campus_id='.request('campus_id') : ''))->with('success', 'Done');
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('error', 'Operation failed '.$th->getMessage());
        }
    }
    
    public function show($id)
    {
        # code...
        $data['item'] = Notification::find($id);
        $data['title'] = $data['item']->title;
        return view('teacher.notification.edit', $data);
    }

    public function drop(Request $request)
    {
        # code...
    }
}
