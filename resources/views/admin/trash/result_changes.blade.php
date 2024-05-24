@extends('admin.layout')
@section('section')
    <div class="py-3 container-fluid">
        <form method="GET">
            <div class="row bg-light border-top border-bottom my-4 py-3 border-secondary">
                
                <div class="col-lg-8">
                    <input type="month" class="form-control" name="month"  value="{{ request('month') }}">
                    <span class="text-capitalize text-secondary">@lang('text.select_month')<i class="text_danger">*</i></span>
                </div>
                <div class="col-lg-2">
                    <button type="submit" class="btn btn-xs rounded btn-primary px-5">@lang('text.word_next')</button>
                </div>
            </div>
        </form>
    </div>
    <div class="py-3">
        <table class="table table-stripped">
            <thead>
                <tr class="border-top border-bottom text-capitalize">
                    <th class="">#</th>
                    <th class="">@lang('text.word_matricule')</th>
                    <th class="">@lang('text.word_student')</th>
                    <th class="">@lang('text.course_code')</th>
                    <th class="">@lang('text.word_title')</th>
                    <th class="">@lang('text.word_type')</th>
                    <th class="">@lang('text.word_change')</th>
                    <th class="">@lang('text.word_date')</th>
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
                        <td>{{ $row->student->matric??'' }}</td>
                        <td>{{ $row->student->name??'' }}</td>
                        <td>{{ $row->course->code??'' }}</td>
                        <td>{{ $row->course->name??'' }}</td>
                        <td>{{ str_replace('_', ' ', $row->action??'') }}</td>
                        <td>{{ $row->change() == null ? '' : "from ".($row->change()->from??'-')." to ".($row->change()->to??'-') }}</td>
                        <td>{{ $row->created_at==null ? '' : $row->created_at->format('d/m/Y')??'' }}</td>
                        {{-- <td><a class="btn btn-sm btn-rounded btn-warning" href="{{ route('admin.trash.mark_changes.undo', $row->id) }}">@lang('text.word_undo')</a></td> --}}
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection