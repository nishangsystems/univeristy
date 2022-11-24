@extends('student.layout')


@section('section')

<div class="panel-body">
    <table class="table table-bordered">
        <tbody>
            <tr>
                <td class="text-capitalize">{{__('text.word_school')}}</td>
                <td class="hidden">{{$department->name}}</td>
                <td class="text-capitalize">
                    <a href="{{route('student.notification.school', [auth('student')->user()->campus_id])}}" class=" btn btn-primary btn-xs m-2">{{__('text.word_notifications')}}</a>
                    <a href="{{route('student.material.school', [auth('student')->user()->campus_id])}}" class=" btn btn-success btn-xs m-2">{{__('text.word_material')}}</a>
                </td>
            </tr>
            <tr>
                <td class="text-capitalize">{{__('text.word_departmental')}}</td>
                <td class="hidden">{{$department->name}}</td>
                <td class="text-capitalize">
                    <a href="{{route('student.notification.department', [$department->id, auth('student')->user()->campus_id])}}" class=" btn btn-primary btn-xs m-2">{{__('text.word_notifications')}}</a>
                    <a href="{{route('student.material.department', [$department->id, auth('student')->user()->campus_id])}}" class=" btn btn-success btn-xs m-2">{{__('text.word_material')}}</a>
                </td>
            </tr>
            <tr>
                <td class="text-capitalize">{{__('text.word_program')}}</td>
                <td class="hidden">{{$program->name}}</td>
                <td class="text-capitalize">
                    <a href="{{route('student.notification.program',[$class->id, auth('student')->user()->campus_id])}}" class=" btn btn-primary btn-xs m-2">{{__('text.word_notifications')}}</a>
                    <a href="{{route('student.material.program',[$class->id, auth('student')->user()->campus_id])}}" class=" btn btn-success btn-xs m-2">{{__('text.word_material')}}</a>
                </td>
            </tr>
            <tr>
                <td class="text-capitalize">{{__('text.word_class')}}</td>
                <td class="hidden">{{$class->program->name.' : '.__('text.word_level').' '.$class->level->level}}</td>
                <td class="text-capitalize">
                    <a href="{{route('student.notification.class',[$class->id, auth('student')->user()->campus_id])}}" class=" btn btn-primary btn-xs m-2">{{__('text.word_notifications')}}</a>
                    <a href="{{route('student.material.class',[$class->id, auth('student')->user()->campus_id])}}" class=" btn btn-success btn-xs m-2">{{__('text.word_material')}}</a>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<!-- <div class="panel panel-default">
 <div> -->
@endsection