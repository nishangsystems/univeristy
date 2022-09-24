@extends('admin.layout')
@section('section')
<div class="w-100 py-5">
    <div class="py-5 px-4 mx-auto">
        <form action="{{route('admin.semesters.store', request('program_id'))}}" method="post">
            @csrf
            <input type="hidden" name="program_id" value="{{request('program_id')}}" id="">
            <div class="row text-capitalize my-3">
                <label for="" class="col-sm-3 col-md-2 col-lg-2">name</label>
                <div class="col-sm-19 col-md-10 col-ld-10">
                    <input type="text" name="name" id="" class="form-control" required>
                </div>
            </div>
            <div class="d-flex justify-content-end py-2">
                <button class="btn btn-primary btn-sm" type="submit">create</button>
            </div>
        </form>
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
                        <a href="{{route('admin.semesters.delete', $sem->id)}}" class="btn btn-primary btn-sm">delete</a> |
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection