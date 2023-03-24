<?php

namespace App\Http\Controllers\admin;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Campus;
use App\Models\CampusStock;
use App\Models\ProgramLevel;
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
        $data['title'] = __('text.available_items');
        return view('admin.stock.index', $data);
    }
    
    
    public function campus_index()
    {
        # code...
        $data['stock'] = \App\Models\Stock::all();
        $data['title'] = __('text.available_items');
        return view('admin.stock.campus.index', $data);
    }

    public function create()
    {
        # code...
        $data['title'] = __('text.create_item');
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
            return back()->with('error', __('text.record_already_exist', ['item'=>$request->name]));
        }
        Stock::create(['name'=>$request->name, 'type'=>$request->type ?? 'givable']);
        return back()->with('success', __('text.word_done'));
    }

    public function edit(Request $request, $id)
    {
        # code...
        $data['title'] = __('text.edit_stock_item');
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
            return back()->with('success', __('text.word_done'));
        }
        else{return back()->with('error', __('text.not_found'));}
    }

    public function delete(Request $request, $id)
    {
        # code...
        $item = Stock::find($id);
        if (!$item == null) {
            # code...
            $item->delete();
            return redirect(route('admin.stock.index'))->with('success', __('text.word_done'));
        }
        return back()->with('error', __('text.not_found'));
    }

    public function receive(Request $request, $id)
    {
        # code...
        $data['title'] = __('text.receive', ['item'=>Stock::find($id)->name ?? 'Item']);
        return view('admin.stock.receive', $data);
    }

    public function cancel_receive(Request $request, $id)
    {
        # code...
        $record = StockTransfers::find($request->record);
        if ($record == null) {return back()->with('error', __('text.item_not_found', ['item'=>__('text.stock_transfer')]));}
        $item = $record->stock;

        if($item == null){ return back()->with('error', __('text.item_not_found', ['item'=>__('text.stock_item')]));}
        $item->quantity -= $record->quantity;
        $item->save();
        $record->delete();

        return back()->with('success', __('text.word_done'));
    }

    public function campus_receive(Request $request, $campus_id, $id)
    {
        # code...
        $data['title'] = __('text.receive', ['item'=>Stock::find($id)->name ?? __('text.word_item') . ($request->has('student_id')? Students::find($request->student_id)->name : null)]);
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

                return back()->with('success', __('text.word_done'));
            }
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('error', __('text.operation_failed').'. '.__('text.item_not_found', ['item'=>__('text.word_item')]));
        }
    }

    // Receive receivables from student
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
                    return back()->with('error', __('text.already_received_item_for', ['item'=>Students::find($request->student_id)->name]));
                }

                StudentStock::create(['stock_id'=>$id, 'student_id'=>$request->student_id, 'quantity'=>$request->quantity, 'type'=>$item->type, 'campus_id'=>Students::find($request->student_id)->campus_id ?? auth()->user()->campus_id, 'year_id'=>Helpers::instance()->getCurrentAccademicYear()]);
    
                // Update campus_stock
                # code...
                $cmps_item = $item->campusStock($campus_id);
                if(!$cmps_item == null){
                    $cmps_item->quantity += $request->quantity;
                    $cmps_item->save();
                }else{
                    CampusStock::create(['campus_id'=>$campus_id, 'stock_id'=>$id, 'quantity'=>$request->quantity]);
                }
                return back()->with('success', __('text.word_done'));
            }
        } catch (\Throwable $th) {
            //throw $th;
            throw $th;
            return back()->with('error',  __('text.operation_failed').'. '.__('text.item_not_found', ['item'=>__('text.word_item')]));
        }
    }

    public function send(Request $request, $id)
    {
        # code...
        $data['title'] = __('text.send_item', ['item'=>Stock::find($id)->name ?? __('text.word_item')]);
        return view('admin.stock.send', $data);
    }

    public function cancel_send(Request $request, $id)
    {
        # code...
        $record = StockTransfers::find($request->record);
        if($record==null){return back()->with('error', __('text.item_not_found', ['item'=>__('text.stock_transfer')]));}
        $item = $record->stock;

        if($item==null){return back()->with('error', __('text.item_not_found', ['item'=>__('text.stock_item')]));}
        $item->quantity += $record->quantity;
        $item->save();
        
        $campusStock = $item->campusStock($record->receiver_campus);
        if(!$campusStock==null){
            $campusStock->quantity -= $record->quantity;
            $campusStock->save();
        }
        $record->delete();
        return back()->with('success', __('text.word_done'));
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
            return back()->with('success', __('text.word_done'));

        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function campus_giveout(Request $request, $campus_id, $id)
    {
        # code...
        $campus_stock = Stock::find($id)->campusStock($request->campus_id) ?? null;
        if($campus_stock == null){
            return back()->with('error', __('text.item_not_in_campus'));
        }elseif ($campus_stock->quantity == 0) {
            # code...
            return back()->with('error', __('text.insuficient_items'));
        }
        $data['title'] = __('text.give_out_item', ['item'=>Stock::find($id)->name ?? __('text.word_item')]);
        return view('admin.stock.campus.giveout', $data);
    }

    // give out stock to sstudents
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
            $stk_count = Stock::find($request->id)->studentStock(request('campus_id'))->where(['type'=>'givable'])->where(['student_id'=>$request->student_id])->where(['year_id'=>Helpers::instance()->getCurrentAccademicYear()])->count();
            if ($stk_count > 0) {
                # code...
                return back()->with('error', __('text.already_gave_out_item_to', ['item'=>Students::find($request->student_id)->name]));
            }

            // Check if there is enough to give out
            if($campus_stock->quantity < $request->quantity){
                return back()->with('error', __('text.insuficient_items'));
            }

            StudentStock::create(['student_id'=>$request->student_id, 'stock_id'=>$id, 'quantity'=>$request->quantity, 'type'=>Stock::find($id)->type ?? 'receivable',  'campus_id'=>Students::find($request->student_id)->campus_id ?? auth()->user()->campus_id, 'year_id'=>Helpers::instance()->getCurrentAccademicYear()]);
            # code...
            $campus_stock->quantity -= $request->quantity;
            $campus_stock->save();

            return back()->with('success', __('text.word_done'));
        }else {
            return back()->with('error', __('text.item_not_found', ['item'=>__('text.stock_item')]));
        }
        
    }

    public function restore(Request $request, $campus_id, $id)
    {
        # code...
        $data['title'] = __('text.restore_item', ['item'=>Stock::find($id)->name ?? __('text.word_item')]);
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
        if ($item == null) {return back()->with('error', __('text.item_not_found', ['item'=>__('text.word_item')]));}
        $campusStock = $item->campusStock($campus_id);
        if($campusStock == null){return back()->with('error', __('text.insuficient_items'));}
        if($campusStock->quantity < $request->quantity ){return back()->with('error', __('text.insuficient_items'));}
        
        $campusStock->quantity -= $request->quantity;
        $campusStock->save();

        $item->quantity += $request->quantity;
        $item->save();

        StockTransfers::create(['sender_campus'=>$campus_id, 'receiver_campus'=>null, 'user_id'=>auth()->id(), 'stock_id'=>$id, 'quantity'=>$request->quantity, 'type'=>'restore']);

        return redirect(route('admin.stock.campus.index', $request->campus_id))->with('success', __('text.word_done'));
    }

    public function delete_student_stock(Request $request)
    {
        # code...
        $ss = StudentStock::find($request->id);
        $ss ? $ss->delete() : null;
        return back()->with('success', __('text.word_done'));
    }

    public function report(Request $request)
    {
        # code...
        $data['title'] = __('text.item_stock_report', ['item'=>Stock::find($request->id)->name]);
        return view('admin.stock.report', $data);
    }
    
    public function print_report(Request $request)
    {
        # code...
        $data['title'] = __('text.item_stock_report', ['item'=>Stock::find($request->id)->name]);
        return view('admin.stock.print', $data);
    }

    public function campus_report(Request $request, $campus_id, $id)
    {
        # code...
        $stock = Stock::find($id);
        $campus = Campus::find($campus_id);
        $data['title'] = __('text.item_campus_stock_report', ['item'=>$stock->name, 'campus'=>$campus->name]);

        $data['external_transfers'] = $stock->studentStock($campus_id)->get();
        $data['internal_transfers'] = $stock->transfers($campus_id)->where(function ($bldr) use ($campus_id) {
            $bldr->where('sender_campus', '=', $campus_id)
                ->orWhere('receiver_campus', '=', $campus_id);
        })->get();
        // dd($data);
        return view('admin.stock.campus.report', $data);
    }

    public function campus_givable_report(Request $request)
    {
        # code...
        if ($request->has('class_id') && $request->has('year_id') && $request->has('item_id')) {
            # code...
            $data['class'] = ProgramLevel::find($request->class_id);
            $data['item'] = Stock::find($request->item_id);
            $data['year'] = Batch::find($request->year_id);
            $data['title'] = "Report For " . $data['item']->name . " -- " . $data['class']->name() . " -- " . $data['year']->name;
            $data['report'] = StudentStock::where(['student_stock.year_id' => $request->year_id, 'student_stock.campus_id' => auth()->user()->campus_id, 'student_stock.type' => 'givable', 'student_stock.stock_id' => $request->item_id])
                ->join('student_classes', ['student_classes.student_id' => 'student_stock.student_id', 'student_classes.year_id' => 'student_stock.year_id'])
                ->where(['student_classes.class_id' => $request->class_id])
                ->join('students', ['students.id'=>'student_classes.student_id'])
                ->get(['student_stock.*', 'students.name as student_name', 'students.matric as student_matric', ]);
            return view('admin.stock.campus.givable_report', $data);
        }
        $data['title'] = __('text.item_stock_report', ['item'=>'']);
        return view('admin.stock.campus.report_index', $data);
        
    }

    public function campus_receivable_report(Request $request)
    {
        # code...
        if ($request->has('class_id') && $request->has('year_id') && $request->has('item_id')) {
            # code...
            $data['class'] = ProgramLevel::find($request->class_id);
            $data['item'] = Stock::find($request->item_id);
            $data['year'] = Batch::find($request->year_id);
            $data['title'] = __('text.report_for', ['item'=>$data['item']->name . " -- " . $data['class']->name() . " -- " . $data['year']->name]);
            $data['report'] = StudentStock::where(['student_stock.year_id' => $request->year_id, 'student_stock.campus_id' => auth()->user()->campus_id, 'student_stock.type' => 'receivable', 'student_stock.stock_id' => $request->item_id])
                ->join('student_classes', ['student_classes.student_id' => 'student_stock.student_id', 'student_classes.year_id' => 'student_stock.year_id'])
                ->where(['student_classes.class_id' => $request->class_id])
                ->join('students', ['students.id'=>'student_classes.student_id'])
                ->get(['student_stock.*', 'students.name as student_name', 'students.matric as student_matric', ]);
                return view('admin.stock.campus.receivable_report', $data);
            }
        $data['title'] = __('text.item_stock_report', ['item'=>'']);
        return view('admin.stock.campus.report_index', $data);
    }
}
