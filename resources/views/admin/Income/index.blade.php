@extends('admin.layout')
@section('title', 'Eligible incomes')
@section('section')

<div class="col-sm-12">
    <div class="col-sm-12">
        <div class="form-panel">
            <form class="form-horizontal" role="form" method="POST" action="{{route('admin.income.store')}}">

                @csrf
                @include('admin.Income.form')
            </form>
        </div>
    </div>
    <div class="content-panel">
        <div class="adv-table table-responsive">
            <table cellpadding="0" cellspacing="0" border="0" class="table" id="hidden-table-info">
                <thead>
                    <tr class="text-capitalize">
                        <th>#</th>
                        <th>{{__('text.word_name')}}</th>
                        <th>{{__('text.word_amount')}} ({{__('text.currency_cfa')}})</th>

                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($incomes as $k=>$income)
                    <tr>
                        <td>{{$k+1}}</td>
                        <td>{{$income->name}}</td>
                        <td>{{number_format($income->amount)}}</td>
                        <td class="d-flex justify-content-end  align-items-center">
                            <a class="btn btn-sm btn-primary m-3" href="{{route('admin.income.show',[$income->id])}}"><i class="fa fa-info-circle"> {{__('text.word_view')}}</i></a> |
                            <a class="btn btn-sm btn-success m-3" href="{{route('admin.income.edit',[$income->id])}}"><i class="fa fa-edit"> {{__('text.word_edit')}}</i></a> |
                            <a onclick="event.preventDefault();
                                            document.getElementById('delete').submit();" class=" btn btn-danger btn-sm m-3"><i class="fa fa-trash"> {{__('text.word_delete')}}</i></a>
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