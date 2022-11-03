@extends('teacher.layout')


@section('section')
<div class="row flex" style="padding:20px;">
    <div>
        <a href="{{route('material.create').'?type='.request('type').'&campus_id='.request('campus_id').'&program_level_id='.request('program_level_id')}}" style="padding:5px; margin:10px; float:bottom;" class="btn-primary">Create New Material</a>
    </div>
   
</div>

<div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
                    <i class="ace-icon fa fa-angle-down bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                    &nbsp;All Material
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
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse(\App\Models\Material::orderBy('created_at', 'DESC')->get(); as $notification)
                        @if($notification->schoolUnit()->count() > 0 && $notification->level()->count() == 0 && request('type')=='departmental')
                            <tr>  
                                <td>{{$notification->title}}</td>
                                <td>{{$notification->audience()}}</td>
                                <td>  <h6 class="mb-0">{{ $notification->created_at }}</h6>
                                {{ $notification->created_at->diffForHumans() }}</td>
                                <td>
                                    <a href="{{url('/storage/material').'/'.$notification->file}}" class=" btn btn-success btn-xs m-2" target="_new">{{__('text.word_download')}}</a>
                                    <a onclick="event.preventDefault();
                                                    document.getElementById('delete').submit();" class=" btn btn-danger btn-xs m-2">Delete</a>
                                    <form id="delete" action="{{route('material.drop',$notification->id).'?type='.request('type').'&program_level_id='.request('program_level_id').'&campus_id='.request('campus_id')}}" method="POST" style="display: none;">
                                        {{ csrf_field() }}
                                    </form>
                                </td>
                            </tr>
                            @else
                                @if($notification->level()->count() > 0 && request('program_level_id') != (''||null) && \App\Models\ProgramLevel::find(request('program_level_id'))->program_id == $notification->school_unit_id)
                                    <tr>  
                                        <td>{{$notification->title}}</td>
                                        <td>{{$notification->audience()}}</td>
                                        <td>  <h6 class="mb-0">{{ $notification->created_at }}</h6>
                                        {{ $notification->created_at->diffForHumans() }}</td>
                                        <td class="text-capitalize">
                                            <a href="{{url('/storage/material').'/'.$notification->file}}" class=" btn btn-success btn-xs m-2" target="_new">{{__('text.word_download')}}</a>
                                            <a onclick="event.preventDefault();
                                                            document.getElementById('delete').submit();" class=" btn btn-danger btn-xs m-2">{{__('text.word_delete')}}</a>
                                            <form id="delete" action="{{route('material.drop',$notification->id).'?type='.request('type').'&program_level_id='.request('program_level_id').'&campus_id='.request('campus_id')}}" method="POST" style="display: none;">
                                                {{ csrf_field() }}
                                            </form>
                                        </td>
                                    </tr>
                                @endif
                            @endif
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