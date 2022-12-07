@extends('admin.layout')
@section('section')
<div class="py-4">
    <form action="{{route('admin.stock.send', request('id'))}}" method="get">
        <div class="row my-2">
            <div class="col-md-3 col-lg-3 text-capitalize">{{__('text.word_available')}}</div>
            <div class="col-md-9 col-lg-9">
                <span class="form-control">{{\App\Models\Stock::find(request('id'))->quantity}}</span>
            </div>
        </div>
        <div class="row my-2">
            <div class="col-md-3 col-lg-3 text-capitalize">{{__('text.word_campus')}}</div>
            <div class="col-md-9 col-lg-9">
                <select name="campus_id" required id="" class="form-control">
                    <option value=""></option>
                    @foreach(\App\Models\Campus::all() as $campus)
                        <option value="{{$campus->id}}">{{$campus->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row my-2">
            <div class="col-md-3 col-lg-3 text-capitalize">{{__('text.word_quantity')}}</div>
            <div class="col-md-9 col-lg-9">
                <input type="number" name="quantity" required id="" class="form-control" min="1">
            </div>
        </div>
        <div class="d-flex justify-content-end">
            <a href="{{url()->previous()}}" class="btn btn-danger btn-sm">{{__('text.word_cancel')}}</a>|
            <button type="submit" name="" id="" class="btn btn-sm btn-primary">{{__('text.word_send')}}</button>
        </div>
    </form>
    <div class="py-3">
        <table class="table">
            <thead class="text-capitalize">
                <th>{{__('text.word_name')}}</th>
                <th>{{__('text.word_quantity')}}</th>
                <th>{{__('text.word_type')}}</th>
                <th>{{__('text.word_campus')}}</th>
                <th></th>
            </thead>
            <tbody>
                @foreach(\App\Models\Stock::find(request('id'))->transfers()->where(['type'=>'send'])->distinct()->orderBy('created_at', 'DESC')->get() as $transfer)
                <tr class="border-bottom border-light">
                    <td class="border-right border-light">{{$transfer->stock->name}}</td>
                    <td class="border-right border-light">{{$transfer->quantity}}</td>
                    <td class="border-right border-light">{{$transfer->stock->type}}</td>
                    <td class="border-right border-light">{{\App\Models\Campus::find($transfer->receiver_campus)->name ?? ''}}</td>
                    <td class="border-right border-light">
                        @if((!$transfer->stock->campusStock($transfer->receiver_campus) == null) && ($transfer->stock->campusStock($transfer->receiver_campus)->quantity >= $transfer->quantity))
                        <a href="{{Request::url()}}/cancel?record={{$transfer->id}}" class="btn btn-danger btn-sm">{{__('text.word_cancel')}}</a>
                        @else
                        <span class="btn btn-secondary btn-sm">{{__('text.word_cancel')}}</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection