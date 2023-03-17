@extends('admin.layout')
@section('section')
@php
    $year = request('year_id') == null ? \App\Helpers\Helpers::instance()->getCurrentAccademicYear() : request('year_id');
@endphp
<div class="py-4">
    
    <table class="table">
        <thead class="text-capitalize">
            <th>###</th>
            <th>{{__('text.word_name')}}</th>
            <th>{{__('text.word_matricule')}}</th>
            @if (request('filer')=='program')
                <th>{{__('text.word_level')}}</th>
                @else
                <th>{{__('text.word_class')}}</th>
            @endif
        </thead>
        <tbody>
            @php($k = 1)
            @foreach($students as $stud)
                <tr>
                    <td>{{$k++}}</td>
                    <td>{{$stud->name}}</td>
                    <td>{{$stud->matric}}</td>
                    @if (request('filter') == 'program')
                    <td>{{\App\Models\ProgramLevel::find($stud->class_id)->level->name ?? '----'}}</td>
                    @else
                    <td>{{\App\Models\ProgramLevel::find($stud->class_id)->name() ?? '----'}}</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection