@extends('admin.layout')
@section('section')
<div class="py-3">
    <form method="get">
        @csrf
        <div class="input-group border border-secondary rounded my-3 text-capitalize">
            <span class="input-group-text bg-secondary text-light fw-bolder col-sm-3 col-md-2">{{__('text.word_background')}}</span>
            <select name="background" id="" class="form-control text-uppercase" required>
                <option value=""></option>
                @foreach(\App\Models\Background::all() as $bg)
                    <option value="{{$bg->id}}" {{request('background') == $bg->id ? 'selected' : ''}}>{{$bg->background_name}}</option>
                @endforeach
            </select>
            <input type="submit" value="{{__('text.word_semesters')}}">
        </div>
    </form>
    
    @if(request()->has('background'))
    <form method="post" class=" py-5 text-capitalize">
        @csrf
        <div class="input-group border border-secondary rounded my-3">
            <span class="input-group-text bg-light text-dark fw-bolder col-sm-3 col-md-2">{{__('text.word_semester')}}</span>
            <select name="semester" id="" class="form-control text-uppercase" required>
                <option value=""></option>
                @foreach(\App\Models\Semester::where(['background_id'=>request('background')])->get() as $bg)
                    <option value="{{$bg->id}}">{{$bg->name}}</option>
                @endforeach
            </select>
        </div>
        <div class="input-group border border-secondary rounded my-3">
            <span class="input-group-text bg-light text-dark fw-bolder col-sm-3 col-md-2">{{__('text.word_date')}}</span>
            <input type="date" name="date" id="" class="form-control" required>
        </div>
        <div class="d-flex justify-content-end my-3">
            <input type="submit" name="" id="" class="btn btn-primary btn-sm" value="{{__('text.word_save')}}">
        </div>
    </form>
    @endif
</div>
@endsection