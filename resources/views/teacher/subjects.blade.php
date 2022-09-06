@extends('teacher.layout')
@section('section')
<!-- page start-->

<div class="col-sm-12">
    <p class="text-muted">
    <h4>My Subjects</h4>
    </p>

    <div class="content-panel">
        <div class="adv-table table-responsive">
            <table cellpadding="0" cellspacing="0" border="0" class="table" id="hidden-table-info">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Coefficient</th>
                        <th>Class</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>

                    @if(\request('class'))
                
                        @foreach($subjects as $k=>$subject)
                        <tr>
                            <td>{{ $k+1 }}</td>
                            <td>{{ $subject->subject->name }}</td>
                            <td>{{ $subject->subject->coef }}</td>
                            <td>{{$classes[request('class')]}}</td>
                            <td style="float: right;">
                                <a class="btn btn-xs btn-primary" href="{{route('user.result', ['subject'=>$subject->id, 'class'=>request('class')])}}">Result</a>
                            </td>
                        </tr>
                        @endforeach
                    @else

                    @foreach($subjects as $k=>$subject)
                    <tr>
                        <td>{{ $k+1 }}</td>
                        <td>{{ $subject->subject->subject->name }}</td>
                        <td>{{ $subject->subject->subject->coef }}</td>
                        <td>{{$classes[$subject->class_id]}}</td>
                        <td style="float: right;">
                            <a class="btn btn-xs btn-primary" href="{{route('user.result', [$subject->id])}}">Result</a>
                        </td>
                        <td style="float: right;">
                            <a class="btn btn-xs btn-success" href="{{route('user.subject.show', [$subject->class_id, $subject->id])}}">View More</a>
                        </td>
                    </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection