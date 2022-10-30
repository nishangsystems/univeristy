@extends('teacher.layout')
@section('section')
<div class="adv-table py-3">
    <table class="table">
        <thead class="text-capitalize bg-light">
            <th>##</th>
            <th>{{__('text.word_matricule')}}</th>
            <th>{{__('text.word_name')}}</th>
            <th></th>
        </thead>
        <tbody>
            @php($k = 1)
            @foreach($students as $student)
            <tr>
                <td>{{$k++}}</td>
                <td>{{$student->matric}}</td>
                <td>{{$student->name}}</td>
                <td></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection