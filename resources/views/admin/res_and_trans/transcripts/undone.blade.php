@extends('admin.layout')
@section('section')
    <div class="py-3">
        <table class="table">
            <thead class="bg-secondary text-light text-capitalize">
                <th class="border-left border-right border-white">#</th>
                <th class="border-left border-right border-white">{{__('text.word_matricule')}}</th>
                <th class="border-left border-right border-white">{{__('text.word_name')}}</th>
                <th class="border-left border-right border-white">{{__('text.word_phone')}}</th>
                <th class="border-left border-right border-white">{{__('text.word_status')}}</th>
                <th class="border-left border-right border-white">{{__('text.word_mode')}}</th>
                <th class="border-left border-right border-white">{{__('text.days_left')}}</th>
                <th class="border-left border-right border-white"></th>
            </thead>
            <tbody class="bg-light">
                @php
                    $k = 1;
                @endphp
                @foreach ($data as $row)
                    <td class="border-left border-right border-white">{{$k++}}</td>
                    <td class="border-left border-right border-white">{{$row->student->matric}}</td>
                    <td class="border-left border-right border-white">{{$row->student->name}}</td>
                    <td class="border-left border-right border-white">{{$row->tel}}</td>
                    <td class="border-left border-right border-white">{{$row->status}}</td>
                    <td class="border-left border-right border-white">{{$row->mode}}</td>
                    <td class="border-left border-right border-white">{{$row->done}}</td>
                    <td class="border-left border-right border-white ">
                        <a class="btn btn-primary btn-sm" href="{{route('admin.res_and_trans.transcripts.set_done', $row->id)}}">{{__('text.word_Done')}}</a>
                    </td>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection