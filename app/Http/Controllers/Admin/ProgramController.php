<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function index($parent_id){
        $data = [];
        $parent = \App\SchoolUnit::find($parent_id);
        $p_id = ($parent->p_id == 0)?$parent->id:$parent->p_id;
        $units =  \App\SchoolUnit::find($p_id)->unit();
        $data['title'] = ($units->count() == 0)?"No Sub Units Available in ".$parent->byLocale(app()->getLocale())->name:"All ". $units->first()->unitType->name;

        $data['units']  = $units;
        $data['parent_id']  = $parent_id;
        return view('admin.units.index')->with($data);

        // echo(\App\SchoolUnit::find($parent)->unitType->id);
    }

    public function store(Request $request){
        $this->validate($request, [
            'name' => 'required',
        ]);
        \DB::beginTransaction();
        try{
            $date = new DateTime();
            $slug =  str_replace("/", "", Hash::make($request->input('name').$date->format('Y-m-d H:i:s')));
            $flag = $request->input('flag');

            $unit = new \App\SchoolUnit();
            $unit->name = $request->input('name');
            $unit->language = $request->input('lang');
            $unit->slug = $slug;
            $unit->unit_id = $request->input('type');
            $unit->parent_id = $request->input('parent_id');
            if($request->flag == 1){
                $school = \App\Schools::find($request->parent_id);
            }else{
                $school = \App\SchoolUnit::find($request->parent_id);
            }
            $unit->school_id = ($school->p_id == 0)?$school->id:$school->p_id;
            $unit->description = $request->input('description');
            $unit->update_flag = '1';
            $unit->logged_by = \Auth::user()->id;
            $unit->p_id = 0;
            $unit->save();
            \DB::commit();
            return redirect()->to(route('admin.units.index',[$unit->parent_id, $flag]))->with('s', $unit->name." Added to units !");
        }catch(\Exception $e){
            \DB::rollback();
            echo($e);
            //  return redirect()->intended(route('admin.units.index'))->with('e','');
        }
    }

    public function edit(Request $request, $id){
        $lang = !$request->lang?'en':$request->lang;
        \App::setLocale($lang);
        $data['id'] = $id;
        $data['flag'] = $request->flag;
        $data['languages'] = \App\Languages::get();
        $unit = \App\SchoolUnit::find($id)->byLocale($lang);
        $data['unit'] =($unit->language != $lang)?null:$unit;
        $data['parent_id'] = \App\SchoolUnit::find($id)->parent_id;
        $data['title'] = "Edit ".$unit->name;
        $data['lang'] = !$request->input('lang') ? "en" : $request->input('lang');
        return view('admin.units.edit')->with($data);
    }

    public function create(Request $request, $parent_id){
        $data['lang'] = !$request->input('lang') ? "en" : $request->input('lang');
        \App::setLocale($data['lang']);
        $data['parent_id'] = $parent_id;
        $data['languages'] = \App\Languages::get();
        $data['flag'] = $flag;
        $parent = ($flag == 1 )?\App\Schools::find($parent_id):\App\SchoolUnit::find($parent_id);
        $data['title'] = "New Unit Under ".$parent->byLocale(app()->getLocale())->name;
        return view('admin.units.create')->with($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);
        \DB::beginTransaction();
        try{
            $lang = ($request->lang == null)?'en':$request->lang;
            $unit =\App\SchoolUnit::find($id)->byLocale($lang);
            if($unit != null && $unit->language == $request->input('lang')){
                $unit->update_flag = '2';
                $unit->save();
            }

            $school_id = $unit->school_id;
            $unit_id = $unit->unit_id;
            $parent_id = $unit->parent_id;
            $p_id = $unit->p_id;
            $slug = $unit->slug;


            $unit = new \App\SchoolUnit();
            $unit->name = $request->input('name');
            $unit->language= $request->input('lang');
            $unit->slug = $slug;
            $unit->description = $request->input('description');
            $unit->p_id = ($p_id == 0)?$id:$p_id;
            if($request->flag == 1){
                $school = \App\Schools::find($parent_id);
            }else{
                $school = \App\SchoolUnit::find($parent_id);
            }
            $unit->school_id = ($school->p_id == 0)?$school->id:$school->p_id;
            $unit->update_flag = '1';
            $unit->logged_by = \Auth::user()->id;
            $unit->unit_id = $unit_id;
            $unit->parent_id = $parent_id;
            $unit->save();
            \DB::commit();

            return redirect()->to(route('admin.units.index',[$parent_id, $request->flag]))->with('s', $unit->name." Updated !");
        }catch(\Exception $e){
            \DB::rollback();
            echo ($e);
            // return redirect()->intended(route('admin.units.index'))->with('e','');
        }

    }

    /**
     * Delete the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $slug
     * @return \Illuminate\Http\Response
     */
    public function destroy($slug)
    {
        $unit = \App\SchoolUnit::find($slug);
        $unit->update_flag = -1;
        $unit->save();
        return redirect()->to(route('admin.units.index'))->with('s', "unit deleted");
    }

}
