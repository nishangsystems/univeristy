<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeachersSubject;
use App\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Session;

class UserController extends Controller
{
  /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request  $request)
    {
        $data['users'] = \App\Models\User::where('type', request('type','teacher'))->paginate(15);
        $data['title'] = "Manage ".request('teacher','user').'s';
        return view('admin.user.index')->with($data);
    }

    public function create(Request $request)
    {
        $data['title'] = "Add User";
        return view('admin.user.create')->with($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'phone' => 'required',
            'address' => 'nullable',
            'gender' => 'required',
            'type' => 'required',
        ]);

        $input = $request->all();
        $input['password'] = Hash::make('password');
        $input['username'] = $request->email;
        $user = \App\Models\User::create($input);

        return redirect()->to(route('admin.users.index',['type'=>$user->type]))->with('success', "User Created Successfully !");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id){
        $data['title'] = "User details";
        $data['user'] = \App\Models\User::find($id);
	    return view('admin.user.show')->with($data);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $data['title'] = "Edit user details";
        $data['user'] = \App\Models\User::find($id);
        return view('admin.user.edit')->with($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){

        $this->validate($request, [
            'name' => 'required',
            'phone' => 'required',
            'address' => 'nullable',
            'gender' => 'required',
            'type' => 'required',
        ]);
        $user = \App\Models\User::find($id);
        if(\Auth::user()->id == $id || \Auth::user()->id == 1){
            return redirect()->to(route('admin.users.index', ['type'=>$user->type]))->with('error', "User can't be updated");
        }

        $input = $request->all();
        $user->update($input);
        return redirect()->to(route('admin.users.show',[$user->id]))->with('success', "User updated Successfully !");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = \App\Models\User::find($id);
        if(\Auth::user()->id == $id || \Auth::user()->id == 1){
            return redirect()->to(route('admin.users.index', ['type'=>$user->type]))->with('error', "User can't be deleted");
        }
        $user->delete();
        return redirect()->to(route('admin.users.index',['type'=>$user->type]))->with('success', "User deleted successfully!");
    }


    public function  createSubject($id){
        $data['user'] = \App\Models\User::find($id);
        $data['title'] = "Assign Subject to ". $data['user']->name;
        return view('admin.user.assignSubject')->with($data);
    }

    public function  dropSubject($id){
       $s = TeachersSubject::find($id);
       if($s){
           $s->delete();
       }
       return redirect()->to(route('admin.users.show',$id))->with('success', "Subject deleted successfully!");

    }

    public function  saveSubject(Request $request, $id){
        $subject = TeachersSubject::where([
            'teacher_id'=>$id,
            'subject_id'=>$request->subject,
            'class_id'=>$request->section,
            'batch_id'=>\App\Helpers\Helpers::instance()->getCurrentAccademicYear()
        ]);

       if($subject->count() == 0){
           TeachersSubject::create([
               'teacher_id'=>$id,
               'subject_id'=>$request->subject,
               'class_id'=>$request->section,
               'batch_id'=>\App\Helpers\Helpers::instance()->getCurrentAccademicYear()
           ]);
           Session::flash('success', "Subject assigned successfully!");
       }else{
           Session::flash('error', "Subject assigned already");
       }

        return redirect()->to(route('admin.users.show',$id));
    }
}
