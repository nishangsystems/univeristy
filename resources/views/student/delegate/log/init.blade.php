@extends('student.layout')
@section('section')
@php
    use Illuminate\Support\Facades\Date;
@endphp

    <div class="row py-2">
        
        <div class="col-md-6 col-lg-6 px-2">
            @isset($content)
                {{-- @dd($attendance) --}}
                <div class="shadow-lg px-4 py-5" id="content">
                    <div class="py-2 border-bottom">
                        <label class="text-capitalize mb-2">{{__('text.word_attendance')}}</label>
                        <div class="d-flex flex-wrap justify-content-between text-capitalize rounded border bg-white py-3 px-3">
                            <span class="text-primary">{{__('text.word_from')}} : <span id="check_in">{{ $attendance->check_in }}</span></span>
                            <span class="text-primary">{{__('text.word_to')}} : <span id="check_out">{{ $attendance->check_in }}</span></span>
                            <input type="hidden" id="attendance_id_field" value="{{ $attendance->id }}">
                        </div>
                    </div>
                    <div class="py-2 border-bottom">
                        <label class="text-capitalize mb-2">{{__('text.word_content')}}</label>
                        <div class="text-capitalize">
                            <!-- display course content here for teacher to pick sub-topic -->

                            <div id="accordion" class="accordion-style1 panel-group">
                                <div class="panel-collapse collapse in" id="collapse">
                                    <div class="">
                                        @foreach ($content->where('level', 2) as $sub_topic)
                                            <div class="itemdiv dialogdiv">
                                                <div class="user">
                                                    <img alt="Alexa's Avatar" src="{{asset('assets/images/avatars/user.jpg')}}" />
                                                </div>

                                                <div class="body">
                                                    @if (\App\Models\CourseLog::where(['topic_id'=>$sub_topic->id])->count() > 0)
                                                        <div class="time">
                                                            <i class="ace-icon fa fa-clock-o"></i>
                                                            <span class="green">{{date('l d-m-Y', strtotime(\App\Models\CourseLog::where(['topic_id'=>$sub_topic->id])->first()->attendance->check_in))}}</span>
                                                        </div>

                                                        <div class="name">
                                                            <a href="#">{{$sub_topic->teacher->name??null}}</a>
                                                            <span class="label label-success arrowed arrowed-in-right text-uppercase">{{__('text.word_taught')}}</span>
                                                            <span class="label label-danger arrowed arrowed-in-right text-capitalize">{{__('text.word_from')}} : {{date('H:i', strtotime(\App\Models\CourseLog::where(['topic_id'=>$sub_topic->id, 'attendance_id'=>$attendance->id])->first()->attendance->check_in))}}</span>
                                                            <span class="label label-danger arrowed arrowed-in-right text-capitalize">{{__('text.word_to')}} : {{date('H:i', strtotime(\App\Models\CourseLog::where(['topic_id'=>$sub_topic->id,'attendance_id'=>$attendance->id])->first()->attendance->check_out))}}</span>
                                                        </div>
                                                    
                                                    @else
                                                        <div class="time">
                                                            <i class="ace-icon fa fa-clock-o"></i>
                                                            <span class="green">4 sec</span>
                                                        </div>

                                                        <div class="name">
                                                            <a href="#">{{$sub_topic->teacher->name??null}}</a>
                                                            <span class="label label-info arrowed arrowed-in-right">NOT TAUGHT</span>
                                                        </div>
                                                        
                                                    @endif
                                                    <div class="text fle flex-wrap">{!! $sub_topic->title !!}</div>

                                                    <div class="tools">
                                                        <a class="btn btn-xs btn-primary" href="{{ route('student.delegate.course.log', [$attendance->id, $sub_topic->id]) }}">
                                                            {{__('text.word_sign')}}
                                                            <i class="ml-2 text-white icon-only ace-icon fa fa-share"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <!-- @foreach ($content->where('level', 1) as $topic)
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapse_{{$topic->id}}">
                                                    <i class="ace-icon fa fa-angle-down bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                                                    &nbsp;{!! $topic->title !!}
                                                </a>
                                            </h4>
                                        </div>
                                        
                                    </div>
                                @endforeach -->
                            </div>
                        </div>
                    </div>
                </div>
            @endisset
        </div>
    </div>
@endsection
@section('script')
    <script>
        atts = (JSON.parse('<?php echo json_encode($attendance->toArray()); ?>'));
        console.log(atts);
        function __loadContent(params) {
            let att = atts.filter((element)=>{return element.id == params})[0];
            console.log(att);
            $('#attendance_id_field').val(params);
            $('#check_in').html(att.check_in);
            $('#check_out').html(att.check_out);
            $('#content').removeClass('hidden')
        }
    </script>
@endsection