@extends('admin.layout')


@section('section')
@if(!(auth()->user()->type == 'teacher' && auth()->user()->classes()->count() == 0))
    <div class="row flex" style="padding:20px;">
        <div>
            <a href="{{route('notifications.create', [request('layer'), request('layer_id'), request('campus_id') ?? 0])}}" style="padding:5px; margin:10px; float:bottom;" class="btn-primary text-capitalize">{{__('text.create_new_notification')}}</a>
        </div>
    </div>
@endif
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
                        <th>Created on</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notifications as $notification)
                        
                        <!-- @if(request('type') != 'departmental' && $notification->visibility != 'general'||'teachers')
                        @endif -->
                                <tr>  
                                    <td>{{$notification->title}}</td>
                                    <td>{{$notification->audience()}}</td>
                                    <td>  <h6 class="mb-0">{{ $notification->created_at }}</h6>
                                    {{ $notification->created_at->diffForHumans() }}</td>
                                    <td><span class="btn btn-xs {{(time() > strtotime($notification->date))?'btn-danger':'btn-success'}} m-2">{{(time() >= strtotime($notification->date))?"Passed":"Pending"}}</span></td>
                                    <td class="text-capitalize">
                                        <a href="{{route('notifications.show',[request('layer'), request('layer_id'), request('campus_id') ?? 0, $notification->id])}}" class=" btn btn-success btn-xs m-2">{{__('text.word_view')}}</a>
                                        @if(!(auth()->user()->type == 'teacher' && auth()->user()->classes()->count() == 0))
                                            <a href="{{route('notifications.edit',[request('layer'), request('layer_id'), request('campus_id') ?? 0, $notification->id])}}" class=" btn btn-primary btn-xs m-2">{{__('text.word_edit')}}</a>
                                            <a href="{{route('notifications.drop',[request('layer'), request('layer_id'), request('campus_id') ?? 0, $notification->id])}}" class=" btn btn-danger btn-xs m-2">{{__('text.word_delete')}}</a>
                                        @endif
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