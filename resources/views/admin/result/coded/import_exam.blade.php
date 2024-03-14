@extends('admin.layout')
@section('section')
    <div class="py-4 container-fluid">
        <div class="d-flex justify-content-end py-2 px-2">
            <a href="{{ route('admin.result.coded.course.import.exam.coded', [$year->id??'', $semester->id??'', $course->id??'']) }}" class="btn btn-primary btn-sm mx-1 my-1">@lang('text.import_coded_results')</a>
        </div>
        <div class="row py-5">
            <div class="col-md-6 col-xl-5">
                <form method="post" class="card">
                    @csrf
                    {{-- <div class="text-secondary card-header">Update result code for {{ $course->name??'' }} [{{ $course->code??'' }}]</div> --}}
                    <div class="row card-bopdy">
                        <div class="text-white text-capitalize col-sm-12 col-md-3">
                            <span class="form-control border-0 bg-dark">@lang('text.word_file') [.csv]</span>
                        </div>
                        <div class="col-col-sm-9 col-md-6">
                            <input type="file" name="file" id="" class="form-control rounded border-0 border-bottom" placeholder="select file" value="{{ old('file') }}">
                        </div>
                        <div class="col-col-sm-3 col-md-2">
                            <button class="form-control btn btn-secondary" type="submit">@lang('text.word_import')</button>
                        </div>
                    </div>
                </form>

                <hr>

                <table class="table-dark col-md-10 col-lg-8 margin-top-5">
                    <thead class="text-capitalize">
                        <tr>
                            <th colspan="2" class="text-center border-bottom border-white">@lang('text.file_format_csv')</th>
                        </tr>
                        <tr class="border-bottom border-dark">
                            <th>@lang('text.word_matricule')</th>
                            <th>@lang('text.exam_mark')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for($i = 0; $i < 3; $i++)
                            <tr>
                                <td>---------</td>
                                <td>---------</td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
            <div class="py-3 px-2 col-md-6 col-xl-7">
                <div class="card w-100">
                    <div class="card-body">
                        <table class="table">
                            <thead class="text-capitalize">
                                <tr class="border-bottom border-white">
                                    <th colspan="6" class="text-center">
                                        <form action="{{ route('admin.result.coded.course.import.exam.undo', [$year->id??'', $semester->id??'', $course->id??'']) }}" method="post" class="row">
                                            @csrf
                                            <div class="col-sm-8 col-xl-8">
                                                <select name="class_id" id="" class="form-control border-top-0 border-bottom-0">
                                                    <option value="">@lang('text.select_class')</option>
                                                    @foreach($classes as $key => $value)
                                                        <option value="{{ $value->id }}">{{ $value->name() }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-sm-4 col-xl-4"><button class="btn btn-warning rounded form-control">@lang('text.clear_exam')</button></div>
                                        </form>
                                    </th>
                                </tr>
                                <tr class="border-bottom border-white">
                                    <th>@lang('text.sn')</th>
                                    <th>@lang('text.matricule')</th>
                                    <th>@lang('text.course_code')</th>
                                    <th>@lang('text.academic_year')</th>
                                    <th>@lang('text.ca_mark')</th>
                                    <th>@lang('text.exam_mark')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $k = 1;
                                @endphp
                                @foreach($results as $key => $value)
                                    <tr>
                                        <td>{{ $k++ }}</td>
                                        <td>{{ $value->student->matric }}</td>
                                        <td>{{ $value->subject->code }}</td>
                                        <td>{{ $value->year->name }}</td>
                                        <td>{{ $value->ca_score }}</td>
                                        <td>{{ $value->exam_score }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

@endsection
