@extends('admin.layout')
@section('section')
    <div class="py-4">
        <div class="row">
            <div class="col-md-6 col-lg-6 py-2 px-3">
                <form class="form bg-light px-3 py-5 rounded" method="post">
                    @csrf
                    <input name="year_id" value="{{$year}}" hidden>
                    <div class="py-1 mt-4">
                        <label class="text-capitalize pb-1">{{__('text.word_campus')}}:</label>
                        <input name="campus_id" value="{{$attendance->campus_id}}" hidden>
                        <input value="{{$attendance->campus->name}}" type="text" class="form-control" readonly>
                    </div>
                    <div class="py-1">
                        <label class="text-capitalize pb-1">{{trans_choice('text.word_teacher',1)}}:</label>
                        <input name="teacher_id" value="{{$attendance->teacher_id}}" hidden>
                        <input value="{{$attendance->teacher->name}}" type="text" class="form-control" readonly>
                    </div>
                    <div class="py-1">
                        <label class="text-capitalize pb-1">{{__('text.word_course')}}:</label>
                        <input name="subject_id" value="{{$attendance->subject_id}}" hidden>
                        <input value="{{$attendance->subject->name}} [ {{$attendance->subject->code}} ]" type="text" class="form-control" readonly>
                    </div>
                    <div class="py-1">
                        <label class="text-capitalize pb-1">{{__('text.check_in')}}:</label>
                        <input value="{{$attendance->check_in}}" type="datetime" name="check_in" class="form-control" readonly>
                    </div>
                    <div class="py-1">
                        <label class="text-capitalize pb-1">{{__('text.check_out')}}:</label>
                        <input value="{{$time}}" type="datetime" name="check_out" class="form-control">
                    </div>
                    <div class="py-3 d-flex justify-content-end">
                        <input class="btn btn-sm btn-primary" value="{{__('text.word_save')}}" type="submit">
                    </div>
                </form>
            </div>
            <div class="col-md-6 col-lg-6 py-2 px-3">
                <table>
                    <thead class="bg-light h4">
                        <th class="border-left border-right">##</th>
                        <th class="border-left border-right">{{__('text.checked_in')}}</th>
                        <th class="border-left border-right">{{__('text.checked_out')}}</th>
                    </thead>
                    <tbody>
                        @php($k = 1)
                        @foreach ($record as $row)
                            <tr class="border-bottom border-top border-light">
                                <td class="border-left border-right border-light">{{$k++}}</td>
                                <td class="border-left border-right border-light text-center">{{$row->check_in}}</td>
                                <td class="border-left border-right border-light text-center">{{$row->check_out}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection