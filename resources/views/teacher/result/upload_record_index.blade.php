@extends('teacher.layout')
@section('section')
    <div class="py-3">
        <div class="card my-4 py-2 ">
            <div class=" card-body border-top border-bottom border-secondary">
                <div class="row my-2 py-1 border-bottom border-light">
                    <div class="col-sm-6 col-lg-4 text-capitalize">@lang('text.CA')</div>
                    <div class="d-none d-lg-block col-lg-4 text-capitalize">{{ ($semester == null ? '' : "SEMESTER: ".$semester->name." | ").($year == null ? '' : "YEAR: ".$year->name) }}</div>
                    <div class="col-sm-6 col-lg-4 text-capitalize">
                        <a class="btn btn-light btn-sm rounded px-5" href="{{ route('user.results.ca.upload_report', ['year'=>$year->id, 'semester'=>$semester->id, 'program_level'=>$class->id]) }}">@lang('text.ca_upload_report')</a>
                    </div>
                </div>
                <div class="row my-2 py-1 border-bottom border-light">
                    <div class="col-sm-6 col-lg-4 text-capitalize">@lang('text.word_exams')</div>
                    <div class="d-none d-lg-block col-lg-4 text-capitalize">{{ ($semester == null ? '' : "SEMESTER: ".$semester->name." | ").($year == null ? '' : "YEAR: ".$year->name) }}</div>
                    <div class="col-sm-6 col-lg-4 text-capitalize">
                        <a class="btn btn-light btn-sm rounded px-5" href="{{ route('user.results.exam.upload_report', ['year'=>$year->id, 'semester'=>$semester->id, 'program_level'=>$class->id]) }}">@lang('text.exam_upload_report')</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection