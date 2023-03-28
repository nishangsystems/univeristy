@extends('admin.layout')
@section('section')
@php
    $year = request('year_id') == null ? \App\Helpers\Helpers::instance()->getCurrentAccademicYear() : request('year_id');
@endphp
<div class="py-4">
    <div class="col-lg-12">
        <form method="post">
            @csrf
            <div class="row my-2">
                <label class="col-sm-3 text-capitalize">{{__('text.word_target')}}</label>
                <div class="col-sm-9">
                    <input class="form-control" value="{{$target??''}}" readonly>
                </div>
            </div>
            <div class="row my-2">
                <label class="col-sm-3 text-capitalize">{{__('text.word_audience')}}</label>
                <div class="col-sm-9">
                    <input class="form-control" name="recipients" value="{{request('recipients')??''}}" readonly>
                </div>
            </div>
            <div class="row my-2">
                <label class="col-sm-3 text-capitalize">{{__('text.word_message')}}</label>
                <div class="col-sm-9">
                    <textarea class="form-control" name="text" rows="4" required></textarea>
                </div>
            </div>
            <div class="d-flex justify-content-end my-2">
                <input type="submit" value="{{__('text.word_send')}}" class="btn btn-sm btn-primary">
            </div>
        </form>
        
    </div>
        
</div>
@endsection