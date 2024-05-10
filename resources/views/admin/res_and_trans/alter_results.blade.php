@extends('admin.layout')
@section('section')
    <div class="py-4">
        <div class="my-4 container-lg-fluid mx-auto row">
            <div class="col-lg-5 my-2">
                <input class="form-control" id="student_search" type="text" oninput="seach_student(this)">
                <span class="text-secondary text-capitalize">@lang('text.word_student')</span>
                <input name="student_id" type="hidden" id="student_id_field">
                <div class="container-fluid" id="student_display_panel"></div>
            </div>
            <div class="col-lg-2 my-2">
                <select class="form-control" id="year_id_field" name="year_id">
                    <option></option>
                    @foreach($years as $year)
                        <option value="{{ $year->id }}">{{ $year->name }}</option>
                    @endforeach
                </select>
                <span class="text-secondary text-capitalize">@lang('text.academic_year')</span>
            </div>
            <div class="col-lg-3 my-2">
                <input class="form-control" id="course_search" type="text" oninput="seach_course(this)">
                <span class="text-secondary text-capitalize">@lang('text.word_course')</span>
                <input name="course_id" type="hidden" id="course_id_field">
                <div class="container-fluid" id="course_display_panel"></div>
            </div>
            <div class="col-lg-2 my-2">
                <button class="btn btn-primary rounded btn-sm" onclick="presubmit_submit()">@lang('text.word_results')</button>
            </div>
        </div>

        @if($course_id != null)
            <div class="rounded card shadow">
                <div class="card-body">
                    <div class="row my-2">
                        <div class="col-lg-4">
                            <select class="form-control" name="semester_id">
                                <option></option>
                                @foreach ($semesters as $sem)
                                    <option value="{{ $sem->id }}" {{ old('semester_id') == $sem->id ? 'selected' : '' }}>{{ $sem->name??'' }}</option>
                                @endforeach
                            </select>
                            <span class="text-capitalize text-secondary">@lang('text.word_semester')</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection