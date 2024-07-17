@extends('admin.layout')
@section('section')
    <div class="py-3">
        <div class="container-fluid rounded-md shadow">
            <table class="table table-stripped">
                <thead class="text-capitalize">
                    <th>@lang('text.sn')</th>
                    <th>@lang('text.word_mode')</th>
                    <th>@lang('text.word_status')</th>
                    <th>@lang('text.word_amount')</th>
                    <th>@lang('text.word_completed')</th>
                    <th>@lang('text.word_pending')</th>
                    <th>@lang('text.word_expired')</th>
                    <th>@lang('text.word_collected')</th>
                </thead>
                <tbody>
                    @php
                        $counter = 1;
                    @endphp
                    @foreach ($data->groupBy('mode') as $mode_items)
                        <tr class="border-bototm">
                            <td rowspan="2">{{ $counter++ }}</td>
                            <td rowspan="2">{{ optional($mode_items->first())->mode??'--mode--' }}</td>
                            @foreach ($mode_items->groupBy('status') as $status_items)
                                @php
                                    $expired = $status_items->filter(function($item){
                                        return now()->diffInDays($item->created_at) > $item->duration;
                                    })
                                @endphp
                                <td>{{ optional($status_items->first())->status??'--status--' }}</td>
                                <td>{{ $status_items->sum('amount') }}</td>
                                <td>{{ $status_items->whereNotNull('done')->count() }}</td>
                                <td>{{ $status_items->whereNull('done')->filter(function($item){return now()->diffInDays($item->created_at) <= $item->duration;})->count() }}</td>
                                <td>{{ $status_items->whereNull('done')->filter(function($item){return now()->diffInDays($item->created_at) > $item->duration;})->count() }}</td>
                                <td>{{ $status_items->whereNotNull('collected')->filter(function($item){return now()->diffInDays($item->created_at) > $item->duration;})->count() }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection