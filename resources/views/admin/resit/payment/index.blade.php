@extends('admin.layout')
@section('section')
    <div class="py-3">
        <table class="table">
            <thead class="text-capitalize">
                <th>@lang('text.sn')</th>
                <th>@lang('text.word_background')</th>
                <th>@lang('text.word_resits')</th>
                <th>@lang('text.word_year')</th>
                <th></th>
            </thead>
            <tbody>
                @php
                    $k = 1;
                @endphp
                @foreach ($resits as $resit)
                    <tr>
                        <td>{{ $k++ }}</td>
                        <td>{{ $resit->background->background_name??'' }}</td>
                        <td>{{ $resit->name??'' }}</td>
                        <td>{{ $resit->year->name??'' }}</td>
                        <td>
                            <a class="btn btn-sm btn-primary rounded px-4" href="{{ route('admin.resits.payments.student', $resit->id) }}">{{ trans_choice('text.word_next', 1) }}</a>
                            <a class="btn btn-sm btn-warning rounded px-4" href="{{ route('admin.resits.payments.student', $resit->id) }}">{{ __('text.word_report') }}</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection