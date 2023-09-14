@extends('admin.layout')
@section('section')
@php
    $year = request()->has('year_id') ? request('year_id') : \App\Helpers\Helpers::instance()->getCurrentAccademicYear();
    $charges = \App\Models\PlatformCharge::where(['year_id'=>$year])->first();
@endphp
<div class="py-4">


    <div class="my-3 px-3">
        <form method="get">
            <div class="my-4 input-group-merge d-flex border border-dark rounded">
                <span class="col-sm-2 input-group-text text-uppercase">{{__('text.word_year')}}</span>
                <select class="form-control" name="year_id" required>
                    <option></option>
                    @foreach (\App\Models\Batch::all() as $batch)
                        <option value="{{$batch->id}}" {{$batch->id == $year ? 'selected' : ''}}>{{$batch->name}}</option>
                    @endforeach
                </select>

                <button class="btn btn-sm btn-primary col-sm-2 text-capitalize" type="submit">{{__('text.word_get')}}</button>
            </div>
        </form>
    </div>

    <form method="post" enctype="multipart/form-data" class="mt-5">
        @csrf
        <input type="hidden" name="year_id" value="{{$year}}">
        <div class="row mx-auto my-2 text-capitalize">
            <span class="text-sm fw-bolder col-sm-3 col-md-2">{{__('text.platform_charges')}}</span>
            <div class="col-sm-9 col-md-10">
                <input type="number" name="yearly_amount" value="{{$charges->yearly_amount ?? ''}}" class="form-control text-uppercase">
            </div>
        </div>
        <div class="row mx-auto my-2 text-capitalize">
            <span class="text-sm fw-bolder col-sm-3 col-md-2">{{__('text.parent_platform_charges')}}</span>
            <div class="col-sm-9 col-md-10">
                <input type="number" name="parent_amount" value="{{$charges->parent_amount ?? ''}}" class="form-control text-uppercase">
            </div>
        </div>
        <div class="row mx-auto my-2 text-capitalize">
            <span class="text-sm fw-bolder col-sm-3 col-md-2">{{__('text.results_chages')}}</span>
            <div class="col-sm-9 col-md-10">
                <input type="number" name="result_amount" value="{{$charges->result_amount ?? ''}}" class="form-control text-uppercase">
            </div>
        </div>
        <div class="row mx-auto my-2 text-capitalize">
            <span class="text-sm fw-bolder col-sm-3 col-md-2">{{__('text.transcript_charges')}}</span>
            <div class="col-sm-9 col-md-10">
                <input type="number" name="transcript_amount" value="{{$charges->transcript_amount ?? ''}}" class="form-control text-uppercase">
            </div>
        </div>
        <div class="d-flex justify-content-end my-2 px-3">
            <input type="submit" class="btn btn-sm btn-primary" value="{{__('text.word_save')}}">
        </div>
    </form>
</div>
@endsection