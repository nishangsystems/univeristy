@extends('teacher.layout')
@section('section')
    <div class="col-sm-12">
        <p class="text-muted">
           My Master Classes
        </p>

        <div class="content-panel">
            <div class="adv-table table-responsive">
                <table cellpadding="0" cellspacing="0" border="0" class="table" id="hidden-table-info">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Class</th>
                        <th>Student Count</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach($classes as $k=>$class)
                        <tr>
                            <td>{{$k + 1 }}</td>
                            <td>{{$class->class->name }}</td>
                            <td>{{$class->class->students($year)->count()}}</td>
                            <td style="float: right;">
                                <a class="btn btn-xs btn-primary" href="{{route('user.class.rank_student', [$class->id])}}">Rank Student</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
