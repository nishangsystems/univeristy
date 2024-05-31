@extends('admin.layout')
@section('section')
    <div class="py-3 container-fluid">
        <form method="GET">
            <div class="row bg-light border-top border-bottom my-4 py-3 border-secondary">
                
                <div class="col-lg-8">
                    <input type="month" class="form-control" name="month" value="{{ request('month') }}">
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
                    <th class="">@lang('text.word_year')</th>
                    <th class="">@lang('text.word_semester')</th>
                    <th class="">@lang('text.applied_on')</th>
                    <th class="">@lang('text.word_course')</th>
                    <th class="">@lang('text.word_action')</th>
                    <th class="">@lang('text.additional_mark')</th>
                    <th class="">@lang('text.word_interval')</th>
                    <th class="">@lang('text.word_date')</th>
                    <th class="">@lang('text.word_user')</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $k = 1;
                @endphp
                @foreach ($data as $row)
                    <tr class="border-bottom">
                        <td>{{ $k++ }}</td>
                        <td>{{ $row->year->name??'' }}</td>
                        <td>{{ $row->semester->code??'' }}</td>
                        <td>{{ $row->class_id != null ? $row->class->name() : ($row->background_id != null ? ($row->background->background_name??'') : '' ) }}</td>
                        <td>{{ $row->course->name??'' }}</td>
                        <td><i>{{ str_replace('_', ' ', $row->action??'') }}</i></td>
                        <td>{{ $row->additional_mark??'' }}</td>
                        <td>{{ $row->interval() != null ? ("lower limit: ".$row->interval()->lower_limit.", upper limit: ".$row->interval()->upper_limit) : '' }}</td>
                        <td>{{ $row->created_at==null ? '' : $row->created_at->format('d/m/Y')??'' }}</td>
                        <td>{{ $row->user->name??'' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection