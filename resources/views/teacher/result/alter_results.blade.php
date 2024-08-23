@extends('teacher.layout')
@section('section')
    <div class="py-4">
        
        <div class="my-4 container-lg-fluid mx-auto row">
            <div class="col-lg-5 my-2">
                <input class="form-control" id="student_search" type="text" value="{{ ($student??null) == null ? '' : ($student->matric??'').' : '. ($student->name??'') }}" oninput="search_student(this)">
                <span class="text-secondary text-capitalize">{{ trans_choice('text.word_student', 1) }}</span>
                <input name="student_id" type="hidden" id="student_id_field" value="{{ $student_id??'' }}" onchange="load_semesters(this)">
                <div class="container-fluid" id="student_display_panel"></div>
            </div>
            <div class="col-lg-2 my-2">
                <select class="form-control" id="year_id_field" name="year_id">
                    <option></option>
                    @foreach($years as $year)
                        <option value="{{ $year->id }}" {{ $year_id == $year->id ? 'selected' : '' }}>{{ $year->name }}</option>
                    @endforeach
                </select>
                <span class="text-secondary text-capitalize">@lang('text.academic_year')</span>
            </div>
            <div class="col-lg-3 my-2">
                <select class="form-control" id="semester_id_field" name="semester_id">
                    <option value="{{ $semester_id??'' }}" selected>{{ $semester->name??null }}</option>
                </select>
                <span class="text-secondary text-capitalize">@lang('text.word_semester')</span>
            </div>
            <div class="col-lg-2 my-2">
                <button class="btn btn-primary rounded btn-sm" onclick="presubmit_submit()">@lang('text.word_next')</button>
            </div>
        </div>

        @if($semester_id != null)
            <div class="card my-5 shadow border-left-0 border-right-0 border-top border-bottom border-dark">
                <div class="text-center text-uppercase py-4 alert-danger border-bottom border-danger"><b>@lang('text.add_exam_course')</b></div>
                <div class="card-body py-5 my-2">
                    <form class="row my-2" method="post">
                        @csrf
                        <div class="col-lg-5">
                            <input type="text" class="form-control" autocomplete="off" id="course_field" oninput="search_course(this)">
                            <span class="text-capitalize text-secondary">@lang('text.word_course')</span>
                            <input name="course_id" type="hidden" id="course_id_field">
                            <div class="container-fluid" id="course_display_panel"></div>
                        </div>
                        <div class="col-lg-2">
                            <input type="text" class="form-control" name="ca_score" id="ca_score">
                            <span class="text-capitalize text-secondary">@lang('text.ca_mark')</span>
                        </div>
                        <div class="col-lg-3">
                            <input type="text" class="form-control" name="exam_score" id="exam_score">
                            <span class="text-capitalize text-secondary">@lang('text.exam_mark')</span>
                        </div>
                        <div class="col-lg-2">
                            <button id="save_course_button" class="btn btn-sm btn-primary rounded" type="submit">@lang('text.add_course')</button>
                        </div>

                    </form>
                </div>
            </div>
        @endif
    </div>
@endsection
@section('script')
    <script>

        let presubmit_submit = function(element){
            let year = $('#year_id_field').val();
            let semester = $('#semester_id_field').val();
            let sid = $('#student_id_field').val();
            let url = "{{ route('user.results.alter_results', ['year_id'=>'_YR_', 'semester_id'=>'_SMST_', 'student_id'=>'_SID_']) }}".replace('_YR_', year).replace('_SMST_', semester).replace('_SID_', `${sid}`);
            window.location = url;
        }

        let search_student = function(element){
            let value = $(element).val();
            let _url = "{{ route('search-all-students', ['name'=>'__NME__']) }}".replace('__NME__', value);
            $.ajax({
                method : 'GET', url : _url, 
                success : function(data){
                    // console.log(data);
                    let html = `<div class="my-2 px-2 py-3 position-absolute border-left border-right bg-white" style="max-height: 40rem; overflow-y: scroll; z-index: 20;">`;
                    data.forEach(element => {
                        html += `<div class="alert-success p-2  border-top border-bottom my-1" onclick="pickStudent('${element.id}', '${element.matric}', '${element.name}')">
                                <b class="text-danger">${element.matric}</b> || <small class="text-uppercase text-secondary">${element.name}</small>
                            </div>`;
                    });
                    html += `</div>`;
                    $('#student_display_panel').html(html);
                }
            });
        }

        
        let pickStudent = function(sid, matric, name){
            $("#student_search").val(`[ ${matric} ] ${name}`);
            $('#student_id_field').val(sid);
            $('#student_display_panel').html(null);
            load_semesters(sid);
        }

        let load_semesters = function(sid){
            let student_id = sid;
            let _url = "{{ route('student_semesters', ['student_id'=>'__SID__']) }}".replace('__SID__', student_id);
            $.ajax({
                method: 'get', url: _url, success: function(data){
                    // console.log(data);
                    let html = '';
                    data.forEach(element=>{
                        html += `<option value="${element.id}">${element.name}</option>`
                    });
                    $('#semester_id_field').html(html);
                }
            });
        }

        let search_course = function(element){
            let value = $(element).val();
            let _url = "{{ route('search_subjects') }}?name="+value;
            $.ajax({
                method : 'GET', url : _url, 
                success : function(data){
                    let html = `<div class="my-2 px-2 py-3 position-absolute border-left border-right bg-white" style="max-height: 40rem; overflow-y: scroll; z-index: 20;">`;
                    // console.log(data.data);
                    data.data.forEach(element => {
                        html += `<div class="alert-success p-2  border-top border-bottom my-1" onclick="pickCourse('${element.id}', '${element.code}', '${element.name}')">
                                <b class="text-danger">${element.code}</b> || <small class="text-uppercase text-secondary">${element.name}</small>
                            </div>`
                    });
                    html += `</div>`;
                    $('#course_display_panel').html(html);
                }
            })
        }

        let pickCourse = function(cid, ccode, ctitle){
            $('#course_field').val('[ '+ccode+' ] '+ctitle);
            $('#course_id_field').val(cid);
            $('#course_display_panel').html(null);

            // get the result record for the course
            get_result(cid);
        }

        let get_result = function(cid){
            let _url = "{{ route('user.results.get_record', ['student_id'=>$student_id, 'year_id'=>$year_id, 'semester_id'=>$semester_id, 'course_id'=>'__CID__']) }}".replace('__CID__', cid);
            $.ajax({
                method: 'get', url: _url, success: function(data){
                    console.log(data)
                    if(data.ca_score + data.exam_score > 0){
                        $('#save_course_button').addClass('hidden');
                    }
                    $('#ca_score').val(data.ca_score);
                    $('#exam_score').val(data.exam_score);
                }
            });
        }

    </script>
@endsection