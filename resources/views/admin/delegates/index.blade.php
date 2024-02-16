@extends('admin.layout')
@section('section')
    <div class="py-3">
        <table class="table">
            <thead class="text-capitalize">
                <th></th>
                <th>@lang('text.word_year')</th>
                <th>@lang('text.word_campus')</th>
                <th>@lang('text.word_class')</th>
                <th>@lang('text.word_student')</th>
                <th></th>
            </thead>
            <tbody></tbody>
        </table>
    </div>
@endsection