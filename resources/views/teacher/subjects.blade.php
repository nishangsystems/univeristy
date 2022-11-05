@extends('teacher.layout')
@section('section')
<!-- page start-->

<div class="col-sm-12">
    <p class="text-muted">
    <h4 class="text-capitalize">{{__('text.my_courses')}}</h4>
    </p>

    <div class="content-panel">
        <div class="adv-table table-responsive">
            <table cellpadding="0" cellspacing="0" border="0" class="table" id="hidden-table-info">
                <thead>
                    <tr class="text-capitalize">
                        <th>#</th>
                        <th>{{__('text.course_code')}}</th>
                        <th>{{__('text.word_name')}}</th>
                        <th>{{__('text.word_class')}}</th>
                        <th>{{__('text.word_campus')}}</th>
                        <th>{{__('text.word_semester')}}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>

                    @if(\request('class'))
                        @php($k = 1)
                        @foreach($subjects as $subject)
                        <tr>
                            @php($class = \App\Models\ProgramLevel::find(request('class')))
                            <td>{{ $k++ }}</td>
                            <td>{{ $subject->code }}</td>
                            <td>{{ $subject->name }}</td>
                            <td>{{$class->program()->first()->name.': LEVEL '.$class->level()->first()->level}}</td>
                            <td>{{ \App\Models\Campus::find($subject->campus_id)->name ?? '----' }}</td>
                            <td>{{ $subject->coef }}</td>
                            <?php /*<td style="float: right;">
                                <a class="btn btn-xs btn-primary" href="{{route('user.result', ['subject'=>$subject->id, 'class'=>request('class')])}}">Result</a>
                            </td> */
                            ?>
                        </tr>
                        @endforeach
                    @else

                    @foreach($courses as $k=>$subject)
                    <tr>
                        @php($class = \App\Models\ProgramLevel::find($subject->class))
                        <td>{{ $k+1 }}</td>
                        <td>{{ $subject->code }}</td>
                        <td>{{ $subject->name }}</td>
                        <td>{{$class->program()->first()->name.': LEVEL '.$class->level()->first()->level}}</td>
                        <td>{{ \App\Models\Campus::find($subject->campus_id)->name ?? '----' }}</td>
                        <td>{{ \App\Models\Semester::find($subject->semester_id)->name }}</td>
                        <?php /* <td style="float: right;">
                            <a class="btn btn-xs btn-primary" href="{{route('user.result', [$subject->id])}}">Result</a>
                        </td> */
                        ?>
                        <td style="float: right;" class="d-flex">
                            <a class="btn btn-xs btn-success text-capitalize" href="{{route('course.notification.index', [$subject->id])}}">{{__('text.word_notifications')}}</a> |
                            <a class="btn btn-xs btn-primary text-capitalize" href="{{route('user.subject.students', [$subject->class, $subject->id])}}?campus_id={{$subject->campus_id}}">{{__('text.word_students')}}</a> |
                            <a class="btn btn-xs btn-success text-capitalize" href="{{route('user.subject.show', [$subject->class, $subject->id])}}">{{__('text.upload_material')}}</a>
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