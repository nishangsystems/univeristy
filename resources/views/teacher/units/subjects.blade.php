@extends('teacher.layout')

@section('section')
<!-- page start-->
    <div class="col-sm-12">
        <p class="text-muted">
            <a href="{{route('user.programs.manage_courses', request('program_level_id'))}}" class="btn btn-info btn-xs text-capitalize">Manage Subjects</a>
        </p>
        <div class="content-panel">
            <div class="adv-table table-responsive">
                <table cellpadding="0" cellspacing="0" border="0" class="table table-stripped table-bordered" id="hidden-table-info">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{__('text.course_code')}}</th>
                            <th>{{__('text.word_name')}}</th>
                            <th>{{__('text.word_coefficient')}}</th>
                            <th>{{__('text.word_semester')}}</th>
                            <th>{{__('text.word_level')}}</th>
                            <th>{{__('text.word_status')}}</th>
                            <th>{{__('text.word_hours')}}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $k = 1; @endphp
                        @foreach($subjects as $subject)
                            <tr>
                                <td>{{ $k++ }}</td>
                                <td>{{ $subject->code }}</td>
                                <td>{{ $subject->name }}</td>
                                <td>{{ \App\Models\ClassSubject::where(['class_id'=>request('program_level_id'), 'subject_id'=>$subject->id])->first()->coef??null }}</td>
                                <td>{{ \App\Models\Semester::find($subject->semester_id)->name }}</td>
                                <td>{{ \App\Models\Level::find($subject->level_id)->level }}</td>
                                <td>{{ $subject->status }}</td>
                                <td>{{ \App\Models\ClassSubject::where(['class_id'=>request('program_level_id'), 'subject_id'=>$subject->id])->first()->hours??null }}</td>
                                <td class="d-flex justify-content-end">
                                    <a class="btn btn-sm btn-primary" href="{{route('user.class_course.ca.result',[request('program_level_id'), $subject->id])}}">
                                        <i class="fa fa-tick"> {{__('text.word_results')}}</i>
                                    </a>|
                                    <a class="btn btn-sm btn-info" href="{{route('user.edit.class_courses',[request('program_level_id'), $subject->id])}}">
                                        <i class="fa fa-edit"> {{__('text.word_edit')}}</i>
                                    </a>|
                                    <form action="{{route('user.delete.class_courses',[request('program_level_id'), $subject->id])}}" method="post" id="del{{ $subject->id }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="event.preventDefault(); confirm('You are about to delete {{ $subject->name }} [{{ $subject->code }}] from {{ $parent->name() }}. Agree to proceed.') ? $('#del{{ $subject->id }}').submit() : null;">
                                            <i class="fa fa-trash"> {{__('text.word_delete')}}</i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                
            </div>
        </div>
    </div>
@endsection