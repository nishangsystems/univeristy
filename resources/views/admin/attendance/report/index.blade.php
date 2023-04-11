@extends('admin.layout')
@section('section')
    <div class="py-4">
        <form class="form bg-light py-5 px-3" method="get">
            <div class="d-flex input-group-merge rounded border">
                <input class="form-control" name="month" type="month" placeholder="{{__('text.pick_a_month')}}" required>
                <input type="submit" class="btn btn-xs btn-primary text-capitalize" value="{{__('text.word_get')}}">
            </div>
        </form>
    </div>
@endsection