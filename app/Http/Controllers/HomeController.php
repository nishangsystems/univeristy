<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return redirect()->to(route('login'));
    }

    public function children($parent)
    {
        $parent = \App\Models\SchoolUnits::find($parent);

        return response()->json(['array'=>$parent->unit,
           // 'name'=>$parent->unit->first()?$parent->unit->first()->type->name:'',
            'valid'=> ($parent->parent_id != 0 && $parent->unit->count() == 0)?'1':0,
            'name'=> $parent->unit->first()?($parent->unit->first()->unit->count() == 0?'section':''):'section'
            ]);
    }

}
