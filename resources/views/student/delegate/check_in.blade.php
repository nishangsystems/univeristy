@extends('student.layout')
@section('section')
    <div class="py-3">
        <div class="row">
            <div class="col-md-6 col-lg-6 py-2 px-5">
                <form class="form bg-light px-3 py-5 rounded" method="post">
                    @csrf
                    <input name="year_id" value="{{$year}}" hidden>
                    <div class="py-1 mt-4">
                        <label class="text-capitalize pb-1">{{__('text.word_campus')}}:</label>
                        <input name="campus_id" value="{{$campus->id}}" hidden>
                        <input value="{{$campus->name}}" type="text" class="form-control" readonly>
                    </div>
                    <div class="py-1">
                        <label class="text-capitalize pb-1">{{trans_choice('text.word_teacher',1)}}:</label>
                        <input name="teacher_id" value="{{$teacher->id}}" type="hidden">
                        <input value="{{$teacher->name}}" type="text" class="form-control" readonly>
                    </div>
                    <div class="py-1">
                        <label class="text-capitalize pb-1">{{__('text.word_course')}}:</label>
                        <input name="subject_id" value="{{$subject->id}}" hidden>
                        <input value="{{$subject->name}} [ {{$subject->code}} ]" type="text" class="form-control">
                    </div>

                    <div class="py-1" id="instant-record">
                        <label class="text-capitalize pb-1">{{__('text.check_in')}}:</label>
                        <input value="{{now()}}" type="datetime-local" name="check_in" class="form-control">
                    </div>
                    <div class="py-3 d-flex justify-content-end">
                        <input class="btn btn-sm btn-primary" value="{{__('text.word_save')}}" type="submit">
                    </div>
                </form>
            </div>
            <div class="col-md-6 col-lg-6 py-2 px-5">
                <table class="table">
                    <thead class="bg-light h4">
                        <th class="border-left border-right">{{__('text.sn')}}</th>
                        <th class="border-left border-right text-primary">{{__('text.checked_in')}}</th>
                        <th class="border-left border-right text-primary">{{__('text.checked_out')}}</th>
                        <th class="border-left border-right"></th>
                    </thead>
                    <tbody>
                        @php($k = 1)
                        @foreach ($record as $row)
                            <tr class="border-bottom border-top border-light">
                                <td class="border-left border-right border-light">{{$k++}}</td>
                                <td class="border-left border-right border-light text-primary">{{date('d-m-Y', strtotime($row->check_in))}} <br> <span class="text-success">
                                {{date('H:i', strtotime($row->check_in))}}
                                </span> </td>
                                <td class="border-left border-right border-light text-primary">
                                    @if($row->check_out == null)
                                        <a class="btn btn-xs btn-danger" href="{{route('student.delegate.check_out', $row->id)}}">{{__('text.check_out')}}</a>
                                    @else
                                        {{date('d-m-Y', strtotime($row->check_out))}} <br> <span class="text-success">
                                        {{date('H:i', strtotime($row->check_out))}}
                                        </span> 
                                    @endif
                                </td>
                                <td class="border-left border-right border-light">
                                    @if($row->check_out == null)
                                        <form action="{{route('student.delegate.attendance.drop', $row->id)}}" method="post" id="drop_form{{ $row->id }}">
                                            @csrf
                                            <a type="submit" onclick="
                                            event.preventDefault();
                                            confirm(`You are about to delete an attendance record. Confirm to proceed.`) ? $('#drop_form{{ $row->id }}').submit() : null;
                                            " class="btn btn-xs btn-danger" href="">{{__('text.word_delete')}}</a>
                                        </form>
                                    @else
                                        <a href="{{ route('student.delegate.course.log.init', $row->id) }}" class="btn btn-primary btn-sm">{{ __('text.course_log') }}</a>
                                        @if($row->courseLog()->count() > 0)
                                            <a href="{{ route('student.delegate.course.log', [$row->id, 0]) }}" class="btn btn-primary btn-sm">{{ __('text.log_history') }}</a>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('script')
    
@endsection