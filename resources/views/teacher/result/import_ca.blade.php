@extends('teacher.layout')
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

        let submit_form = function(){
            let year = $('#form_year').val();
            let semester = $('#form_semester').val();
            let _class = "{{ $class_id }}";
            let course = "{{ $course->id }}"
            let url = "{{ route('user.class_course.ca.import', ['class_id'=>'_CID_', 'course_id'=>'_CSID_', 'semester'=>'_SID_', 'year'=>'_YR_']) }}".replace('_YR_', year).replace('_SID_', semester).replace('_CSID_', course).replace('_CID_', _class);
            window.location = url;
        }

        let pickCourse = function(ccode){
            $('#form_course_code').val(ccode);
            $('#course_search_list').html(null);
        }

        let clearResult = function(route){
            if(confirm(`{{ $delete_prompt ?? 'You are about to clear all exam results for this course' }}`)){
                window.location = route;
            }
        }
    </script>
@endsection
@section('section')
    <div class="py-2 container-fluid">
        <div class="row my-4 container-fluid p-3 shadow">
            <div class="col-sm-6 col-md-4 col-lg-3 col-xl-2 p-2">
                <select class="form-control" name="year" id="form_year" disabled>
                    <option></option>
                    @foreach (\App\Models\Batch::all() as $year)
                        <option value="{{ $year->id }}" {{ $year->id == old('year', $year_id) ? 'selected' : '' }}>{{ $year->name??'' }}</option>
                    @endforeach
                </select>
                <span class="text-secondary">{{ __('text.academic_year') }}</span>
            </div>
            <div class="col-sm-6 col-md-4 col-lg-3 p-2">
                <select class="form-control" name="semester" id="form_semester" disabled>
                    <option></option>
                    @foreach ($semesters??[] as $semester)
                        <option value="{{ $semester->id }}" {{ $semester->id == old('year', $semester_id) ? 'selected' : '' }}>{{ $semester->name??'' }}</option>
                    @endforeach
                </select>
                <span class="text-secondary">{{ __('text.word_semester') }}</span>
            </div>
            <div class="col-sm-6 col-md-4 col-lg-3 p-2">
                <select class="form-control" name="class" id="form_class" disabled>
                    <option></option>
                    @foreach ($classes??[] as $_class)
                        <option value="{{ $_class['id'] }}" {{ $_class['id'] == old('class', $class_id) ? 'selected' : '' }}>{{ $_class['name']??'' }}</option>
                    @endforeach
                </select>
                <span class="text-secondary">{{ __('text.word_class') }}</span>
            </div>
            <div class="col-sm-6 col-md-4 col-lg-3 col-xl-2 p-2">
                <input class="form-control" name="course_code" id="form_course_code" value="{{ $course_code??'' }}" readonly oninput="search_course(this)">
                <span class="text-secondary">{{ __('text.course_code') }}</span>
                <div id="course_search_list" class="position-relative"></div>
            </div>
            <div class="col-sm-6 col-md-12 col-lg-3 col-xl-2 p-2">
                <button class="btn btn-primary rounded form-control" onclick="submit_form()">{{ __('text.word_results') }}</button>
            </div>
        </div>

        @if($semester_id != null)
            <div class="alert-info py-3 text-center border-top border-bottom text-capitalize"><b>{{ $title2 ?? 'Uploading CA Results' }}</b></div>
            <div class="py-3 text-center border-top border-bottom text-capitalize">{!! $_title2 ?? '----------' !!}</div>
            <div class="row">
                <div class="col-lg-5">
                    <div class="">
                        <div class="alert-warning py-3 text-center text-uppercase border-top border-bottom"><b>@lang('text.upload_ca_marks_in_csv_only')</b></div>
                        @if($can_update_ca)
                            <form class="my-4" method="POST" enctype="multipart/form-data" action="{{ route('user.class_course.ca.import', ['class_id'=>$class_id, 'semester'=>$semester_id, 'year'=>$year_id, 'course_id'=>$course->id]) }}">
                                @csrf
                                <input class="form-control my-2 rounded" name="file" type="file">
                                <button class="form-control btn btn-sm btn-primary rounded my-2" type="submit">@lang('text.word_import')</button>
                            </form>
                        @else
                            <div class="alert-danger py-3 border-top border-bottom text-uppercase text-center"><b>@lang('text.cant_alter_ca_after_exam_is_uploaded')</b></div>
                        @endif
                        <table>
                            <thead class="text-uppercase">
                                <tr class="bg-light text-dark border-top border-bottom">
                                    {{-- <th>#</th> --}}
                                    <th>A</th>
                                    <th>B</th>
                                </tr>
                                <tr class="bg-light text-danger border-top border-bottom">
                                    {{-- <th></th> --}}
                                    <th>@lang('text.word_matricule')</th>
                                    <th>@lang('text.ca_mark')</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-top border-bottom">
                                    <td>DNT23D023</td>  <td>23</td>
                                </tr>
                                <tr class="border-top border-bottom">
                                    <td>DNT23D003</td>  <td>20</td>
                                </tr>
                                <tr class="border-top border-bottom">
                                    <td>DNT23D102</td>  <td>17</td>
                                </tr>
                                <tr class="border-top border-bottom">
                                    <td>DNT23D021</td>  <td>25</td>
                                </tr>
                                <tr class="border-top border-bottom">
                                    <td>DNT23D025</td>  <td>24</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="container-fluid shadow rounded py-3">
                        <div class="py-2 my-3 alert alert-info text-center border-top border-bottom">
                            <b>{{ $title2 ?? 'Uploaded CA Marks' }}</b> <hr class="my-1">
                            @if ($can_update_ca)
                                <a class="btn btn-danger btn-sm rounded px-5 py-1 text-capitalize" onclick="clearResult(`{{ route('user.class_course.ca.import.clear', ['year'=>$year_id, 'semester'=>$semester_id, 'course_id'=>$course->id, 'class_id'=>$class->id]) }}`)"><b>{{ $delete_label }}</b></a>
                            @else
                                <div class="alert-danger py-3 border-top border-bottom text-uppercase text-center"><b>@lang('text.cant_alter_ca_after_exam_is_uploaded')</b></div>
                            @endif
                        </div>
                        <table class="table-stripped">
                            <thead class="text-capitalize border-top border-bottom">
                                <th class="border-left border-right border-light">@lang('text.sn')</th>
                                <th class="border-left border-right border-light">@lang('text.word_matricule')</th>
                                <th class="border-left border-right border-light">@lang('text.course_code')</th>
                                <th class="border-left border-right border-light">@lang('text.academic_year')</th>
                                <th class="border-left border-right border-light">@lang('text.ca_mark')</th>
                                <th class="border-left border-right border-light">@lang('text.exam_mark')</th>
                            </thead>
                            <tbody>
                                @php
                                    $k = 1;
                                @endphp
                                @foreach ($results??[] as $res)
                                    <tr class="border-top border-bottom">
                                        <td>{{ $k++ }}</td>
                                        <td>{{ $res->student->matric??'' }}</td>
                                        <td>{{ $res->subject->code??'' }}</td>
                                        <td>{{ $res->year->name??'' }}</td>
                                        <td>{{ $res->ca_score??'' }}</td>
                                        <td><span class="fa fa-close fa-2x text-danger"></span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
