@extends('documentation.layout')
@section('section')
    <div class="d-flex justify-content-end py-4"><a class="btn btn-xs btn-primary rounded" href="{{route('documentation.create', [request('id')])}}">{{__('text.add_child')}}</a></div>
    <div class="py-4 my-3 card col-md-9 mx-auto px-2 bg-light">
        {!! $item->content !!}
    </div>
@endsection