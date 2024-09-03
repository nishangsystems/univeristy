@extends('admin.layout')
@section('section')
    <div class="py-4 container-fluid">
        <form method="GET" class="card shadow">
            <div class="row my-4 container-fluid p-3">
                <div class="col-sm-6 col-md-4 col-lg-3 p-2">
                    <select class="form-control" name="year_id" id="form_year" required>
                        <option></option>
                        @foreach (\App\Models\Batch::all() as $_year)
                            <option value="{{ $_year->id }}" {{ $_year->id == old('year_id', optional($year??null)->id??null) ? 'selected' : '' }}>{{ $_year->name??'' }}</option>
                        @endforeach
                    </select>
                    <span class="text-secondary">{{ __('text.academic_year') }}</span>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3 p-2">
                    <select class="form-control" name="semester_id" id="form_semester" required>
                        <option></option>
                        @foreach ($semesters??[] as $_semester)
                            <option value="{{ $_semester->id }}" {{ $_semester->id == old('year', optional($semester??null)->id??null) ? 'selected' : '' }}>{{ $_semester->name??'' }}</option>
                        @endforeach
                    </select>
                    <span class="text-secondary">{{ __('text.word_semester') }}</span>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3 p-2">
                    <input class="form-control" name="course_code" id="form_course_code" value="{{ $course_code??'' }}" required oninput="search_course(this)">
                    <span class="text-secondary">{{ __('text.course_code') }}</span>
                    <div id="course_search_list" class="position-relative"></div>
                </div>
                <div class="col-sm-6 col-md-12 col-lg-3 p-2">
                    <button class="btn btn-primary rounded form-control" type="submit">{{ __('text.word_results') }}</button>
                </div>
            </div>
        </form>
        <hr>
        @isset($year)
            <div class="row my-5">
                <div class="col-md-6 col-lg-6">
                    <div class="container-fluid my-3 py-3 border-bottom border-top bg-light border-secondary">
                        <form method="POST" enctype="multipart/form-data" action="{{ route('admin.result.encoded.courses', ['semester_id'=>$semester->id, 'year_id'=>$year->id]) }}">
                            @csrf
                            <div class="header text-uppercase text-center"><b>@lang('text.import_exam_course_coding')</b></div>
                            <div class="my-2">
                                <input type="file" name="file" class="form-control" required>
                                <i>@lang('text.csv_file')<span class="text-danger">*</span></i>
                            </div>
                            <div class="my-2">
                                <button class="btn btn-sm btn-primary rounded" type="submit">@lang('text.word_import')</button>
                            </div>
                        </form>
                    </div>
                    <table class="table-stripped">
                        <thead class="text-uppercase bg-danger text-white border-top border-bottom">
                            <tr class="border-bottom border-light"><th colspan="3" class="header text-center"><b>@lang('text.file_format_csv')</b></th></tr>
                            <tr>
                                <th>@lang('text.paper_code')</th>
                                <th>@lang('text.word_matricule')</th>
                            </tr>
                        </thead>
                        <tbody class="bg-light">
                            <tr class="border-bottom border-secondary"><td>pcode</td><td>smat</td></tr>
                            <tr class="border-bottom border-secondary"><td>pcode</td><td>smat</td></tr>
                            <tr class="border-bottom border-secondary"><td>pcode</td><td>smat</td></tr>
                            <tr class="border-bottom border-secondary"><td>pcode</td><td>smat</td></tr>
                        </tbody>
                    </table>
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
                                    <th>@lang('text.word_count')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $k =1;
                                @endphp
                                @foreach($courses as $key => $value)
                                    <tr>
                                        <td>{{ $k++ }}</td>
                                        <td>{{ $value->subject->name }}</td>
                                        <td>{{ $value->subject->code }}</td>
                                        <td>{{ $value->encoded_records }}</td>
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
    let search_course = function(element){
        let value = $(element).val();
        let _url = "{{ route('search_subjects') }}?name="+value;
        $.ajax({
            method : 'GET', url : _url, 
            success : function(data){
                let html = `<div class="my-2 px-2 py-3 position-absolute border-left border-right bg-white" style="max-height: 40rem; overflow-y: scroll; z-index: 20;">`;
                console.log(data.data);
                data.data.forEach(element => {
                    html += `<div class="alert-success p-2  border-top border-bottom my-1" onclick="pickCourse('${element.code}')">
                            <b class="text-danger">${element.code}</b> || <small class="text-uppercase text-secondary">${element.name}</small>
                        </div>`
                });
                html += `</div>`;
                $('#course_search_list').html(html);
            }
        })
    }

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
                        <td></td>
                    </tr>
                    `.replaceAll('__COID__', element.id);
                });
                $('#xcourses_body').html(html);
            }
        })
    }

    
    let pickCourse = function(ccode){
        $('#form_course_code').val(ccode);
        $('#course_search_list').html(null);
    }

</script>
@endsection