@extends('admin.layout')
@section('section')
<div class="py-4">
    <table class="table">
        <thead class="text-capitalize">
            <th>{{__('text.word_name')}}</th>
            <th>{{__('text.word_quantity')}}</th>
            <th>{{__('text.word_type')}}</th>
            <th></th>
        </thead>
        <tbody>
            @if(auth()->user()->campus_id == null)
                @foreach($stock as $item)
                <tr>
                    <td>{{$item->name}}</td>
                    <td>{{$item->quantity}}</td>
                    <td>{{$item->type}}</td>
                    <td>
                        @if(!$item->type == 'receivaable')
                            <a href="{{route('admin.stock.receive', $item->id)}}" class="btn btn-sm btn-primary">{{__('text.word_receive')}}</a>|
                            <a href="{{route('admin.stock.share', $item->id)}}" class="btn btn-sm btn-warning">{{__('text.word_send')}}</a>|
                        @endif
                        <a href="{{route('admin.stock.edit', $item->id)}}" class="btn btn-sm btn-success">{{__('text.word_edit')}}</a>|
                        <a href="{{route('admin.stock.report', [$item->id])}}" class="btn btn-sm btn-success">{{trans_choice('text.word_report', 1)}}</a>|
                        <a href="{{route('admin.stock.delete', $item->id)}}" class="btn btn-sm btn-danger" onclick="event.preventDefault(); delete_alert(event, {{$item->name}})">{{__('text.word_delete')}}</a>
                    </td>
                </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>
@endsection