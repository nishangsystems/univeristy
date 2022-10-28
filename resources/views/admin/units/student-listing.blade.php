@extends('admin.layout')

@section('section')
    <div class="col-sm-12">

        <div class="content-panel">
            <div class="table-responsive">
                <table  border="0" class="table table-bordered" >
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Matricule</th>
                        @if(request('action') != 'class_list')
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Class</th>
                            <th></th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                        @php($k = 1)
                        @foreach($students as $student)
                            <tr>
                                <td>{{$k}}</td>
                                <td>{{$student->name}}</td>
                                <td>{{$student->matric}}</td>
                                @if(request('action') != 'class_list')
                                    <td>{{$student->email}}</td>
                                    <td>{{$student->phone}}</td>
                                    <td>{{$student->address}}</td>
                                    <td>{{\App\Models\ProgramLevel::find($student->program_id)->program()->first()->name.' : Level '.\App\Models\ProgramLevel::find($student->program_id)->level()->first()->level}}</td>
                                    <?php //<td>{{$classes[$student->class_id]}}</td> ?>
                                    <td class="d-flex justify-content-end align-items-center" >
                                        <a class="btn btn-xs btn-primary" href="{{route('admin.student.show',[$student->id])}}"><i class="fa fa-eye"> Profile</i></a>
                                        @if(request('action') != 'class_list')
                                            | <a class="btn btn-xs btn-success" href="{{route('admin.student.edit',[$student->id])}}"><i class="fa fa-edit"> Edit</i></a> |
                                            <a onclick="event.preventDefault();
                                                                            document.getElementById('delete').submit();" class=" btn btn-danger btn-xs m-2">Delete</a>
                                            <form id="delete" action="{{route('admin.student.destroy',$student->id)}}" method="POST" style="display: none;">
                                                @method('DELETE')
                                                {{ csrf_field() }}
                                            </form>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                            @php($k += 1)
                        @endforeach
                    </tbody>
                </table>
                <div class="d-flex justify-content-end">
                    {{--{{$students->links()}}--}}
                </div>
            </div>
        </div>
    </div>
@endsection
