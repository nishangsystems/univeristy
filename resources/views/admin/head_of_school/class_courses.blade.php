@extends('admin.layout')
@section('section')
    <div class="py-3">
        <div class="card">
            <div class="card-header">
                <h4><strong class="text-primary text-uppercase">{{$title}}</strong></h4>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach ($courses as $course_group)
                        <div class="col-xl-6">
                            <div class="container-fluid rounded-lg shadow-sm p-2">
                                <table class="table">
                                    <thead class="text-uppercase">
                                        <tr>
                                            <th colspan="5"><h4 class="text-uppercase text-dark">{{$course_group->semester->name}} &rangle; &rangle; {{$course_group->semester->background->background_name}}</h4></th>
                                        </tr>
                                        <tr>
                                            <th>@lang('text.sn')</th>
                                            <th>@lang('text.course_code')</th>
                                            <th>@lang('text.course_title')</th>
                                            <th>@lang('text.credit_value')</th>
                                            <th>@lang('text.word_status')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $k = 1;
                                        @endphp
                                        @foreach ($course_group as $course)
                                            <tr>
                                                <td>{{$k++}}</td>
                                                <td>{{$course->code}}</td>
                                                <td>{{$course->name}}</td>
                                                <td>{{$course->_class_subject($class->id)->first()->coef ?? $course->coef}}</td>
                                                <td>{{$course->_class_subject($class->id)->first()->status ?? $course->status}}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection