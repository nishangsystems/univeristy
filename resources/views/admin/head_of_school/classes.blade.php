@extends('admin.layout')
@section('section')
    @php
        $year = $helpers->getCurrentAccademicYear();
    @endphp
    <div class="my-3">
        <table class="table adv-table">
            <thead class="text-capitalize fw-semibold">
                <th>#</th>
                <th>@lang('text.word_school')</th>
                <th></th>
            </thead>
            <tbody>
                @php
                    $k = 1;
                @endphp
                @foreach ($classes as $class)
                    <tr>
                        <td>{{ $k++ }}</td>
                        <td>{{ $class->name()??'--class-name--' }}</td>
                        <td>
                            <a class="btn btn-sm btn-info rounded" href="{{ route('admin.headOfSchools.class.courses', $class->id) }}">@lang('text.word_courses')</a>|
                            <a class="btn btn-sm btn-success rounded" href="{{ route('admin.headOfSchools.class.students', $class->id) }}">@lang('text.word_students')</a>|
                            <a class="btn btn-sm btn-primary rounded" href="{{ route('admin.result.ca.upload_report', ['program_level'=>$class->id, 'year'=>$year, 'semester'=>$helpers->getSemester($class->id)->id]) }}">@lang('text.ca_upload_report')</a>|
                            <a class="btn btn-sm btn-success rounded" href="{{ route('admin.result.exam.upload_report', ['program_level'=>$class->id, 'year'=>$year, 'semester'=>$helpers->getSemester($class->id)->id]) }}">@lang('text.exam_upload_report')</a>|
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection