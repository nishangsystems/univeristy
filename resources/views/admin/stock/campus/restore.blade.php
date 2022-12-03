@extends('admin.layout')
@section('section')
<div class="py-4">
    <form action="{{route('admin.stock.campus.return', [request('campus_id'), request('id')])}}" method="get">
        <div class="row my-2">
            <div class="col-md-3 col-lg-3 text-capitalize">{{__('text.word_quantity')}}</div>
            <div class="col-md-9 col-lg-9">
                <input type="number" name="quantity" required id="" class="form-control" min="1">
            </div>
        </div>
        <div class="d-flex justify-content-end">
            <a href="{{url()->previous()}}" class="btn btn-danger btn-sm">{{__('text.word_cancel')}}</a>|
            <button type="submit" name="" id="" class="btn btn-sm btn-primary">{{__('text.word_restore')}}</button>
        </div>
    </form>
</div>
@endsection