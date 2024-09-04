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
                    <input for="cname" class="form-control" name="name" readonly value="{{$subject->name}}">
                </div>
            </div>
            <div class="form-group">
                <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.word_coefficient')}}:</label>
                <div class="col-lg-10">
                    <input for="cname" class="form-control" name="coef" value="{{$subject->coef}}">
                </div>
            </div>
            <div class="form-group">
                <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.word_status')}}:</label>
                <div class="col-lg-10">
                    <select  class="form-control" name="status" value="{{$subject->status}}">
                        <option>@lang('text.select_status')</option>
                        <option value="C" {{ old('status', $subject->status) == 'C' ? 'selected' : '' }}>C</option>
                        <option value="R" {{ old('status', $subject->status) == 'R' ? 'selected' : '' }}>R</option>
                        <option value="G" {{ old('status', $subject->status) == 'G' ? 'selected' : '' }}>G</option>
                        <option value="E" {{ old('status', $subject->status) == 'E' ? 'selected' : '' }}>E</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="semester_id" class="control-label col-lg-2 text-capitalize">{{__('text.word_semester')}}:</label>
                <div class="col-lg-10">
                    <select  class="form-control" name="semester_id">
                        <option>@lang('text.select_semester')</option>
                        @foreach (\App\Models\Semester::where('is_main_semester', 1)->distinct()->get() as $semester)
                            <option value="{{$semester->id}}" {{ old('semester_id', $subject->_semester_id) == $semester->id ? 'selected' : '' }}>{{$semester->name??''}}</option>
                        @endforeach
                    </select>
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