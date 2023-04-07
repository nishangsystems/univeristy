@extends('student.layout')
@section('section')
<div class="py-3">

    <div class="widget-box transparent">
        <div class="widget-header widget-header-small">
            <h4 class="widget-title blue smaller text-capitalize">
                <i class="ace-icon fa fa-rss orange"></i>
                {{__('text.course_instructors')}}
            </h4>

        </div>

        <div class="widget-body">
            <div class="widget-main padding-8">
                <div id="profile-feed-1" class="profile-feed">

                    @foreach($teachers as $teacher)
                        <div class="profile-activity clearfix">
                            <div>
                                <img class="pull-left" alt="Alex Doe's avatar" src="{{asset('assets/images/avatars/user.jpg')}}" />
                                <a class="user" href="#"> {{$teacher->name}} </a>
                            </div>
                        </div>
                    @endforeach

                    
                </div>
            </div>
        </div>
    </div>


    <div class="row py-4">
        <div class="col-md-6">
            <div class="card">
                <h4 class="card-header">{{__('text.course_objective')}}</h4>
                <div class="card-body">{!! $subject->objective !!}</div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <h4 class="card-header">{{__('text.expected_outcomes')}}</h4>
                <div class="card-body">{!! $subject->outcomes !!}</div>
            </div>
        </div>
    </div>


    <!-- COURSE OUTLINE STARTS HERE -->

    <div class="row">
        <div class="col-sm-12 col-md-12">
            <h3 class="row header smaller lighter blue">
                <span class="col-xs-6 text-capitalize"> {{__('text.course_content')}} </span><!-- /.col -->
            </h3>

            <div id="accordion" class="accordion-style1 panel-group">
                @foreach ($topics->where('level', '1') as $topic)
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapse_{{$topic->id}}">
                                    <i class="ace-icon fa fa-angle-down bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                                    &nbsp;{!! $topic->title !!}
                                </a>
                            </h4>
                        </div>
                        
                        <div class="panel-collapse collapse" id="collapse_{{$topic->id}}">
                            <div class="">
                                @foreach ($topics->where('parent_id', $topic->id) as $sub_topic)
                                    <div class="itemdiv dialogdiv">
                                        <div class="user">
                                            <img alt="Alexa's Avatar" src="{{asset('assets/images/avatars/user.jpg')}}" />
                                        </div>

                                        <div class="body">
                                            <div class="time">
                                                <i class="ace-icon fa fa-clock-o"></i>
                                                <span class="green">@if (\App\Models\CourseLog::where(['topic_id'=>$sub_topic->id])->count() > 0)
                                                        {{date('l d-m-Y', strtotime(\App\Models\CourseLog::where(['topic_id'=>$sub_topic->id])->orderBy('id')->first()->attendance->check_in))}}
                                                    @endif</span>
                                            </div>

                                            <div class="name text-capitalize">
                                                <a href="#">{{$sub_topic->teacher->name??null}}</a>
                                                @if (\App\Models\CourseLog::where(['topic_id'=>$sub_topic->id])->count() > 0)
                                                    <span class="label label-success arrowed arrowed-in-right text-uppercase">{{__('tect.word_taught')}}</span>
                                                    <span class="label label-danger arrowed arrowed-in-right">{{__('text.word_from')}} : {{ date('H:i', strtotime(\App\Models\CourseLog::where(['topic_id'=>$sub_topic->id])->orderBy('id')->first()->attendance->check_in)) }}</span>
                                                    <span class="label label-danger arrowed arrowed-in-right">{{__('text.word_to')}} : {{ date('H:i', strtotime(\App\Models\CourseLog::where(['topic_id'=>$sub_topic->id])->orderBy('id')->first()->attendance->check_out)) }}</span>
                                                @else
                                                    <span class="label label-info arrowed arrowed-in-right">NOT TAUGHT</span>
                                                @endif
                                            </div>
                                            <div class="text fle flex-wrap">{!! $sub_topic->title !!}</div>

                                            <div class="tools">
                                                <a href="#" class="btn btn-minier btn-info">
                                                    <i class="icon-only ace-icon fa fa-share"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div><!-- /.col -->
    </div>
</div>
@endsection
@section('script')

@endsection