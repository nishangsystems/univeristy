<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Income;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class IncomeController extends Controller
{
    private $select = [
        'name',
        'amount',
        'user_id',
        'id',
    ];
    /**
     * list all incomes of a school
     * I use the is of the current authenticated admin, to get the incomes for his/her school
     */
    public function index()
    {
        $user_id = Auth::id();
        $data['incomes'] = Income::where('user_id', $user_id)->select($this->select)->paginate(5);
        $data['title'] = 'School Income';
        return view('admin.Income.index')->with($data);
    }

    /**
     * show form to create an income for a school
     */
    public function create()
    {
        $data['title'] = 'Create Other Income';
        return view('admin.Income.create')->with($data);
    }


    /**
     * store an income for a school
     * 
     * @param Illuminate\Http\Request
     * @return string
     */
    public function store(Request $request)
    {
        $this->validateData($request);
        $income = new Income();
        $income->name = $request->name;
        $income->amount = $request->amount;
        $income->user_id = Auth::id();
        $income->save();
        return redirect()->back()->with('success', 'Income saved successfully !');
    }

    /**
     * validate data to creat income
     *  @param Illuminate\Http\Request
     */
    private function validateData($request)
    {
        return $request->validate([
            'name' => 'required|max:255|string',
            'amount' => 'required|numeric',
        ]);
    }

    /**
     * show form to edit income
     * 
     */
    public function edit($id)
    {
        $data['income'] = Income::findOrFail($id);
        $data['title'] =  'Update Income';
        return view('admin.Income.edit')->with($data);
    }

    /**
     * update an income of a school
     * 
     * @param int $id
     * @param Illuminate\Http\Request
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:255|string',
            'amount' => 'required|numeric',
        ]);
        $updated_income = Income::findOrFail($id)->update($request->all());
        return  redirect()->route('admin.income.index')->with('success', 'Income updated successfully !');
    }

    /**
     * delete an income of a school
     * 
     * @param int $id
     */
    public function destroy($id)
    {
        $deleted = Income::findOrFail($id)->delete();
        return back()->with('success', 'Income deleted successfully!');
    }

    /**
     * show details of an income
     * 
     * @param int $id
     */
    public function show($id)
    {
        $data['income'] = Income::findOrFail($id);
        $data['title'] = 'Income details';
        return view('admin.Income.show')->with($data);
    }
}
