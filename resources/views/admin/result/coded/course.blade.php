@extends('admin.layout')
@section('section')
    <div class="py-4 container-fluid">
        <div class="row py-5">
            <div class="col-md-5 col-xl-6">
                <form method="post" class="card">
                    @csrf
                    <div class="text-secondary card-header">Update result code for {{ $course->name??'' }} [{{ $course->code??'' }}]</div>
                    <div class="row card-body p-0">
                        <div class="text-white text-capitalize col-sm-12 col-md-3">
                            <span class="form-control border-0 bg-dark">@lang('text.result_code')</span>
                        </div>
                        <div class="col-sm-9 col-md-6">
                            <input type="text" name="code" id="" class="form-control rounded border-0 border-bottom" placeholder="result code here" value="{{ old('code', $exam_code->exam_code??'') }}">
                        </div>
                        <div class="col-sm-3 col-md-2">
                            @if($exam_code->exam_code != null)
                                <button class="form-control btn btn-success" type="submit">@lang('text.word_update')</button>
                            @else
                                <button class="form-control btn btn-primary" type="submit">@lang('text.word_save')</button>
                            @endif
                        </div>
                    </div>
                </form>
                <hr>
                <div class="py-3 my-4 text-center">
                    <div class="d-flex justify-content-end py-3">
                        <a href="{{ route('admin.result.coded.course.import.ca', [$year->id??'', $semester->id??'', $course->id??'']) }}" class="btn btn-primary rounded my-1 mx-1">@lang('text.import_ca')</a>
                        <a href="{{ route('admin.result.coded.course.import.exam', [$year->id??'', $semester->id??'', $course->id??'']) }}" class="btn btn-success rounded my-1 mx-1">@lang('text.import_exam')</a>
                        <a href="{{ route('admin.result.coded.students', [$year->id??'', $semester->id??'', $course->id??'']) }}" class="btn btn-info rounded my-1 mx-1">@lang('text.student_coding')</a>
                    </div>
                    @if($has_exam)
                        <div class="alert-success fs-2 py-5">@lang('text.results_already_exist_for_accademic_year')</div>
                    @else
                        <div class="alert-danger fs-2 py-5">@lang('text.no_results_found_for_this_accademic_year')</div>
                    @endif
                </div>
            </div>
            <div class="col-md-7 col-xl-6">
                <div class="card">
                    <div class="card-body">
                        <table class="table">
                            <thead class="text-capitalize">
                                <th>#</th>
                                <th>@lang('text.course_title')</th>
                                <th>@lang('text.course_code')</th>
                                <th>@lang('text.exam_code')</th>
                                <th>@lang('text.word_action')</th>
                            </thead>
                            <tbody>
                                @php
                                    $k =1;
                                @endphp
                                @foreach($codes as $key => $value)
                                    <tr>
                                        <td>{{ $k++ }}</td>
                                        <td>{{ $value->course->name }}</td>
                                        <td>{{ $value->course->code }}</td>
                                        <td>{{ $value->exam_code }}</td>
                                        <td>
                                            <a href="{{ route('admin.result.coded.course', [$year->id??'', $semester->id??'', $value->course_id??'']) }}" class="btn btn-primary btn-xs rounded">@lang('text.word_edit')</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        

@endsection
