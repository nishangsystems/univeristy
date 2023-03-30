@extends('admin.layout')
@section('section')
@php
    $current_year = \App\Helpers\Helpers::instance()->getCurrentAccademicYear();
@endphp
<div class="py-3">
    <form method="post" class="text-capitalize" id="this_resit_form">
        @csrf
        <div class="my-2 row">
            <label class="col-sm-3 col-md-3">{{__('text.word_name')}}</label>
            <div class="col-sm-9 col-md-9">
                <input type="text" class="form-control" name="name" value="{{$resit->name??null}}" required >
            </div>
        </div>
        <div class="my-2 row">
            <label class="col-sm-3 col-md-3">{{__('text.academic_year')}}</label>
            <div class="col-sm-9 col-md-9">
                <select class="form-control" name="year_id" required>
                    <option>---</option>
                    @foreach (\App\Models\Batch::all() as $batch)
                        <option value="{{$batch->id}}" {{$resit->year_id == $batch->id ? 'selected' : ''}}>{{$batch->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="my-2 row">
            <label class="col-sm-3 col-md-3">{{__('text.word_background')}}</label>
            <div class="col-sm-9 col-md-9">
                <select class="form-control" name="background_id" required>
                    <option>---</option>
                    @foreach (\App\Models\Background::all() as $batch)
                        <option value="{{$batch->id}}" {{$resit->background_id == $batch->id ? 'selected' : ''}}>{{$batch->background_name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="my-2 row">
            <label class="col-sm-3 col-md-3">{{__('text.start_date')}}</label>
            <div class="col-sm-9 col-md-9">
                <input type="date" class="form-control" name="start_date" required value="{{$resit->start_date??''}}">
            </div>
        </div>
        <div class="my-2 row">
            <label class="col-sm-3 col-md-3">{{__('text.end_date')}}</label>
            <div class="col-sm-9 col-md-9">
                <input type="date" class="form-control" name="end_date" required value="{{$resit->end_date??''}}">
            </div>
        </div>
        <div class="my-3 d-flex justify-content-end">
            <button class="btn btn-sm btn-primary" onclick="confirm(`You are about to create a resit. Confirm to continue`) ? $('#this_resit_form').submit() : null">{{__('text.word_save')}}</button>
        </div>
    </form>
</div>
<div class="my-2">
    <table class="table">
        <thead class="bg-secondary text-light text-capitalize">
                <th class="border-left border-right border-white">#</th>
                <th class="border-left border-right border-white">{{__('text.word_name')}}</th>
                <th class="border-left border-right border-white">{{__('text.academic_year')}}</th>
                <th class="border-left border-right border-white">{{__('text.word_background')}}</th>
                <th class="border-left border-right border-white">{{__('text.start_date')}}</th>
                <th class="border-left border-right border-white">{{__('text.end_date')}}</th>
                <th class="border-left border-right border-white">{{__('text.word_status')}}</th>
                <th class="border-left border-right border-white"></th>
        </thead>
        <tbody>
            @php($k = 1)
            @foreach (\App\Models\Resit::where(['campus_id'=>auth()->user()->campus_id??null])->orderBy('id', 'DESC')->get() as $resit)
                <tr class="border-bottom border-white">
                    <td class="border-left border-right border-white">{{$k++}}</td>
                    <td class="border-left border-right border-white">{{$resit->name ?? ''}}</td>
                    <td class="border-left border-right border-white">{{$resit->year->name}}</td>
                    <td class="border-left border-right border-white">{{$resit->background->background_name}}</td>
                    <td class="border-left border-right border-white">{{date('l d-m-Y', strtotime($resit->start_date))}}</td>
                    <td class="border-left border-right border-white">{{date('l d-m-Y', strtotime($resit->end_date))}}</td>
                    <td class="border-left border-right border-white">@if($resit->is_open()) <span class="text-primary">{{__('text.word_open')}}</span> @else <span class="text-danger">{{__('text.word_closed')}}</span> @endif</td>
                    <td class="border-left border-right border-white">
                        <a class="btn btn-sm btn-primary" href="{{route('admin.custom_resit.edit', $resit->id)}}">{{__('text.word_edit')}}</a>
                        <button class="btn btn-sm btn-danger" onclick="window.location = confirm(`Your about to delete resit : {{$resit->year->name}} - {{$resit->background->background_name}} - {{$resit->start_date}} to {{$resit->end_date}}`) ? `{{route('admin.custom_resit.delete', $resit->id)}}` : null">{{__('text.word_delete')}}</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection