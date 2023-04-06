@extends('teacher.layout')

@section('section')
<!-- page start-->

<div class="col-sm-12">
    <div class="content-panel">
        <form class="form py-5 px-3 col-md-6 mx-auto" method="post">
            @csrf
            <input type="hidden" name="_id" value="{{$class_subject->id}}">
            <div class="py-2">
                <label class="text-capitalize">{{__('text.word_class')}}</label>
                <input class="form-control" name="class_id" type="text" value="{{$class_subject->class->name()}}" readonly>
            </div>
            <div class="py-2">
                <label class="text-capitalize">{{__('text.word_course')}}</label>
                <input class="form-control" name="subject_id" type="text" value="{{$class_subject->subject->name}}" readonly>
            </div>
            <div class="py-2">
                <label class="text-capitalize">{{__('text.word_status')}}</label>
                <input class="form-control" name="status" type="text" value="{{$class_subject->status}}" readonly>
            </div>
            <div class="py-2">
                <label class="text-capitalize">{{__('text.word_hours')}}</label>
                <input class="form-control" name="hours" type="number" value="{{$class_subject->hours}}">
            </div>
            <div class="py-2">
                <label class="text-capitalize">{{__('text.word_coefficient')}}</label>
                <input class="form-control" name="coef" type="number" value="{{$class_subject->coef}}">
            </div>
            <div class="py-2 d-flex justify-content-end">
                <input type="submit" class="btn btn-xs btn-primary" value="{{__('text.word_save')}}">
            </div>
        </form>
    </div>
</div>
@endsection