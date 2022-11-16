@extends('admin.layout')

@section('section')
<!-- page start-->

<div class="col-sm-12">
    <p class="text-muted">
        <a href="{{route('admin.units.subjects.manage_class_subjects', $parent->id)}}" class="btn btn-info btn-xs text-capitalize">Manage Subjects</a>
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
                        <th></th>
                    </tr>
                </thead>
                <tbody>

                    @foreach($subjects as $k=>$subject)
                    <tr>
                        <td>{{ $k+1 }}</td>
                        <td>{{ $subject->code }}</td>
                        <td>{{ $subject->name }}</td>
                        <td>{{ $subject->coef }}</td>
                        <td>{{ \App\Models\Semester::find($subject->semester_id)->name }}</td>
                        <td>{{ \App\Models\Level::find($subject->level_id)->level }}</td>
                        <td>{{ $subject->status }}</td>
                        <td class="d-flex">
                            <a class="btn btn-sm btn-primary" href="{{route('admin.edit.class_subjects',[request('program_level_id'), $subject->id])}}">
                                <i class="fa fa-edit text-capitalize"> {{__('text.word_edit')}}</i>
                            </a> |
                            @if((\App\Models\Result::where(['subject_id'=>$subject->id])->count() == 0) && (\App\Models\StudentSubject::where(['course_id'=>$subject->id])->count() == 0))
                                <a class="btn btn-sm btn-danger" href="{{route('admin.delete.class_subjects',[request('program_level_id'), $subject->id])}}">
                                    <i class="fa fa-trash text-capitalize"> {{__('text.word_delete')}}</i>
                                </a>
                                @else
                                <div aria-disabled="true" class="btn btn-sm btn-dark">
                                    <i class="fa fa-trash text-capitalize"> {{__('text.word_delete')}}</i>
                                </div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
        </div>
    </div>
</div>
@endsection