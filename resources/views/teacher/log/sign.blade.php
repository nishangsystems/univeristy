@extends('teacher.layout')
@section('section')
    <div class="row py-2">
        <div class="col-md-5 col-lg-5 px-2">
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
                            <div class="py-2 px-2 rounded border-top border-bottom" readonly>{!! $topic->title !!}</div>
                        </div>
                        <input type="hidden" name="attendance_id" value="{{$attendance_record->id}}">
                        <!-- <div class="py-2">
                            <input class="form-control" value="{{$attendance_record->check_in.'  -  '.$attendance_record->check_out}}" readonly>
                        </div> -->
                        <div class="py-2">
                            <label class="text-capitalize pb-1">{{__('text.course_log')}}:</label>
                            <textarea class="form-control w-100" name="details" id="course_log" rows="4"></textarea>
                        </div>
                        <div class="d-flex justify-content-end py-2">
                            <input class="btn btn-sm btn-primary" type="submit" value="{{__('text.word_save')}}">
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-7 col-lg-7 px-2">
            <table class="table table-primary rounded shadow-lg">
                <thead class="text-primary text-capitalize">
                    <th class="border-left border-right border-light">{{__('text.sn')}}</th>
                    <th class="border-left border-right border-light">{{__('text.sub_topic')}}</th>
                    <th class="border-left border-right border-light">{{__('text.check_in')}}</th>
                    <th class="border-left border-right border-light">{{__('text.check_out')}}</th>
                    <th class="border-left border-right border-light">{{__('text.course_log')}}</th>
                    <th></th>
                </thead>
                <tbody>
                    @php($k = 1)
                    @foreach ($log_history as $record)
                        <tr class="border-bottom border-light">
                            <td class="border-left border-right border-light">{{$k++}}</td>
                            <td class="border-left border-right border-light">{!! '<div class="text-capitalize text-primary h4">'.$record->topic->subject->name.' [ '.$record->topic->subject->code.' ]</div><div class="text-success h4 fa fa-circle fa-2x"> '.$record->topic->parent->title.'</div><div class="text-primary h5 fa-1x">'.$record->topic->title.'</div>' !!}</td>
                            <td class="border-left border-right border-light"><span class="text-primary">{{ date('d-m-Y', strtotime($record->attendance->check_in)) }}</span> <br> <span>{{ date('H:i', strtotime($record->attendance->check_in)) }}</span></td>
                            <td class="border-left border-right border-light"><span class="text-primary">{{ date('d-m-Y', strtotime($record->attendance->check_out)) }}</span> <br> <span>{{ date('H:i', strtotime($record->attendance->check_out)) }}</span></td>
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
    {{-- <script>
        var editor1 = new RichTextEditor("#course_log");
    </script> --}}
    <script src="{{ asset('assets/js') }}/ckeditor/ckeditor.js"></script>
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