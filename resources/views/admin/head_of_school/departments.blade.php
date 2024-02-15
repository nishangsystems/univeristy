@extends('admin.layout')
@section('section')
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
                @foreach ($departments as $dept)
                    <tr>
                        <td>{{ $k++ }}</td>
                        <td>{{ $dept->name??'--school-name--' }}</td>
                        <td>
                            <a class="btn btn-sm btn-primary rounded" href="{{ route('admin.headOfSchools.programs',  [$dept->parent->id, $dept->id]) }}">@lang('text.word_programs')</a>|
                            <a class="btn btn-sm btn-secondary rounded" href="{{ route('admin.headOfSchools.classes',  [$dept->parent->id, $dept->id]) }}">@lang('text.word_classes')</a>|
                            <a class="btn btn-sm btn-success rounded" href="{{ route('admin.headOfSchools.department.students', $dept->id) }}">@lang('text.word_students')</a>|
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection