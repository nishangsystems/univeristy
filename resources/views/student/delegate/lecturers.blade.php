@extends('student.layout')
@section('section')
    <div class="py-3">
        <table class="table">
            <div class="text-center fw-semibold fs-3 h3">
                @lang('text.word_lecturers')
            </div>
            <thead class="text-capitalize">
                <tr>
                    <th>###</th>
                    <th>@lang('text.word_name')</th>
                    <th>@lang('text.word_class')</th>
                    <th>@lang('text.word_subject')</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @php
                    $k = 1;
                @endphp
                @foreach($teachers as $key => $tcha)
                    <tr>
                        <td>{{ $k++ }}</td>
                        <td>{{ $tcha->user->name }}</td>
                        <td>{{ $tcha->class->name() }}</td>
                        <td>{{ $tcha->subject->name }}</td>
                        <td>
                            <a href="{{ route('student.delegate.check_in', [$tcha->id]) }}" class="btn btn-primary rounded">@lang('text.take_record')</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
@section('script')
    
@endsection