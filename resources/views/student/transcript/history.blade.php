@extends('student.layout')
@section('section')
    @php
        $year = \App\Helpers\Helpers::instance()->getCurrentAccademicYear();
        $current = auth()->user()->_class($year) != null;
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
                    <td class="border-left border-right border-white">{{$k++}}</td>
                    <td class="border-left border-right border-white">{{date('l d/m/Y', strtotime($row->created_at))}}</td>
                    <td class="border-left border-right border-white">{{$row->config->mode}}</td>
                    <td class="border-left border-right border-white">{{date('l d/m/Y', strtotime($row->done))}}</td>
                    <td class="border-left border-right border-white">{{$row->status}}</td>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection