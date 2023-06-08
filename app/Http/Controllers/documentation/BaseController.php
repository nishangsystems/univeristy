<?php

namespace App\Http\Controllers\documentation;

use App\Http\Controllers\Controller;
use App\Models\Documentation;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BaseController extends Controller
{
    //
    public function index()
    {
        # code...
        $data['title'] = "Documentations For Nishang University Platform";
        return view('documentation.index', $data);
    }

    public function show(Request $request)
    {
        # code...
        $data['title'] = Documentation::find($request->id)->fullname()??'';
        $data['item'] = Documentation::find($request->id);
        return view('documentation.show', $data);
    }

    public function create(Request $request)
    {
        # code...
        $data['title'] = "Create manual item";
        $data['parent'] = Documentation::find($request->parent??0);
        return view('documentation.create', $data);
    }
    
    public function store(Request $request)
    {
        # code...
        $validator = Validator::make($request->all(), ['role'=>'required', 'parent_id'=>'required', 'title'=>'required', 'content'=>'required']);
        if($validator->fails()){return back()->with('error', $validator->errors()->first());}
        
        // item update proper
        $item = new Documentation();
        $item->fill(['role'=>$request->role, 'permission'=>$request->permission??null, 'parent_id'=>$request->parent_id, 'title'=>$request->title, 'content'=>$request->content]);
        $item->save();
        return back()->with('success', __('text.word_done'));
    }

    public function edit(Request $request)
    {
        # code...
        $data['item'] = Documentation::find($request->id);
        $data['title'] = __('text.word_edit').' '.$data['item']->fullname();
        return view('documentation.edit', $data);
    }

    public function update(Request $request)
    {
        # code...
        $validator = Validator::make($request->all(), ['role'=>'required', 'parent_id'=>'required', 'title'=>'required', 'content'=>'required']);
        if($validator->fails()){return back()->with('error', $validator->errors()->first());}
        
        // item update proper
        $item = Documentation::find($request->id);
        if($item != null){
            $item->fill(['role'=>$request->role, 'permission'=>$request->permission??null, 'parent_id'=>$request->parent_id, 'title'=>$request->title, 'content'=>$request->content]);
            $item->save();
            return back()->with('success', __('text.word_done'));
        }
        return back()->with('error', __('text.item_not_found', ['item'=>'manual item']));
    }

    public function destroy(Request $request)
    {
        # code...
        $item = Documentation::find($request->id);
        if($item != null){
            // can only delete an item that has not child items
            if($item->children()->count() > 0){
                return back()->with('error', "Can't delete this item. It contains atleast one sub-titles.");
            }else{
                $item->delete();
            }
            return back()->with('success', __('text.word_done'));
        }
    }

    public function permission_root($slug)
    {
        # code...
        $docs = Documentation::where('permission', $slug)->where('role', 'admin')->orderBy('parent_id', 'ASC')->get();
        if($docs != null){
            $permission = Permission::where('slug', $slug)->first();
            $data['title'] = __('text.admin_user_manual')." >> ".$permission->name;
            $data['parent'] = $permission;
            $data['data'] = $docs;
            return view('documentation.sub_index', $data);
        }
        return back()->with('error', __('text.no_manual_entries_available'));
    }

    public function teacher_index($slug)
    {
        # code...
        $doc = Documentation::where('permission', $slug)->orWhere(function($query)use($slug){
            $slug == 'hod' ? $query->where('permission', 'teacher') : null;
        })->where('role', 'teacher')->orderBy('parent_id', 'ASC')->first();
        if($doc != null){
            return redirect(route('documentation.show', $doc->id));
        }
        return back()->with('error', __('text.no_manual_entries_available'));
    }
}
