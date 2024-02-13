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
                @foreach ($schools as $school)
                    <tr>
                        <td>{{ $k++ }}</td>
                        <td>{{ $school->name??'--school-name--' }}</td>
                        <td>
                            <a class="btn btn-sm btn-primary rounded" href="{{ route('admin.headOfSchools.departments', $school->id) }}">@lang('text.word_departments')</a>|
                            <a class="btn btn-sm btn-success rounded" href="{{ route('admin.headOfSchools.students', $school->id) }}">@lang('text.word_students')</a>|
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection