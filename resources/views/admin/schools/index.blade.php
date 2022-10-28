@extends('admin.layout')
@section('section')
    <div class="container">
        <div class="py-3 text-capitalize px-3">
            <a href="{{route('admin.schools.create')}}" class="btn btn-primary btn-sm">add school</a>
        </div>
        <table class="table table-stripped">
            <thead>
                <th>###</th>
                <th>Name</th>
                <th>Contact</th>
                <th>Address</th>
                <th></th>
            </thead>
            <tbody>
                @php($k = 1)
                @foreach(\App\Models\School::all() as $schl)
                <tr class="border-bottom">
                    <td>{{$k++}}</td>
                    <td>{{$schl->name}}</td>
                    <td>{{$schl->contact}}</td>
                    <td>{{$schl->address}}</td>
                    <td class="d-flex justify-content-end">
                        <span><a href="{{route('admin.schools.edit', $schl->id)}}" class="btn btn-sm btn-primary mx-1">edit</a></span>
                        <span><a href="{{route('admin.schools.preview', $schl->id)}}" class="btn btn-sm btn-success mx-1">details</a></span>
                        <span><a href="{{route('admin.schools.delete', $schl->id)}}" class="btn btn-sm btn-danger mx-1">delete</a></span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection