@extends('admin.layout')
@section('section')
<div class="py-3">
    <form method="POST">
        @csrf
        <div class="row my-2">
            <label class="col-md-3 text-capitalize">{{__('text.ca_dateline')}}</label>
            <div class="col-md-9">
                <input class="form-control" type="date" name="ca_dateline" value="{{$semester->ca_latest_date ?? ''}}">
            </div>
        </div>
        <div class="row my-2">
            <label class="col-md-3 text-capitalize">{{__('text.exam_dateline')}}</label>
            <div class="col-md-9">
                <input class="form-control" type="date" name="exam_dateline" value="{{$semester->exam_latest_date ?? ''}}">
            </div>
        </div>
        <div class="d-flex justify-content-end my-2">
            <input type="submit" value="{{__('text.word_save')}}" class="btn btn-sm btn-primary">
        </div>
    </form>
</div>
@endsection