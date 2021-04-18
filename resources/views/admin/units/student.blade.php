@extends('admin.layout')

@section('section')
    <div class="col-sm-12">

        <div class="content-panel">
            <div class="adv-table table-responsive">
                <table cellpadding="0" cellspacing="0" border="0" class="table" id="hidden-table-info">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Gender</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($students as $k=>$student)
                        <tr>
                            <td>{{$k+1}}</td>
                            <td>{{$student->name}}</td>
                            <td>{{$student->email}}</td>
                            <td>{{$student->phone}}</td>
                            <td>{{$student->address}}</td>
                            <td>{{$student->gender}}</td>
                            <td style="float: right;">
                                <a class="btn btn-xs btn-primary" href="{{route('admin.student.show',[$student->id])}}"><i class="fa fa-eye"> Profile</i></a> |
                                <a class="btn btn-xs btn-success" href="{{route('admin.student.edit',[$student->id])}}"><i class="fa fa-edit"> Edit</i></a> |
                                <a onclick="event.preventDefault();
                                            document.getElementById('delete').submit();" class=" btn btn-danger btn-xs m-2">Delete</a>
                                <form id="delete" action="{{route('admin.student.destroy',$student->id)}}" method="POST" style="display: none;">
                                    @method('DELETE')
                                    {{ csrf_field() }}
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="d-flex justify-content-end">
                    {{$students->links()}}
                </div>
            </div>
        </div>
    </div>
@endsection
