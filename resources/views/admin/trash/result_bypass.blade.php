@extends('admin.layout')
@section('section')
    <div class="py-3">
        <table class="table table-stripped">
            <thead>
                <tr class="border-top border-bottom text-capitalize">
                    <th class="">#</th>
                    <th class="">@lang('text.word_matricule')</th>
                    <th class="">@lang('text.word_name')</th>
                    <th class="">@lang('text.word_class')</th>
                    <th class="">@lang('text.bypassed_on')</th>
                    <th class="">@lang('text.bypassed_by')</th>
                    {{-- <th class=""></th> --}}
                </tr>
            </thead>
            <tbody>
                @php
                    $k = 1;
                @endphp
                @foreach ($data as $row)
                    <tr class="border-bottom">
                        <td>{{ $k++ }}</td>
                        <td>{{ $row->matric??'' }}</td>
                        <td>{{ $row->name??'' }}</td>
                        <td>{{ $row->class->name()??'' }}</td>
                        <td>{{ $row->bypassed_at==null ? '' : $row->bypassed_at->format('d/m/Y') ?? $row->created_at }}</td>
                        <td>{{ $row->_bypassed_by->name??'' }}</td>
                        {{-- <td><a class="btn btn-sm btn-rounded btn-warning" href="{{ route('admin.trash.result_bypass.undo', $row->id) }}">@lang('text.word_undo')</a></td> --}}
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection