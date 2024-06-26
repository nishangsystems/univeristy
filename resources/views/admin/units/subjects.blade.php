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
                        {{-- <th>{{__('text.word_level')}}</th> --}}
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
                        <td>{{ $subject->_coef ?? $subject->coef }}</td>
                        <td>{{ \App\Models\Semester::find($subject->semester_id)->name }}</td>
                        {{-- <td>{{ \App\Models\Level::find($subject->level_id)->level }}</td> --}}
                        <td>{{ $subject->_status ?? $subject->status }}</td>
                        <td class="d-flex">
                            <a class="btn btn-sm btn-info" href="{{route('admin.units.course_master',['program_level_id'=>request('program_level_id'), 'course_id'=>$subject->id])}}">
                                <i class="fa fa-user text-capitalize"> {{__('text.course_master')}}</i>
                            </a> |
                            <a class="btn btn-sm btn-primary" href="{{route('admin.edit.class_subjects',[request('program_level_id'), $subject->id])}}">
                                <i class="fa fa-edit text-capitalize"> {{__('text.word_edit')}}</i>
                            </a> |
                            <a class="btn btn-sm btn-danger" onclick="window.location=confirm('You are about to delete a course from a class. Confirm to continue.')? `{{ route('admin.delete.class_subjects',[request('program_level_id'), $subject->id])}}` : `#`">
                                <i class="fa fa-trash text-capitalize"> {{__('text.word_delete')}}</i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
        </div>
    </div>
</div>
@endsection