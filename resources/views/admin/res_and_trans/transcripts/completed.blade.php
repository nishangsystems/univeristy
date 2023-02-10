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
                <th class="border-left border-right border-white">{{__('text.date_printed')}}</th>
                <th class="border-left border-right border-white"></th>
            </thead>
            <tbody class="bg-light">
                @php
                    $k = 1;
                @endphp
                @foreach ($data as $row)
                    <tr class="border-bottom border-white">
                        <td class="border-left border-right border-white">{{$k++}}</td>
                        <td class="border-left border-right border-white">{{$row->student->matric}}</td>
                        <td class="border-left border-right border-white">{{$row->student->name}}</td>
                        <td class="border-left border-right border-white">{{$row->tel}}</td>
                        <td class="border-left border-right border-white">{{$row->status}}</td>
                        <td class="border-left border-right border-white">{{$row->mode}}</td>
                        <td class="border-left border-right border-white">{{$row->done}}</td>
                        <td class="border-left border-right border-white {{$row->collected == null ? 'text-primary' : 'text-success' }}">{{$row->collected == null ? __('text.word_available') : __('text.word_collected')}}</td>
                    </tr>
                @endforeach
                @foreach ($_data as $row)
                    <tr class="border-bottom border-white">
                        <td class="border-left border-right border-white">{{$k++}}</td>
                        <td class="border-left border-right border-white">{{$row->student->matric}}</td>
                        <td class="border-left border-right border-white">{{$row->student->name}}</td>
                        <td class="border-left border-right border-white">{{$row->tel}}</td>
                        <td class="border-left border-right border-white">{{$row->status}}</td>
                        <td class="border-left border-right border-white">{{$row->mode}}</td>
                        <td class="border-left border-right border-white">{{$row->done}}</td>
                        <td class="border-left border-right border-white {{$row->collected == null ? 'text-primary' : 'text-success' }}">{{$row->collected == null ? __('text.word_available') : __('text.word_collected')}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection