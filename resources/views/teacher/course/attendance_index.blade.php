@extends('teacher.layout')
@section('section')
<!-- page start-->

<div class="col-sm-12">
    {{-- <p class="text-muted">
    <h4 class="text-capitalize">{{__('text.my_courses')}}</h4>
    </p> --}}

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
                    @foreach($courses as $k=>$subject)
                    <tr>
                        @php($class = \App\Models\ProgramLevel::find($subject->class))
                        <td>{{ $k+1 }}</td>
                        <td>{{ $subject->code }}</td>
                        <td>{{ $subject->name }}</td>
                        <td>{{$class->program()->first()->name.': LEVEL '.$class->level()->first()->level}}</td>
                        <td>{{ \App\Models\Campus::find($subject->campus_id)->name ?? '----' }}</td>
                        <td>{{ \App\Models\Semester::find($subject->semester_id)->name }}</td>
                        <td style="float: right;" class="d-flex">
                            <a class="btn btn-xs btn-success text-capitalize" href="{{route('user.course.attendance.setup', [$subject->teacher_subject_id])}}">{{__('text.take_attendance')}}</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection