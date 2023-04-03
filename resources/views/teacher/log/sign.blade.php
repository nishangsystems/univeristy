@extends('teacher.layout')
@section('section')
    <div class="row py-2">
        <div class="col-md-6 col-lg-6 px-2">
            <div class="shadow-lg px-2">
                <div class="py-4">
                    <form method="post" class="form py-4 px-3 bg-light">
                        @csrf
                        <input type="hidden" name="campus_id" value="{{$campus->id}}">
                        <!-- <div class="py-2">
                            <input class="form-control" value="{{$campus->name}}" readonly>
                        </div> -->
                        <input type="hidden" name="subject_id" value="{{$subject->id}}">
                        <!-- <div class="py-2">
                            <input class="form-control" value="{{$subject->name}}" readonly>
                        </div> -->
                        <div class="py-2">
                            <input type="hidden" name="topic_id" value="{{$topic->id}}">
                            <input class="form-control" value="{!! $topic->title !!}" readonly>
                        </div>
                        <input type="hidden" name="attendance_id" value="{{$attendance_record->id}}">
                        <!-- <div class="py-2">
                            <input class="form-control" value="{{$attendance_record->check_in.'  -  '.$attendance_record->check_out}}" readonly>
                        </div> -->
                        <div class="py-2">
                            <label class="text-capitalize pb-1">{{__('text.course_log')}}:</label>
                            <textarea class="form-control" name="details" id="course_log" rows="4"></textarea>
                        </div>
                        <div class="d-flex justify-content-end py-2">
                            <input class="btn btn-sm btn-primary" type="submit" value="{{__('text.word_save')}}">
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-6 px-2">
            <table class="table table-primary rounded shadow-lg">
                <thead class="text-primary text-capitalize">
                    <th class="border-left border-right border-light">{{__('text.sn')}}</th>
                    <th class="border-left border-right border-light">{{__('text.word_period')}}</th>
                    <th class="border-left border-right border-light">{{__('text.course_log')}}</th>
                    <th></th>
                </thead>
                <tbody>
                    @php($k = 1)
                    @foreach ($log_history as $record)
                        <tr class="border-bottom border-light">
                            <td class="border-left border-right border-light">{{$k++}}</td>
                            <td class="border-left border-right border-light">from {!! $record->attendance->check_in.' <br> to '.$record->attendance->check_out !!}</td>
                            <td class="border-left border-right border-light">{!! $record->details !!}</td>
                            <td class="border-left border-right border-light">
                                <a class="btn btn-sm btn-danger" href="{{route('user.course.log.drop', ['subject_id'=>$record->attendance->subject_id, 'log_id'=>$record->id])}}">{{__('text.word_delete')}}</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ asset('public/assets/js') }}/ckeditor/ckeditor.js"></script>
    <script>
        CKEDITOR.replace('course_log');
    </script>
    <script>
        function __loadContent(params) {
            console.log(params);
            $('#attendance_id_field').val(params);
            // let attendance = $(params).attr(data);
            // console.log(attendance);
            $('#content').removeClass('hidden')
        }
    </script>
@endsection