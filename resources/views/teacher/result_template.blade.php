@extends('teacher.layout')
@section('section')
@php
    $request = request();

@endphp
<div class="adv-table py-3">

    <table class="table adv-table">
        <thead class="text-capitalize bg-light">
            <th>{{__('text.word_matricule')}}</th>
            <th>{{__('text.course_code')}}</th>
            <th>{{__('text.ca_mark')}}</th>
            <th>{{__('text.exam_mark')}}</th>
        </thead>
        <tbody>
            @foreach($students as $student)
            <tr>
                <td>{{$student->matric}}</td>
                <td>{{\App\Models\Subjects::find($request->course_id)->code ?? ''}}</td>
                <td></td>
                <td></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection