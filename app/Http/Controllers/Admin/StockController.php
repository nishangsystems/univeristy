<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\CampusStock;
use App\Models\Stock;
use App\Models\StockTransfers;
use App\Models\Students;
use App\Models\StudentStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Iterator;

class StockController extends Controller
{
    //
    
    public function index()
    {
        # code...
        $data['stock'] = \App\Models\Stock::all();
        $data['title'] = "Available Items";
        return view('admin.stock.index', $data);
    }


    public function campus_index()
    {
        # code...
        $data['stock'] = \App\Models\Stock::all();
        $data['title'] = "Available Items";
        return view('admin.stock.campus.index', $data);
    }

    public function create()
    {
        # code...
        $data['title'] = "Create Item";
        return view('admin.stock.create', $data);
    }

    public function save(Request $request)
    {
        // return $request->all();
        # code...
        $validate = Validator::make($request->all(), ['name'=>'required', 'type'=>'in:receivable,givable']);
        if ($validate->fails()) {
            # code...
            return back()->with('error', $validate->errors()->first());
        }
        if(Stock::where(['name'=>$request->name])->count() > 0){
            return back()->with('error', 'Item with name '.$request->name.' already exist.');
        }
        Stock::create(['name'=>$request->name, 'type'=>$request->type ?? 'givable']);
        return back()->with('success', 'Done');
    }

    public function edit(Request $request, $id)
    {
        # code...
        $data['title'] = "Edit Stock Item";
        $data['item'] = Stock::find($id);
        return view('admin.stock.edit', $data);
    }

    public function update(Request $request, $id)
    {
        # code...
        $validate = Validator::make($request->all(), ['name'=>'required', 'type'=>'in:receivable,givable']);
        if ($validate->fails()) {
            # code...
            return back()->with('error', $validate->errors()->first());
        }
        $item = Stock::find($id);
        if(!$item == null){
            $item->update($request->all());
            $item->save();
            return back()->with('success', '!Done');
        }
        else{return back()->with('error', 'Item could not be resolved');}
    }

    public function delete(Request $request, $id)
    {
        # code...
        $item = Stock::find($id);
        if (!$item == null) {
            # code...
            $item->delete();
            return redirect(route('admin.stock.index'))->with('success', '!Done');
        }
        return back()->with('error', 'Stock item/entry not found.');
    }

    public function receive(Request $request, $id)
    {
        # code...
        $data['title'] = "Receive ".Stock::find($id)->name ?? 'Item';
        return view('admin.stock.receive', $data);
    }

    public function cancel_receive(Request $request, $id)
    {
        # code...
        $record = StockTransfers::find($request->record);
        if ($record == null) {return back()->with('error', 'Record could not be resolved.');}
        $item = $record->stock;

        if($item == null){ return back()->with('error', 'Item could not be resolved.');}
        $item->quantity -= $record->quantity;
        $item->save();
        $record->delete();

        return back()->with('success', 'Done');
    }

    public function campus_receive(Request $request, $campus_id, $id)
    {
        # code...
        $data['title'] = "Receive ".Stock::find($id)->name ?? 'Item ' . ($request->has('student_id')? Students::find($request->student_id)->name : null);
        return view('admin.stock.campus.receive', $data);
    }

    public function accept(Request $request)
    {
        # code...
        try {
            //code...
            $item = Stock::find($request->id);
            if (!$item == null) {
                # code...
                $item->recieve($request->quantity);

                StockTransfers::create(['quantity'=>$request->quantity, 'user_id'=>auth()->id(), 'stock_id'=>$request->id, 'type'=>'receive']);

                return back()->with('success', 'Done');
            }
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('error', 'Operation failed. Item could not be resolved. '.$th->getMessage());
        }
    }

    public function campus_accept(Request $request, $campus_id, $id)
    {
        // update both student_stock and campus_stock
        # code...
        $validate = Validator::make($request->all(), ['quantity'=>'required', 'student_id'=>'required']);
        if($validate->fails()){
            return back()->with('error', $validate->errors()->first());
        }
        try {
            //code...

            $item = Stock::find($request->id);
            if (!$item == null) {
                // update student_stock

                // check if student already has a record for this stock item
                $stk_count = $item->studentStock(request('campus_id'))->where(['type'=>'receivable'])->where(['student_id'=>$request->student_id])->count();
                if ($stk_count > 0) {
                    # code...
                    return back()->with('error', 'Can\'t receive item more than once. Record already exist for '.Students::find($request->student_id)->name);
                }

                StudentStock::create(['stock_id'=>$id, 'student_id'=>$request->student_id, 'quantity'=>$request->quantity, 'type'=>$item->type, 'campus_id'=>Students::find($request->student_id)->campus_id ?? auth()->user()->campus_id]);
    
                // Update campus_stock
                # code...
                $cmps_item = $item->campusStock($campus_id);
                if(!$cmps_item == null){
                    $cmps_item->quantity += $request->quantity;
                    $cmps_item->save();
                }else{
                    CampusStock::create(['campus_id'=>$campus_id, 'stock_id'=>$id, 'quantity'=>$request->quantity]);
                }
                return back()->with('success', 'Done');
            }
        } catch (\Throwable $th) {
            //throw $th;
            throw $th;
            return back()->with('error', 'Operation failed. Item could not be resolved. '.$th->getMessage());
        }
    }

