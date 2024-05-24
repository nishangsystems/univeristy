@extends('admin.layout')
@section('section')
    <div class="py-4 container-fluid">
        <form method="GET" class="card">
            <div class="row card-bopdy">
                <div class="col-sm-5 col-md-5">
                    <select name="year_id" class="form-control rounded border-0 border-bottom" required>
                        <option value="">@lang('text.word_year')</option>
                        @foreach(\App\Models\Batch::all() as $key => $value)
                            <option value="{{ $value->id }}" {{ old('year_id', request('year_id')) == $value->id ? 'selected' : '' }}>{{ $value->name??'' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-5 col-md-5">
                    <select name="semester_id" class="form-control rounded border-0 border-bottom" required>
                        <option value="">@lang('text.word_semester')</option>
                        @foreach(\App\Models\Semester::all() as $key => $value)
                            <option value="{{ $value->id }}" {{ old('semester_id', request('semester_id')) == $value->id ? 'selected' : '' }}>{{ $value->name??'' }}</option>
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
                            <th>@lang('text.course_code')</th>
                            <th>@lang('text.paper_code')</th>
                            <th>@lang('text.word_matricule')</th>
                        </tr>
                    </thead>
                    <tbody class="bg-light">
                        <tr class="border-bottom border-secondary"><td>ccode</td><td>pcode</td><td>smat</td></tr>
                        <tr class="border-bottom border-secondary"><td>ccode</td><td>pcode</td><td>smat</td></tr>
                        <tr class="border-bottom border-secondary"><td>ccode</td><td>pcode</td><td>smat</td></tr>
                        <tr class="border-bottom border-secondary"><td>ccode</td><td>pcode</td><td>smat</td></tr>
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
                                <th>@lang('text.exam_code')</th>
                                <th>@lang('text.word_action')</th>
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
                                    <td>{{ $value->exam_code }}</td>
                                    <td>
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
                        <td></td>
                    </tr>
                    `.replaceAll('__COID__', element.id);
                });
                $('#xcourses_body').html(html);
            }
        })
    }
</script>
@endsection