@extends('teacher.layout')
@section('section')
@php
    $request = request();

@endphp
<div class="adv-table py-3">
    <div class="d-flex justify-content-end py-2">
        <a href="{{route('user.subject.result_template', ['campus_id'=>$request->campus_id, 'class_id'=>$request->class_id, 'course_id'=>$request->course_id])}}" class="btn btn-sm btn-primary text-capitalize">{{__('text.results_template')}}</a>
    </div>
    <table class="table">
        <thead class="text-capitalize bg-light">
            <th>##</th>
            <th>{{__('text.word_matricule')}}</th>
            <th>{{__('text.word_name')}}</th>
        </thead>
        <tbody>
            @php($k = 1)
            @foreach($students as $student)
            <tr>
                <td>{{$k++}}</td>
                <td>{{$student->matric}}</td>
                <td>{{$student->name}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection