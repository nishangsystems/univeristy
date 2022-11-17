@extends('student.layout')


@section('section')

<div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
                    <i class="ace-icon fa fa-angle-down bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                    &nbsp;{{__('text.all_notifications')}}
                </a>
            </h4>
        </div>
        
        <div class="panel-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Audience</th>
                        <th>Due date</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notifications as $notification)
                        <tr>  
                            <td>{{$notification->title}}</td>
                            <td>{{$notification->audience()}}</td>
                            {{ $notification->created_at->diffForHumans() }}</td>
                            <td><span class="m-2">{{date('l d/m/Y', strtotime($notification->date))}}</span></td>
                            <td class="text-capitalize">
                                <a href="{{route('student.notification.view',[$notification->id])}}" class=" btn btn-success btn-xs m-2">{{__('text.word_view')}}</a>
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