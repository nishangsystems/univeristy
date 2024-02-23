@extends('admin.layout')
@section('section')
    <div class="py-3">
        <div class="d-flex justify-content-end py-2 my-3">
            <a href="{{ route('admin.dep_courses.create', $department->id) }}" class="btn btn-primary rounded btn-sm">@lang('text.add_course')</a>
        </div>
        <table class="table">
            <thead class="text-capitalize">
                <th>###</th>
                <th>@lang('text.word_title')</th>
                <th>@lang('text.word_level')</th>
                <th>@lang('text.word_semester')</th>
                <th>@lang('text.word_status')</th>
                <th></th>
            </thead>
            <tbody>
                @php
                    $k = 1;
                @endphp
                @foreach($courses as $key => $course)
                    <tr>
                        <td>{{ $k++ }}</td>
                        <td>{{ $course->name??'' }}</td>
                        <td>Level {{ $course->level->level??'' }}</td>
                        <td>{{ $course->semester->name??'' }}</td>
                        <td>{{ $course->status??'' }}</td>
                        <td>
                            <a href="{{ route('admin.dep_courses.drop', $course->id) }}" class="btn btn-danger rounded btn-sm">@lang('text.word_drop')</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
@endsection
@section('script')
    
@endsection