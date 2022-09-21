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
                @foreach(\App\Models\Schools::all() as $schl)
                <tr class="border-bottom">
                    <td>{{$k++}}</td>
                    <td>{{$schl->name}}</td>
                    <td>{{$schl->contact}}</td>
                    <td>{{$schl->address}}</td>
                    <td class="d-flex justify-content-end">
                        <a href="{{route('admin.schools.edit', $schl->id)}}" class="btn btn-sm btn-primary rounded"><i class="fas fa-edit fa-2x"></i>edit</a>
                        <a href="{{route('admin.schools.details', $schl->id)}}" class="btn btn-sm btn-primary rounded"><i class="fw-bolder">...</i>details</a>
                        <a href="{{route('admin.schools.delete', $schl->id)}}" class="btn btn-sm btn-primary rounded"><i class="fas fa-trash-alt fa-2x"></i>delete</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection