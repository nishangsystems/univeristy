@extends('admin.layout')
@section('section')
    <div class="py-4 container-fluid">
        <form method="post" class="card">
            @csrf
            <div class="text-secondary card-header">Update result code for {{ $course->name??'' }} [{{ $course->code??'' }}]</div>
            <div class="row card-bopdy">
                <div class="text-white text-capitalize col-sm-12 col-md-3">
                    <span class="form-control border-0 bg-dark">@lang('text.result_code')</span>
                </div>
                <div class="col-col-sm-9 col-md-7">
                    <input type="text" name="code" id="" class="form-control rounded border-0 border-bottom" placeholder="result code here" value="{{ old('code', $exam_code->exam_code??'') }}">
                </div>
                <div class="col-col-sm-3 col-md-2">
                    <button class="form-control btn btn-secondary" type="submit">@lang('text.word_update')</button>
                </div>
            </div>
        </form>
        <hr>
        <div class="py-3 my-4 text-center">
            <div class="d-flex justify-content-end py-3">
                <a href="{{ route('admin.result.coded.course.import', $course->id??'') }}" class="btn btn-primary rounded">@lang('text.import_exam')</a>
            </div>
            @if($exam_code->results()->whereNotNull('exam_score')->count() > 0)
                <div class="alert-success fs-2 py-5">@lang('text.results_already_exist_for_accademic_year')</div>
            @else
                <div class="alert-danger fs-2 py-5">@lang('text.no_results_found_for_this_accademic_year')</div>
            @endif
        </div>

@endsection
