@extends('teacher.layout')


@section('section')

<div class="row flex" style="padding:20px;">
    <div>
        <a href="{{route('user.notifications.create')}}" style="padding:5px; margin:10px; float:bottom;" class="btn-primary">Create New Notification</a>
    </div>
   
</div>

<div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
                    <i class="ace-icon fa fa-angle-down bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                    &nbsp;All Notifications
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
                    @forelse(\App\Models\Notifications::orderBy('created_at', 'DESC')->get(); as $notification)

                        <tr>  
                            <td>{{$notification->name}}</td>
                            <td>{{($notification->program == null)?"For all Programs":$notification->program->name}} - 
                            {{($notification->level == null)?"For all Levels":$notification->level->name}}</td>
                            <td>  <h6 class="mb-0">{{ $notification->created_at }}</h6>
                            {{ $notification->created_at->diffForHumans() }}</td></td>
                            <td><span class="btn btn-xs {{(time() > strtotime($notification->date))?'btn-danger':'btn-success'}} m-2">{{(time() >= strtotime($notification->date))?"Passed":"Pending"}}</span></td>
                        <td>
                                <a href="{{route('admin.notification.edit',$notification->id)}}" class=" btn btn-primary btn-xs m-2">Edit</a>
                                <a href="{{route('admin.notification.show',$notification->id)}}" class=" btn btn-success btn-xs m-2">View</a>
                                <a onclick="event.preventDefault();
												document.getElementById('delete').submit();" class=" btn btn-danger btn-xs m-2">Delete</a>


                            <form id="delete" action="{{route('admin.notification.destroy',$notification->id)}}" method="POST" style="display: none;">
                            @method('DELETE')
                                {{ csrf_field() }}
                            </form>
                            </td>
                        </tr>
                    @empty
                        <tr>  
                            <td colspan="5" class="text-center">No Notifications Found</td>
                        </tr>
                    @endforelse
                
                </tbody>
            </table>
        </div>
     </div>
 <div>
@stop