@extends('admin.layout')

@section('section')
<div class="mx-3">
    <div class="form-panel">
        <form class="form-horizontal" role="form" method="POST" action="{{route('admin.units.class_subjects.update',  [$parent->id,  $subject->subject_id])}}">
            @csrf
            @method('PUT')
            <div class="form-group row">
                <label for="cname" class="control-label col-sm-2 text-capitalize">{{__('text.word_name')}}: </label>
                <div class="col-sm-10">
                    <input for="cname" class="form-control" name="name" value="{{$subject->name}}"></input>
                </div>
            </div>
            <div class="form-group">
                <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.word_coefficient')}}:</label>
                <div class="col-lg-10">
                    <input for="cname" class="form-control" name="coef" value="{{$subject->coef}}"></input>
                </div>
            </div>
            <div class="form-group">
                <div class="d-flex justify-content-end col-lg-12">
                    <button id="save" class="btn btn-xs btn-primary mx-3 text-capitalize" type="submit">{{__('text.word_save')}}</button>
                    <a class="btn btn-xs btn-danger text-capitalize" href="{{route('admin.units.subjects', $parent->id)}}" type="button">{{__('text.word_cancel')}}</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection