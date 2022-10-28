@extends('admin.layout');
@section('section')
<div class="w-100 py-5">
    <div class="py-5">
        <a href="{{route('admin.semesters.create', request('program_id'))}}" class="btn btn-primary">{{__('text.add_semester')}}</a>
    </div>
    <table class="table">
        <thead>
            <th>###</th> 
            <th>Name</th> 
            <th></th> 
        </thead>
        <tbody>
            @php
                $k = 1;
                $flag = false;
            @endphp
            @foreach($semesters as $sem)
                <tr class="border-bottom">
                    <td class="border-right border-left">{{$k++}}</td>
                    <td class="border-right border-left">{{$sem->name}}</td>
                    <td class="border-right border-left">
                        <a href="{{route('admin.semesters.edit', [request('program_id'), $sem->id])}}" class="btn btn-primary btn-sm">edit</a> |
                        <a href="{{route('admin.semesters.delete', $sem->id)}}" class="btn btn-danger btn-sm">delete</a> |
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection