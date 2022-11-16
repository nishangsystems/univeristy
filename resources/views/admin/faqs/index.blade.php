@extends('admin.layout')


@section('section')
<div class="panel panel-default">
        <div class="panel-heading w-100">
            <h4 class="panel-title">
                <a class="accordion-toggle text-uppercase" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
                    <i class="ace-icon fa fa-angle-down bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                    &nbsp;{{__('text.word_faqs')}}
                </a>
            </h4>
        </div>
        <div class="panel-body w-100">
            <table class="table table-bordered container-fluid">
                <thead>
                    <tr class="text-capitalize">
                        <th>{{__('text.word_question')}}</th>
                        <th>{{__('text.created_on')}}</th>
                        @if(auth()->user()->type)
                        <th>{{__('text.word_status')}}</th>
                        @endif
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($faqs as $notification)
                        <tr>  
                            <td>{{$notification->question}}</td>
                            <td>  <h6 class="mb-0">{{ $notification->created_at }}</h6></td>
                            @if(auth()->user()->type)
                            <td><a  href="{{route('faqs.publish',[$notification->id])}}" class="btn btn-xs {{$notification->status() ? 'btn-danger' : 'btn-success'}} m-2 text-uppercase">{{$notification->status() ? __('text.word_unpublish'): __('text.word_publish')}}</a></td>
                            @endif
                            <td class="text-capitalize">
                                <a href="{{route('faqs.edit',[$notification->id])}}" class=" btn btn-primary btn-xs m-2">{{__('text.word_edit')}}</a>
                                <a href="{{route('faqs.show',[$notification->id])}}" class=" btn btn-success btn-xs m-2">{{__('text.word_view')}}</a>
                                <a href="{{route('faqs.drop',[$notification->id])}}" class=" btn btn-danger btn-xs m-2">{{__('text.word_delete')}}</a>
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