    public function send(Request $request, $id)
    {
        # code...
        $data['title'] = "Send ".Stock::find($id)->name ?? 'Item';
        return view('admin.stock.send', $data);
    }

    public function cancel_send(Request $request, $id)
    {
        # code...
        $record = StockTransfers::find($request->record);
        if($record==null){return back()->with('error', 'Record could not be resolved.');}
        $item = $record->stock;

        if($item==null){return back()->with('error', 'Item could not be resolved.');}
        $item->quantity += $record->quantity;
        $item->save();
        
        $campusStock = $item->campusStock($record->receiver_campus);
        if(!$campusStock==null){
            $campusStock->quantity -= $record->quantity;
            $campusStock->save();
        }
        $record->delete();
        return back()->with('success', '!Done');
    }

    public function __send(Request $request, $id)
    {
        # code...
        $validate = Validator::make($request->all(), ['campus_id'=>'required', 'quantity'=>'required|integer']);
        if($validate->fails()){
            return back()->with('error', $validate->errors()->first());
        }
        try {
            $item = Stock::find($id);
            $item->send($request->quantity, $request->campus_id);
            StockTransfers::create([
                'sender_campus'=>auth()->user()->campus_id ?? null,
                'receiver_campus'=>$request->campus_id,
                'user_id'=>auth()->id(),
                'stock_id'=>$id,
                'type'=>'send',
                'quantity'=>$request->quantity
            ]);
            return back()->with('success', '!Done');

        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function campus_giveout(Request $request, $campus_id, $id)
    {
        # code...
        $campus_stock = Stock::find($id)->campusStock($request->campus_id) ?? null;
        if($campus_stock == null){
            return back()->with('error', 'This Campus does not have selected item.');
        }elseif ($campus_stock->quantity == 0) {
            # code...
            return back()->with('error', 'Not enough items in stock.');
        }
        $data['title'] = "Give Out ".Stock::find($id)->name ?? 'Item';
        return view('admin.stock.campus.giveout', $data);
    }

    public function post_campus_giveout(Request $request, $campus_id, $id)
    {
        # code...
        $validate = Validator::make($request->all(), ['quantity'=>'required', 'student_id'=>'required']);
        if ($validate->fails()) {
            # code...
            return back()->with('error', $validate->errors()->first());
        }
        $campus_stock = Stock::find($id)->campusStock($request->campus_id) ?? null;
        if (!$campus_stock == null) {

            // check if student already has a record for this stock item
            $stk_count = Stock::find($request->id)->studentStock(request('campus_id'))->where(['type'=>'givable'])->where(['student_id'=>$request->student_id])->count();
            if ($stk_count > 0) {
                # code...
                return back()->with('error', 'Can\'t give out item more than once per student. Record already exist for '.Students::find($request->student_id)->name);
            }

            // Check if there is enough to give out
            if($campus_stock->quantity < $request->quantity){
                return back()->with('error', 'Not enough stock to give out.');
            }

            StudentStock::create(['student_id'=>$request->student_id, 'stock_id'=>$id, 'quantity'=>$request->quantity, 'type'=>Stock::find($id)->type ?? 'receivable',  'campus_id'=>Students::find($request->student_id)->campus_id ?? auth()->user()->campus_id]);
            # code...
            $campus_stock->quantity -= $request->quantity;
            $campus_stock->save();

            return back()->with('success', '!Done');
        }else {
            return back()->with('error', 'Stock item could not be resolved');
        }
        
    }

    public function restore(Request $request, $campus_id, $id)
    {
        # code...
        $data['title'] = "Restore ".Stock::find($id)->name ?? 'Item';
        $data['item'] = Stock::find($id);
        return view('admin.stock.campus.restore', $data);
    }

    public function __restore(Request $request, $campus_id, $id)
    {
        # code...
        $validate = Validator::make($request->all(), ['quantity'=>'required']);

        if ($validate->fails()) {
            # code...
            return back()->with('error', $validate->errors()->first());
        }
        $item = Stock::find($id);
        if ($item == null) {return back()->with('error', 'Item could not be resolved');}
        $campusStock = $item->campusStock($campus_id);
        if($campusStock == null){return back()->with('error', 'Nothing to restore.');}
        if($campusStock->quantity < $request->quantity ){return back()->with('error', 'Can\'t restore. Not enough items in stock.');}
        
        $campusStock->quantity -= $request->quantity;
        $campusStock->save();

        $item->quantity += $request->quantity;
        $item->save();

        StockTransfers::create(['sender_campus'=>$campus_id, 'receiver_campus'=>null, 'user_id'=>auth()->id(), 'stock_id'=>$id, 'quantity'=>$request->quantity, 'type'=>'restore']);

        return redirect(route('admin.stock.campus.index', $request->campus_id))->with('success', '!Done');
    }

    public function delete_student_stock(Request $request)
    {
        # code...
        $ss = StudentStock::find($request->id);
        $ss ? $ss->delete() : null;
        return back()->with('success', 'Done');
    }

    public function report(Request $request)
    {
        # code...
        $data['title'] = "Stock Report";
        return view('admin.stock.report', $data);
    }
    
    public function print_report(Request $request)
    {
        # code...
        $data['title'] = $request->type." Stock Report";
        return view('admin.stock.print', $data);
    }
}
