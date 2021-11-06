<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
                $request->session()->flash('success', $request->role.' saved successfully');
            }catch(\Exception $e){
                \DB::rollback();
                $request->session()->flash('error', $e->getMessage());
            }
        }else{
            $request->session()->flash('error', "Not Permitted to perform this action");
        }
        return redirect()->to(route('admin.roles.index'));
    }

    public function destroy(Request $request, $id)
    {
        if ($request->user()->can('manage_roles')) {
            //Code goes here
        }
        return redirect()->to(route('admin.roles.index'));
    }

    public function edit(Request $request, $slug){
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
                $request->session()->flash('success', $request->role.' role saved successfully');
            }else{
                $request->session()->flash('error', 'Cant edit this role');
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
                $request->session()->flash('success', $request->role.' Role saved successfully');

            }catch(\Exception $e){
                \DB::rollback();
                $request->session()->flash('error', $e->getMessage());
            }
        }else{
            $request->session()->flash('error', 'Cant Edit this Role');
        }
        return redirect()->to(route('admin.user.index'));
    }
}
