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
                @foreach ($programs as $prog)
                    <tr>
                        <td>{{ $k++ }}</td>
                        <td>{{ $prog->name??'--school-name--' }}</td>
                        <td>
                            <a class="btn btn-sm btn-primary rounded" href="{{ route('admin.headOfSchools.classes', [$prog->parent->parent->id, $prog->parent->id, $prog->id]) }}">@lang('text.word_classes')</a>|
                            <a class="btn btn-sm btn-success rounded" href="{{ route('admin.headOfSchools.program.students',  $prog->id) }}">@lang('text.word_students')</a>|
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection