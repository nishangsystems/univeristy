@extends('teacher.layout')
@section('section')
<div class="col-sm-12">
    <p class="text-muted">
    <h4 class="mb-4">{{$class->name}} Student</h4>
    </p>
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

                            @foreach(\App\Models\Term::all() as $term)

                            <a class="btn btn-xs btn-success" href="{{route('user.student.report_card',[$class->id,$term->id, $student->id])}}"><i class="fa fa-eye"> {{$term->name}} Term Report</i></a>
                            @endforeach
                            <a class="btn btn-xs btn-primary" href="{{route('user.student.show',[$student->id])}}"><i class="fa fa-eye"> Profile</i></a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="d-flex justify-content-end">
                {{-- $students->links() ?? '' --}}
            </div>
        </div>
    </div>
</div>
@endsection