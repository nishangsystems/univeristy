@extends('admin.layout')
@section('section')
    <div class="py-3">
        <table class="table">
            <thead class="text-capitalize">
                <th>@lang('text.sn')</th>
                <th>@lang('text.word_student')</th>
                <th>@lang('text.former_class')</th>
                <th>@lang('text.current_class')</th>
                <th>@lang('text.done_by')</th>
                <th>@lang('text.word_date')</th>
            </thead>
            <tbody>
                @php
                    $cnt = 1;
                @endphp
                @foreach ($changes as $change)
                    <tr class="border-top border-bottom">
                        <td>{{ $cnt++ }}</td>
                        <td>{{ $change->student->name??"STUDENT" }}</td>
                        <td>{{ $change->former_class()->first()->name() }}</td>
                        <td>{{ $change->current_class()->first()->name() }}</td>
                        <td>{{ $change->user->name }}</td>
                        <td>{{ $change->created_at->format('F dS, Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection