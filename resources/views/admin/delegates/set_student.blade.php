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
                @php
                    $k = 1;
                @endphp
                @foreach ($delegates as $delegate)
                    <tr>
                        <td>{{ $k++ }}</td>
                        <td>{{ $delegate->year->name }}</td>
                        <td>{{ $delegate->campus->name }}</td>
                        <td>{{ $delegate->class->name }}</td>
                        <td>{{ $delegate->student->name }}</td>
                        <td>{{ $delegate->student->matric }}</td>
                        <td>
                            <form method="post" action="{{ route('admin.delegates.update', $delegate->id) }}">
                                @csrf
                                <input type="hidden" name="status", value="{{ $delegate->status == 1 ? 0 : 1 }}">
                                <button type="submit" class="btn btn-sm rounded btn-warning">@lang('text.change_status')</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection