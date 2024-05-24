@extends('admin.layout')
@section('section')
    <div class="text-center alert-success py-3 my-2 text-uppercase header border-top border-bottom border-dark"><b>@lang('text.n_courses_cleaned', ['count'=>$duplicates])</b></div>
    <div class="text-center alert-warning py-3 my-2 text-uppercase header border-top border-bottom border-dark"><b>@lang('text.n_courses_dropped', ['count'=>$drops])</b></div>
    <div class="text-center alert-primary py-3 my-2 text-uppercase header border-top border-bottom border-dark"><b>@lang('text.operation_complete')</b></div>
@endsection