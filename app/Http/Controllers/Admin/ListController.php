<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Models\PaymentItem;
use App\Models\SchoolUnits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Session;
use Redirect;
use DB;
use Auth;

class ListController extends Controller{

    public function index(Request $request, $unit_id){
        $unit = SchoolUnits::find($unit_id);
        $data['unit'] = $unit;
        $data['lists'] = $unit->items;
        $data['title'] = "Fee Listing for ".$unit->name;
        return view('admin.fee.list.index')->with($data);
    }

    public function create(Request $request, $unit_id){
        $unit = SchoolUnits::find($unit_id);
        $data['unit'] = $unit;
        $data['title'] = "Add Fee Listing for ".$unit->name;
        return view('admin.fee.list.create')->with($data);
    }

    public function store(Request $request, $unit_id)
    {
        $this->validate($request, [
            'name' => 'required',
            'amount' => 'required',
        ]);
        try{
            \DB::beginTransaction();
            $unit = SchoolUnits::find($unit_id);
            $slug = Hash::make(now());
            $item = PaymentItem::create([
                'name'=>$request->name,
                'amount'=>$request->amount,
                'slug' => $slug,
                'unit' => $unit_id,
                'year_id' => \App\Helpers\Helpers::instance()->getCurrentAccademicYear()
            ]);

            $unit->setChildrenFee([
                'name'=>$request->name,
                'amount'=>$request->amount,
                'slug' => $slug,
                'year_id' => \App\Helpers\Helpers::instance()->getCurrentAccademicYear()
            ]);

            \Illuminate\Support\Facades\DB::commit();
            return redirect()->to(route('admin.fee.list.index', $unit_id))->with('success', "Fee saved successfully !");
        }catch(\Exception $e){
            DB::rollBack();
            echo $e;
        }
    }


    public function edit(Request $request,$unit_id, $id){
        $unit = SchoolUnits::find($unit_id);
        $data['unit'] = $unit;
        $data['item'] = PaymentItem::find($id);
        $data['title'] = "Edit Fee Listing for ".$unit->name;
        return view('admin.fee.list.edit')->with($data);
    }

    public function update(Request $request,$unit_id, $id){

        $this->validate($request, [
            'name' => 'required',
            'amount' => 'required',
        ]);
        try{
            DB::beginTransaction();
            $item = PaymentItem::find($id);
            $unit = SchoolUnits::find($unit_id);
            $slug = Hash::make(now());
            $input = [];
            $input['slug'] = $slug;
            $input['name'] =  $request->name;
            $input['amount'] = $request->amount;
            $input['slug'] = $slug;
            $input['year_id'] = Helpers::instance()->getYear();
            $item->update($input);

            DB::commit();
            return redirect()->to(route('admin.fee.list.index', $unit_id))->with('success', "Fee List Successfully !");
        }catch(\Exception $e){
            DB::rollBack();
            echo $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($unit_id, $id)
    {
       $item = PaymentItem::find($id);
      
       if($item->payments->count() > 0){
        return redirect()->to(route('admin.fee.list.index', $unit_id))->with('error', "Fee cant be deleted, some student have payed for already !");
    }

        $item->delete();
        return redirect()->to(route('admin.fee.list.index', $unit_id))->with('success', "Fee deleted successfully !");
    }


}
