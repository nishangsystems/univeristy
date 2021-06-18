@extends('admin.layout')
@section('title', 'Eligible incomes')
@section('section')

<div class="col-sm-12">
    <div class="col-sm-12">
        <div class="mb-3 d-flex justify-content-start">
            <h3 class="font-weight-bold">School incomes</h3>
        </div>
        <!-- <div class="  mb-3 d-flex justify-content-start">
            <a href="{{route('admin.income.create')}}" class="btn btn-primary btn-sm">Add income</a>
        </div> -->
    </div>
    <div class="content-panel">
        <div class="adv-table table-responsive">
            <table cellpadding="0" cellspacing="0" border="0" class="table" id="hidden-table-info">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Amount (CFA)</th>

                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($incomes as $k=>$income)
                    <tr>
                        <td>{{$k+1}}</td>
                        <td>{{$income->name}}</td>
                        <td>{{$income->amount}}</td>
                        <td class="d-flex justify-content-end  align-items-center">
                            <a class="btn btn-sm btn-primary m-3" href="{{route('admin.income.show',[$income->id])}}"><i class="fa fa-info-circle"> View</i></a> |
                            <a class="btn btn-sm btn-success m-3" href="{{route('admin.income.edit',[$income->id])}}"><i class="fa fa-edit"> Edit</i></a> |
                            <a onclick="event.preventDefault();
                                            document.getElementById('delete').submit();" class=" btn btn-danger btn-sm m-3"><i class="fa fa-trash"> Delete</i></a>
                            <form id="delete" action="{{route('admin.income.destroy',$income->id)}}" method="POST" style="display: none;">
                                @method('DELETE')
                                {{ csrf_field() }}
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="d-flex justify-content-end">
                {{$incomes->links()}}
            </div>
        </div>
    </div>
</div>
@endsection