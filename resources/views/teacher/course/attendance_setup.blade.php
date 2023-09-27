@extends('teacher.layout')
@section('section')

<div class="py-4">
    <form class="d-block col-xs-12 col-sm-10 col-md-8 mx-auto py-4 px-2" action="{{ route('user.course.attendance.record', $teacher_subject->id) }}" method="GET">
        <div class="input-group my-2 border">
            <span class="input-group-text text-primary border-0 border-collapse col-sm-4 rounded-0">{{ __('text.word_course') }}</span>
            <input class="form-control border-collapse border-0 rounded-0" readonly value="{{ $teacher_subject->subject->name }}">
        </div>
        <div class="input-group my-2 border">
            <span class="input-group-text text-primary border-0 border-collapse col-sm-4 rounded-0">{{ __('text.word_class') }}</span>
            <input class="form-control border-collapse border-0 rounded-0" readonly value="{{ $teacher_subject->class->name() }}">
        </div>
        <div class="d-flex justify-content-end py-3">
            <a href="{{ URL::previous() }}" class="btn btn-sm btn-danger">{{ __('text.word_back') }}</a> | 
            <input type="submit" value="{{ __('text.word_confirm') }}" class="btn btn-sm btn-primary">
        </div>
    </form>
</div>
@endsection