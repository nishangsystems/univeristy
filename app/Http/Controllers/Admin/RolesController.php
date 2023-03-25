<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RolesController extends Controller{

    public function index(){
        $data['roles'] = \App\Models\Role::all();
        return view('admin.roles.index')->with($data);
    }

    public function show(Request $request, $slug){
        return view('admin.roles.show');
    }


    public function store(Request $request)
    {
        if(\Auth::user()->can('manage_roles')){
            $this->validate($request, [
                'name' => 'required',
                'permissions' => 'required',
            ]);
            \DB::beginTransaction();
            try{
                $role = new \App\Models\Role();
                $role->name = $request->name;
                $role->slug = str_replace(" ","_",strtolower($request->name));
                $role->save();

                foreach($request->permissions as $perm){
                    \DB::table('roles_permissions')->insert([
                        'role_id' => $role->id,
                        'permission_id'=>$perm,
                    ]);
                }
                \DB::commit();
                $request->session()->flash('success', __('text.word_done'));
            }catch(\Exception $e){
                \DB::rollback();
                $request->session()->flash('error', $e->getMessage());
            }
        }else{
            $request->session()->flash('error', __('text.operation_not_allowed'));
        }
        return redirect()->to(route('admin.roles.index'));
    }

    public function destroy(Request $request, $id)
    {
        // return $id;
        if ($request->user()->can('manage_roles')) {
            $role = Role::find($id);
            // return $role;
            // check if authenticated user is not deleting his/her role
            // dd($role->users);
            if($role->users()->where('users.id', auth()->id())->count() > 0){
                return back()->with('error', __('text.you_can_not_delete_role_phrase'));
            }
            if($role != null){
                // delete all permissions associated to the role being deleted
                foreach ($role->permissionsR as $key => $value) {
                    $value->delete();
                }
                // delete all user-roles associated to the role being deleted
                foreach ($role->users as $key => $user) {
                    # code...
                    $user->delete();
                }
                $role->delete();
            }
        }
        return back()->with('success', __('text.word_done'));
    }

    public function edit(Request $request, $slug){
        if(!auth()->user()->can('manage_permissions')){
            return redirect(route('admin.roles.index'))->with('error', __('text.operation_not_allowed'));
        }
        $data['role'] = \App\Models\Role::whereSlug($slug)->first();
        if(!$data['role']){
            abort(404);
        }
        return view('admin.roles.edit')->with($data);
    }

    public function create(Request $request){
        return view('admin.roles.create');
    }


    public function update(Request $request, $slug){
        $this->validate($request, [
            'name' => 'required',
            'permissions' => 'required',
        ]);

        \DB::beginTransaction();
        try{
            if($slug !== 'admin' || $slug !== 'teacher' || $slug !== 'parent'){
                $role = \App\Models\Role::whereSlug($slug)->first();
                $role->name = $request->name;
                $role->save();
                foreach ($role->permissionsR as $pem){
                    $pem->delete();
                }
                foreach($request->permissions as $perm){
                    \DB::table('roles_permissions')->insert([
                        'role_id' => $role->id,
                        'permission_id'=>$perm,
                    ]);
                }
                \DB::commit();
                $request->session()->flash('success', __('text.word_done'));
            }else{
                $request->session()->flash('error', __('text.can_not_edit_this_role'));
            }
        }catch(\Exception $e){
            \DB::rollback();
            $request->session()->flash('error', $e->getMessage());
        }

        return redirect()->to(route('admin.roles.index'));
    }


    public function permissions(Request $request){
        $data['permissions'] = [];
        if($request->role){
            $data['permissions'] = \App\Models\Role::whereSlug($request->role)->first()->permissions;
        }else{
            $data['permissions'] = \App\Models\Permission::all();
        }
        return view('admin.roles.permissions')->with($data);
    }

    public function rolesView(Request $request){
        $data['user'] = \App\User::whereSlug(request('user'))->first();
        return view('admin.roles.assign')->with($data);
    }

    public function rolesStore(Request $request){
        $this->validate($request, [
            'user_id' => 'required',
            'role_id' => 'required',
        ]);

        $user = \App\User::find($request->user_id);
        if(!$user->hasRole('admin')){
            $role = \App\Models\Role::find($request->role_id);
            \DB::beginTransaction();
            try{
                if($user == null || $role == null){
                    abort(404);
                }

                foreach ($user->roleR as $r){
                    $r->delete();
                }
                \DB::table('users_roles')->insert([
                    'role_id' => $role->id,
                    'user_id'=>$user->id,
                ]);
                \DB::commit();
                $request->session()->flash('success', __('text.word_done'));

            }catch(\Exception $e){
                \DB::rollback();
                $request->session()->flash('error', $e->getMessage());
            }
        }else{
            $request->session()->flash('error', __('text.can_not_edit_this_role'));
        }
        return redirect()->to(route('admin.user.index'));
    }
}
