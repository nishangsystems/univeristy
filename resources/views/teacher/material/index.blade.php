@extends('teacher.layout')


@section('section')

@if(!(auth()->user()->type == 'teacher' && auth()->user()->classes()->count() == 0))
<div class="row flex" style="padding:20px;">
    <div>
        <a href="{{route('material.create', [request('layer'), request('layer_id'), request('campus_id') ?? 0])}}" style="padding:5px; margin:10px; float:bottom;" class="btn-primary text-capitalize">{{__('text.create_new_material')}}</a>
    </div>
   
</div>
@endif

<div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a class="accordion-toggle text-capitalize" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
                    <i class="ace-icon fa fa-angle-down bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                    &nbsp;{{__('text.all_material')}}
                </a>
            </h4>
        </div>
        
        <div class="panel-body">
            <table class="table table-bordered">
                <thead>
                    <tr class="text-capitalize">
                        <th>{{__('text.word_title')}}</th>
                        <th>{{__('text.word_audience')}}</th>
                        <th>{{__('text.created_on')}}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($material as $notification)
                        <tr>  
                            <td>{{$notification->title}}</td>
                            <td>{{$notification->audience()}}</td>
                            <td>  <h6 class="mb-0">{{ $notification->created_at }}</h6>
                            {{ $notification->created_at->diffForHumans() }}</td>
                            <td>
                                <a href="{{url('/storage/material').'/'.$notification->file}}" target="_new" class=" btn btn-primary btn-sm">{{__('text.word_download')}}</a>|
                                <a href="{{route('material.show',[request('layer'), request('layer_id'), request('campus_id') ?? 0, $notification->id])}}" class=" btn btn-success btn-sm">{{__('text.word_view')}}</a>|
                                @if(!(auth()->user()->type == 'teacher' && auth()->user()->classes()->count() == 0))
                                    <a href="{{route('material.edit',[request('layer'), request('layer_id'), request('campus_id') ?? 0, $notification->id])}}" class=" btn btn-primary btn-sm">{{__('text.word_edit')}}</a>|
                                    <a href="{{route('material.drop',[request('layer'), request('layer_id'), request('campus_id') ?? 0, $notification->id])}}" class=" btn btn-danger btn-sm">{{__('text.word_delete')}}</a>|
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>  
                            <td colspan="5" class="text-center">No Material Found</td>
                        </tr>
                    @endforelse
                
                </tbody>
            </table>
        </div>
     </div>
 <div>
@stop