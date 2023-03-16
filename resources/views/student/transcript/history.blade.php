@extends('student.layout')
@section('section')
    @php
        $year = \App\Helpers\Helpers::instance()->getCurrentAccademicYear();
        $current = auth('student')->user()->_class($year) != null;
    @endphp
    <div class="py-3">
    <table class="table">
            <thead class="bg-secondary text-light text-capitalize">
                <th class="border-left border-right border-white">#</th>
                <th class="border-left border-right border-white">{{__('text.date_applied')}}</th>
                <th class="border-left border-right border-white">{{__('text.word_mode')}}</th>
                <th class="border-left border-right border-white">{{__('text.date_completed')}}</th>
                <th class="border-left border-right border-white">{{__('text.word_status')}}</th>
            </thead>
            <tbody class="bg-light">
                @php
                    $k = 1;
                @endphp
                @foreach ($data as $row)
                    <tr class="border-bottom border-white">
                        <td class="border-left border-right border-white">{{$k++}}</td>
                        <td class="border-left border-right border-white">{{date('l d/m/Y', strtotime($row->created_at))}}</td>
                        <td class="border-left border-right border-white">{{$row->config->mode}}</td>
                        <td class="border-left border-right border-white">@if($row->done != null) {{date('l d/m/Y', strtotime($row->done))}} @endif</td>
                        <td class="border-left border-right border-white">@if($row->done==null) &hellip; {{__('text.word_processing')}} @else &#x2714; {{__('text.word_completed')}} @endif</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection