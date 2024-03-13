@extends('admin.layout')
@section('section')
    <div class="py-4 container-fluid">
        <form method="GET" class="card">
            @csrf
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
            {{-- <hr> --}}
            <div class="py-3 my-4 text-center">
                <div class="h5 text-secondary">Search course by title or course code </div>
                <input type="search" name="" id="" oninput="getCourses(this)" class="form-control rounded text-center" placeholder="Search course by title or course code">
            </div>

            <table class="table">
                <thead class="text-capitalize">
                    <th>@lang('text.sn')</th>
                    <th>@lang('text.course_title')</th>
                    <th>@lang('text.course_code')</th>
                    <th>@lang('text.word_semester')</th>
                    <th>@lang('text.word_level')</th>
                    {{-- <th>@lang('text.result_code')</th> --}}
                    <th>@lang('text.word_action')</th>
                </thead>
                <tbody id="xcourses_body"></tbody>
            </table>
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
                let counter = 1;
                data.forEach(element => {
                    html += `
                    <tr>
                        <td>${counter++}</td>
                        <td>${element.name}</td>
                        <td>${element.code}</td>
                        <td>${element._semester}</td>
                        <td>${element._level}</td>
                        {{-- <td>${element._code}</td> --}}
                        <td>
                            <a class="btn btn-xs rounded btn-primary mx-1 my-1" href="{{ route('admin.result.coded.course', [$year->id??'', $semester->id??'', '__COID__']) }}">details</a> |
                            <a class="btn btn-xs rounded btn-warning mx-1 my-1" href="{{ route('admin.result.coded.course.import.exam', [$year->id??'', $semester->id??'', '__COID__']) }}">@lang('text.import_exam')</a> |
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