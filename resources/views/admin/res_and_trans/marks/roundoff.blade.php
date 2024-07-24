@extends('admin.layout')
@section('script')
    <script>


        let presubmit_submit = function(element){
            let year = $('#year_id_field').val();
            let semester = $('#semester_id_field').val();
            let class_id = $('#background_id_field').val();
            let url = "{{ route('admin.res_and_trans.exam.roundoff', ['year_id'=>'_YR_', 'semester_id'=>'_SMST_', 'background_id'=>'_CLSID_']) }}".replace('_YR_', year).replace('_SMST_', semester).replace('_CLSID_', class_id);
            window.location = url;
        }

        /*
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
        */
        
        let loadSemesters = function(bg_element){
            let bg_id = $(bg_element).val();
            let _url = "{{ route('semesters', ['background'=>'__BID__']) }}".replace('__BID__', bg_id);
            $.ajax({
                method: 'GET', url: _url, 
                success: function(data){
                    console.log(data);
                    let html = `<option value="">{{ __('text.word_semester') }}</option>`;
                    data.forEach(element=>{
                        html += `<option value="${element.id}">${element.name}</option>`;
                    });
                    $('#semester_id_field').html(html);
                }
            });
        }
    </script>
@endsection
@section('section')
    <div class="py-4">
        <div class="my-4 container-md-fluid mx-auto row">
            
            <div class="col-md-6 col-xl-2 my-2">
                <select class="form-control" id="year_id_field" name="year_id">
                    <option></option>
                    @foreach($years as $year)
                        <option value="{{ $year->id }}" {{ $year_id == $year->id ? 'selected' : '' }}>{{ $year->name }}</option>
                    @endforeach
                </select>
                <span class="text-secondary text-capitalize">@lang('text.academic_year')<i class="text-danger">*</i></span>
            </div>
            <div class="col-md-6 col-xl-4 my-2">
                <select class="form-control" id="background_id_field" name="background_id" onchange="loadSemesters(this)">
                    <option value="">@lang('text.select_background')</option>
                    @foreach ($backgrounds as $bg)
                        <option value="{{ $bg->id }}" {{ $bg->id == $background_id ? 'selected' : '' }}>{{ $bg->background_name }}</option>
                    @endforeach
                </select>
                <span class="text-secondary text-capitalize">@lang('text.word_background')<i class="text-danger">*</i></span>
            </div>
            <div class="col-md-6 col-xl-4 my-2">
                <select class="form-control" id="semester_id_field" name="semester_id">
                    <option>@lang('text.word_semester')</option>
                    @isset ($semester)
                        <option value="{{ $semester->id??'' }}" selected>{{ $semester->name??null }}</option>
                    @endif
                </select>
                <span class="text-secondary text-capitalize">@lang('text.word_semester')<i class="text-danger">*</i></span>
            </div>
            {{-- <div class="col-md-6 col-xl-2 my-2">
                <input type="text" class="form-control" autocomplete="off" id="course_field" oninput="search_course(this)" value="{{ !isset($course)  ? null : "[{$course->code}] {$course->name}" }}">
                <span class="text-capitalize text-secondary">@lang('text.word_course')<i class="text-danger">*</i></span>
                <input name="course_id" type="hidden" id="course_id_field" value="{{ $course_id }}">
                <div class="container-fluid" id="course_display_panel"></div>
            </div> --}}
            
            <div class="col-xl-2 my-2">
                <button class="btn btn-primary rounded btn-sm" onclick="presubmit_submit()">@lang('text.word_next')</button>
            </div>
        </div>

        @if($semester_id != null)
            <div class="card my-5 shadow border-left-0 border-right-0 border-top border-bottom border-dark">
                <div class="text-center text-uppercase py-4 alert-danger border-bottom border-danger text-uppercase"><b>{{ $title }}</b></div>
                <div class="card-body py-5 my-2">
                    <form class="row my-2" method="post">
                        @csrf
                        <div class="col-md-3">
                            <input type="number" class="form-control" name="lower_limit" id="lower_limit" required>
                            <span class="text-capitalize text-secondary">@lang('text.lower_limit')<i class="text-danger">*</i></span>
                        </div>
                        <div class="col-md-3">
                            <input type="number" class="form-control" name="upper_limit" id="upper_limit" required>
                            <span class="text-capitalize text-secondary">@lang('text.upper_limit')<i class="text-danger">*</i></span>
                        </div>
                        <div class="col-md-3">
                            <input type="number" class="form-control" name="mark" id="mark" required>
                            <span class="text-capitalize text-secondary">@lang('text.additional_mark')<i class="text-danger">*</i></span>
                        </div>
                        <div class="col-md-3">
                            <button id="save_course_button" class="btn btn-sm btn-primary rounded" type="submit">@lang('text.roundoff_mark')</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
@endsection
