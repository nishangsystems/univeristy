@extends('teacher.layout')


@section('section')
<div class="row flex" style="padding:20px;">
    <div>
        <a href="{{route('course.notification.create', request('course_id'))}}" style="padding:5px; margin:10px; float:bottom;" class="btn-primary text-capitalize">{{__('text.create_new_notification')}}</a>
    </div>
   
</div>

<div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a class="accordion-toggle text-capitalize" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
                    <i class="ace-icon fa fa-angle-down bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                    &nbsp;{{__('text.all_notifications')}}
                </a>
            </h4>
        </div>
        
        <div class="panel-body">
            <table class="table table-bordered">
                <thead>
                    <tr class="text-capitalize">
                        <th>{{__('text.word_title')}}</th>
                        <th>{{__('text.created_on')}}</th>
                        <th>{{__('text.due_date')}}</th>
                        <th>{{__('text.word_status')}}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse(\App\Models\CourseNotification::where('course_id', request('course_id'))->orderBy('created_at', 'DESC')->get() as $notification)
                            <tr>  
                                <td>{{$notification->title}}</td>
                                <td>  <h6 class="mb-0">{{ $notification->created_at }}</h6>
                                {{ $notification->created_at->diffForHumans() }}</td>
                                <td><span class="btn btn-xs {{(time() > strtotime($notification->date))?'btn-danger':'btn-success'}} m-2">{{(time() >= strtotime($notification->date))?"Passed":"Pending"}}</span></td>
                                <td class="text-capitalize">
                                    <a href="{{route('course.notification.edit',[request('course_id'), $notification->id])}}" class=" btn btn-primary btn-xs m-2">{{__('text.word_edit')}}</a>
                                    <a href="{{route('course.notification.show',[request('course_id'), $notification->id])}}" class=" btn btn-success btn-xs m-2">{{__('text.word_view')}}</a>
                                    <a href="{{route('course.notification.drop',[request('course_id'), $notification->id])}}" class=" btn btn-danger btn-xs m-2">{{__('text.word_delete')}}</a>
                                </td>
                            </tr>
                    @empty
                        <tr>  
                            <td colspan="5" class="text-center">{{__('text.no_notifications_found')}}</td>
                        </tr>
                    @endforelse
                
                </tbody>
            </table>
        </div>
     </div>
 <div>
@stop