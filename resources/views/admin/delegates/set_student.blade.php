@extends('admin.layout')
@section('section')
    <div class="py-3">
        <div class="d-flex justify-content-end py-2">
            <a class="btn btn-sm btn-primary rounded text-capitalize" href="{{ route('admin.delegates.create') }}">@lang('text.add_class_delegate')</a>
        </div>
        <table class="table">
            <thead class="text-capitalize">
                <th></th>
                <th>@lang('text.word_year')</th>
                <th>@lang('text.word_campus')</th>
                <th>@lang('text.word_class')</th>
                <th>@lang('text.word_student')</th>
                <th>@lang('text.word_matricule')</th>
                <th></th>
            </thead>
            <tbody>
                
            </tbody>
        </table>
    </div>
@endsection