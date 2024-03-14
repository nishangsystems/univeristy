@extends('admin.layout')
@section('section')
    <div class="py-4 container-fluid">
        <form method="GET" class="card">
            <div class="row card-bopdy">
                <div class="col-sm-5 col-md-5">
                    <select name="year_id" class="form-control rounded border-0 border-bottom" required>
                        <option value="">@lang('text.word_year')</option>
                        @foreach(\App\Models\Batch::all() as $key => $value)
                            <option value="{{ $value->id }}" {{ old('year_id') == $value->id ? 'selected' : '' }}>{{ $value->name??'' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-5 col-md-5">
                    <select name="semester_id" class="form-control rounded border-0 border-bottom" required>
                        <option value="">@lang('text.word_semester')</option>
                        @foreach(\App\Models\Semester::all() as $key => $value)
                            <option value="{{ $value->id }}" {{ old('semester_id') == $value->id ? 'selected' : '' }}>{{ $value->name??'' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2 col-md-2">
                    <button class="form-control btn btn-primary border-0" type="submit">@lang('text.word_initialise')</button>
                </div>
            </div>
        </form>
        <hr>
        @isset($year)
        <div class="d-flex justify-content-end py-3 flex-wrap">
            <a href="{{ route('admin.result.coded.courses', [$year->id, $semester->id]) }}" class="text-capitalize btn btn-success rounded">@lang('text.import_course_codes')</a> 
        </div>
        <div class="row my-5">
            <div class="col-md-6 col-lg-6">
                <div class="container-fluid">
                    {{-- <hr> --}}
                    <div class="py-3 my-4 text-center">
                        <div class="h5 text-secondary">Search course by title or course code </div>
                        <input type="search" name="" id="" oninput="getCourses(this)" class="form-control rounded text-center" placeholder="Search course by title or course code">
                    </div>
        
                    <table class="table">
                        <thead class="text-capitalize">
                            <th>@lang('text.course_title')</th>
                            <th>@lang('text.course_code')</th>
                            <th>@lang('text.word_semester')</th>
                            <th>@lang('text.word_level')</th>
                            {{-- <th>@lang('text.result_code')</th> --}}
                            <th>@lang('text.word_action')</th>
                        </thead>
                        <tbody id="xcourses_body"></tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-6 col-lg-6">
                <div class="container-fluid">
                    <table class="table">
                        <thead class="text-capitalize">
                            <tr>
                                <th colspan="5" class="text-center">
                                    <span class="h4 text-capitalize text-info font-weight-bold">@lang('text.existing_data')</span>
                                </th>
                            </tr>
                            <tr>
                                <th>#</th>
                                <th>@lang('text.course_title')</th>
                                <th>@lang('text.course_code')</th>
                                <th>@lang('text.exam_code')</th>
                                <th>@lang('text.word_action')</th>
                            </tr>
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
        @endisset
    </div>
@endsection
@section('script')
<script>
    let getCourses = function(element){
        let searchKey = $(element).val();
        let _url = "{{ route('search_courses') }}";
        $.ajax({
            method: "GET", url: _url, data: {'name': searchKey},
            success: function(data){
                console.log(data);
                let html = ``;
                data.forEach(element => {
                    html += `
                    <tr>
                        <td>${element.name}</td>
                        <td>${element.code}</td>
                        <td>${element._semester}</td>
                        <td>${element._level}</td>
                        {{-- <td>${element._code}</td> --}}
                        <td>
                            <a class="btn btn-xs rounded btn-primary mx-1 my-1" href="{{ route('admin.result.coded.course', [$year->id??'', $semester->id??'', '__COID__']) }}">details</a>
                            <a class="btn btn-xs rounded btn-warning mx-1 my-1" href="{{ route('admin.result.coded.course.import.exam', [$year->id??'', $semester->id??'', '__COID__']) }}">@lang('text.import_exam')</a>
                            <a class="btn btn-xs rounded btn-success mx-1 my-1" href="{{ route('admin.result.coded.course.import.ca', [$year->id??'', $semester->id??'', '__COID__']) }}">@lang('text.import_ca')</a>
                        </td>
                    </tr>
                    `.replaceAll('__COID__', element.id);
                });
                $('#xcourses_body').html(html);
            }
        })
    }
</script>
@endsection