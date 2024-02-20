@extends('student.layout')
@section('section')
    <div class="py-3">
        <table class="table">
            <div class="text-center fw-semibold fs-3 h3">
                @lang('text.word_courses')
            </div>
            <thead class="text-capitalize">
                <tr>
                    <th>###</th>
                    <th>@lang('text.word_title')</th>
                    <th>@lang('text.word_code')</th>
                    <th>@lang('text.credit_value')</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @php
                    $k = 1;
                @endphp
                @foreach($courses as $key => $course)
                    <tr>
                        <td>{{ $k++ }}</td>
                        <td>{{ $course->name }}</td>
                        <td>{{ $course->code }}</td>
                        <td>{{ $course->coef }}</td>
                        <td>
                            <a href="{{ route('student.delegate.record_attendance', $course->id) }}" class="btn btn-primary rounded">@lang('text.record_hours')</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
@section('script')
    
@endsection