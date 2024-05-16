@extends('admin.layout')
@section('section')
    <div class="py-3">
        <table class="table">
            <thead>
                <tr class="border-top border-bottom text-capitalize">
                    <th class="">#</th>
                    <th class="">@lang('text.word_matricule')</th>
                    <th class="">@lang('text.word_name')</th>
                    <th class="">@lang('text.word_class')</th>
                    <th class="">@lang('text.word_amount')</th>
                    <th class="">@lang('text.deleted_on')</th>
                    <th class="">@lang('text.deleted_by')</th>
                    <th class=""></th>
                </tr>
            </thead>
            <tbody>
                @php
                    $k = 1;
                @endphp
                @foreach ($data as $row)
                    <tr class="border-bottom">
                        <td>{{ $k++ }}</td>
                        <td>{{ $row->student->matric??'' }}</td>
                        <td>{{ $row->student->name??'' }}</td>
                        <td>{{ $row->class->name()??'' }}</td>
                        <td>{{ $row->amount??'' }}</td>
                        <td>{{ $row->deleted_at->format('d/m/Y')??'' }}</td>
                        <td>{{ $row->track == null ? null : $row->track->user->name??'' }}</td>
                        <td><a class="btn btn-sm btn-rounded btn-warning" href="{{ route('admin.trash.fee.undo', $row->id) }}">@lang('text.word_undo')</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection