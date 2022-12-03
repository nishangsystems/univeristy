@extends('admin.layout')
@section('section')
<div class="py-3">
    <form action="{{route('admin.stock.update', request('id'))}}" method="get">
        <div class="row my-2">
            <label class="col-md-3 col-lg-3 text-capitalize">{{__('text.word_name')}}</label>
            <div class="col-md-9 col-lg-9">
                <input type="text" name="name" class="form-control" id="" required value="{{$item->name}}">
            </div>
        </div>
        <div class="row my-2">
            <label class="col-md-3 col-lg-3 text-capitalize">{{__('text.word_type')}}</label>
            <div class="col-md-9 col-lg-9">
                <select name="type" class="form-control text-capitalize" required id="">
                    <option value=""></option>
                    <option value="givable" {{$item->type == 'givable' ? 'selected' : ''}}>{{__('text.word_givable')}}</option>
                    <option value="receivable" {{$item->type == 'receivable' ? 'selected' : ''}}>{{__('text.word_receivable')}}</option>
                </select>
            </div>
        </div>
        <div class="d-flex justify-content-end my-2">
            <input type="submit" value="{{__('text.word_save')}}" class="btn btn-light btn-sm border-0">
        </div>
    </form>
    <div class="pt-5">
        <table class="table">
            <thead class="text-capitalize">
                <th>{{__('text.word_name')}}</th>
                <th>{{__('text.word_quantity')}}</th>
                <th class="text-primary">{{__('text.word_type')}}</th>
            </thead>
            <tbody>
                @foreach(\App\Models\Stock::orderBy('created_at', 'DESC')->get() as $item)
                <tr>
                    <td>{{$item->name}}</td>
                    <td>{{$item->quantity}}</td>
                    <td class="text-capitalize text-primary">{{$item->type}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection