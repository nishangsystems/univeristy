@extends('admin.layout')
@section('section')
    <div class="py-3">
        <table class="table">
            <thead class="text-capitalize">
                <th>###</th>
                <th>@lang('text.word_name')</th>
                <th></th>
            </thead>
            <tbody>
                @php
                    $k = 1;
                @endphp
                @foreach($departments as $key => $dep)
                    <tr>
                        <td>{{ $k++ }}</td>
                        <td>{{ $dep->name??'' }}</td>
                        <td>
                            <a href="{{ route('admin.dep_courses.courses', $dep->id) }}" class="btn btn-primary rounded btn-sm">@lang('text.word_courses')</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
@endsection
@section('script')
    
@endsection