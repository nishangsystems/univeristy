@extends('student.layout')


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
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($faqs as $notification)
                        <tr>  
                            <td>{{$notification->question}}</td>
                            <td class="text-capitalize">
                                <a href="{{route('faqs.show',[$notification->id])}}" class=" btn btn-success btn-xs m-2">{{__('text.word_view')}}</a>
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