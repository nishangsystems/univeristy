@extends('admin.layout')

@section('section')

<div class="col-sm-12">
    <div class="col-sm-12 mb-5">
        @include("admin.expense.create")
    </div>
    <div class="content-panel">
        <div class="adv-table table-responsive">
            <table cellpadding="0" cellspacing="0" border="0" class="table" id="hidden-table-info">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Amount Spend(CFA)</th>
                        <th>Date</th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expenses as $k=>$expense)
                    <tr>
                        <td>{{$k+1}}</td>
                        <td>{{$expense->name}}</td>
                        <td>{{number_format($expense->amount_spend)}}</td>
                        <td>{{date('jS F Y', strtotime($expense->date))}}</td>
                        <td class="d-flex justify-content-end  align-items-center">

                            <a class="btn btn-sm btn-success m-3" href="{{route('admin.expense.edit',[$expense->id])}}"><i class="fa fa-edit"> Edit</i></a> |
                            <a onclick="event.preventDefault();
                                            document.getElementById('delete').submit();" class=" btn btn-danger btn-sm m-3"><i class="fa fa-trash"> Delete</i></a>
                            <form id="delete" action="{{route('admin.expense.destroy',$expense->id)}}" method="POST" style="display: none;">
                                @method('DELETE')
                                {{ csrf_field() }}
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="d-flex justify-content-end">
                {{$expenses->links()}}
            </div>
        </div>
    </div>
</div>
@endsection