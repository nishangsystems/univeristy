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

        let printer = function(){
            let printable = $('#printable');
            let doc_body = $(document.body).html();
            
            // swap page body
            $(document.body).html(printable);
            window.print();

            // restore document body
            $(document.body).html(doc_body);
        }
    </script>
@endsection
@section('section')
    <div class="py-2 container-fluid">
        <div class="row my-4 py-3 shadow">
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

        <div class="d-flex justify-content-end py-2">
            <button class="btn btn-primary rounded btn-sm text-capitalize" onclick="printer()">@lang('text.word_print')</button>
        </div>
        @if($semester_id != null)
            <div class="container-fluid shadow rounded py-3 mt-4" id="printable">
                <div class="py-2 my-3  text-center">
                    <b>{{ 'CA Marks For '.$course->code }}({{$course->name}})</b> &rangle; &rangle; <i class="text-primary text-capitalize">{{ $semester->name }} | {{ $year->name }}</i> <hr class="my-1">
                </div>
                <table class="table-stripped">
                    <thead class="text-capitalize border-top border-bottom">
                        <th class="border-left border-right border-light">@lang('text.sn')</th>
                        <th class="border-left border-right border-light">@lang('text.word_name')</th>
                        <th class="border-left border-right border-light">@lang('text.word_matricule')</th>
                        <th class="border-left border-right border-light">@lang('text.ca_mark')</th>
                        <th class="border-left border-right border-light">@lang('text.word_sign')</th>
                    </thead>
                    <tbody>
                        @php
                            $k = 1;
                        @endphp
                        @foreach ($results??[] as $res)
                            <tr class="border-top border-bottom">
                                <td>{{ $k++ }}</td>
                                <td>{{ $res->student->name??'' }}</td>
                                <td>{{ $res->student->matric??'' }}</td>
                                <td>{{ $res->ca_score??'' }}</td>
                                <td class="border-bottom border-dark"></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection