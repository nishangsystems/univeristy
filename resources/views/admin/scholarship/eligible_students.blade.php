@extends('admin.layout')

@section('section')

<div class="col-sm-12">
    <div class="col-sm-12">
        <div class="mb-3 d-flex justify-content-start">
            <h4 class="font-weight-bold">Eligible Students</h4>
        </div>

    </div>
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
                        <td class="d-flex justify-content-end align-items-center">
                            <a class="btn btn-sm btn-primary" href="{{route('admin.scholarship.award.create', $student->id)}}"><i class="fa fa-money"> Award Scholarship</i></a> |

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