@extends('admin.layout')
@section('section')
    <div class="py-4 container-fluid">
        <div class="d-flex justify-content-end py-3 flex-wrap">
            <a href="{{ route('admin.result.coded.index') }}?action=all" class="text-capitalize btn btn-secondary rounded">@lang('text.word_courses')</a> |
            <a href="{{ route('admin.result.coded.courses') }}" class="text-capitalize btn btn-success rounded">@lang('text.import_course_codes')</a> |
            <a href="{{ route('admin.result.coded.students') }}" class="text-capitalize btn btn-info rounded">@lang('text.import_student_codes')</a> |
            <a href="{{ route('admin.result.coded.import') }}" class="text-capitalize btn btn-primary rounded">@lang('text.import_exam')</a>
        </div>
        <hr>
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
                <th>@lang('text.result_code')</th>
                <th>@lang('text.word_action')</th>
            </thead>
            <tbody id="xcourses_body"></tbody>
        </table>
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
                        <td>${element._code}</td>
                        <td>
                            <a class="btn btn-xs rounded btn-primary" href="{{ route('admin.result.coded.course', '__COID__') }}">more</a> |
                            <a class="btn btn-xs rounded btn-primary" href="{{ route('admin.result.coded.course.import', '__COID2__') }}">@lang('text.import_exam')</a>
                        </td>
                    </tr>
                    `.replace('__COID__', element.id).replace('__COID2__', element.id);
                });
                $('#xcourses_body').html(html);
            }
        })
    }
</script>
@endsection