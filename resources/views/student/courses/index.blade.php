@extends('student.layout')
@section('section')
<div class="py-3">
    @php
        $current_year = \App\Helpers\Helpers::instance()->getYear();
        $current_year_name = \App\Models\Batch::find(\App\Helpers\Helpers::instance()->getYear())->name;
        $current_semester = \App\Helpers\Helpers::instance()->getSemester(auth('student')->user()->_class($current_year)->id)->id;
        $current_semester_name = \App\Helpers\Helpers::instance()->getSemester(auth('student')->user()->_class($current_year)->id)->name;
    @endphp
    @if($access)
        <div class="form-group">
            
            <!-- Modal -->
            <div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-primary d-flex justify-content-around">
                            <h5 class="modal-title" id="modal-title">Modal title</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="false">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            
                            <table>
                                <thead class="text-capitalize bg-light text-primary">
                                    <th class="border-left border-right">{{__('text.course_code')}}</th>
                                    <th class="border-left border-right">{{__('text.course_title')}}</th>
                                    <th class="border-left border-right">{{__('text.credit_value')}}</th>
                                    <th class="border-left border-right">{{__('text.word_status')}}</th>
                                    <th class="border-left border-right">{{__('text.word_action')}}</th>
                                </thead>
                                <tbody id="modal_table"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="py-3">
                <div class="form-group form-group-merged d-flex border">
                        <select name="level" class="form-control border-0" id="modal-year" required>
                            <option value="">{{__('text.academic_year')}}</option>
                            @foreach(\App\Models\Batch::all() as $batch)
                                <option value="{{$batch->id}}" {{$batch->id == $current_year ? 'selected' : ''}}>{{$batch->name}}</option>
                            @endforeach
                        </select>
                        <select name="level" class="form-control border-0" id="modal-semester" required>
                            <option value="">{{__('text.word_semester')}}</option>
                            @foreach(\App\Models\ProgramLevel::find(auth('student')->user()->_class($current_year)->id)->program()->first()->background()->first()->semesters()->get() as $semester)
                                <option value="{{$semester->id}}" {{$semester->id == $current_semester ? 'selected': ''}}>{{$semester->name}}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-sm btn-info text-capitalize border-0" onclick="load('#modal-year', '#modal-semester')">{{__('text.view_courses')}}</button>
                    </div>
                    <table class="table">
                        <thead>

                            <th id="table-title" class="text-dark h4 text-center text-capitalize fw-bolder" colspan="6"></th>
                        </thead>
                        <thead class="text-capitalize bg-secondary text-white">
                            <th class="border-left border-right">{{__('text.sn')}}</th>
                            <th class="border-left border-right">{{__('text.course_code')}}</th>
                            <th class="border-left border-right">{{__('text.course_title')}}</th>
                            <th class="border-left border-right">{{__('text.credit_value')}}</th>
                            <th class="border-left border-right">{{__('text.word_status')}}</th>
                            <th></th>
                        </thead>
                        <tbody id="course_table"></tbody>
                    </table>

            </div>
        </div>
    @else
        <div class="py-4 text-center h4 text-danger bg-light my-4">
            {{trans('text.fee_access_phrase', ['amount'=>$min_fee, 'action'=>'see registered courses'])}}
        </div>
    @endif
</div>
@endsection
@section('script')
<script>
    let registered_courses = [];
    let cv_sum = 0;

    $(document).ready(function(){
        load('#modal-year', '#modal-semester')
    })
    
    function load(x = null, y = null) {
        if ('{{$access}}' == true) {
            loadCourses(x,y);
        }
        else{alert('Minimum Fee requirement not met.');}
    }

    function loadCourses(year_id = null, semester_id = null) {
        year = year_id == null ? "{{$current_year}}" : $(year_id).val();
        semester = semester_id == null ? "{{$current_semester}}" : $(semester_id).val();
        
        title = "{{trans('text.formb_title', ['year'=>'__Y__', 'semester'=>'__S__'])}}";
        title = title.replace('__Y__', "{{$current_year_name}}");
        title = title.replace('__S__', "{{$current_semester_name}}");
        $('#table-title').text(title);
        
        url = "{{route('student.registered_courses', ['__Y__', '__S__'])}}";
        url =  url.replace('__Y__', year);
        url =  url.replace('__S__', semester);

        $.ajax({
            method: 'GET',
            url: url,
            success: function(data){
                if (data != null) {
                    action = `<a href="{{route('student.courses.download', ['__YEAR', '__SEMESTER'])}}" class="text-uppercase text-decoration-none h6 px-3 py-1 btn btn-info">{{__('text.download_formb')}}</a>`;
                    action = action.replace('__YEAR', year);
                    action = action.replace('__SEMESTER', semester);
                    console.log(data);
                    registered_courses = data.courses;
                    cv_sum = data.cv_sum;
                    // if(cv_sum > cv_total){alert("Problem encountered. Maximum credits excceded");}
                    let html2 = ``;
                    cnt = 1;
                    for (const key in registered_courses) {
                        html2 += `<tr class="border-bottom" id="modal-`+registered_courses[key]['id']+`">
                                <td class="border-left border-right">`+ cnt++ +`</td>
                                <td class="border-left border-right">`+registered_courses[key]['code']+`</td>
                                <td class="border-left border-right">`+registered_courses[key]['name']+`</td>
                                <td class="border-left border-right">`+registered_courses[key]['cv']+`</td>
                                <td class="border-left border-right">`+registered_courses[key]['status']+`</td>
                                <td class="border-left border-right d-flex text-capitalize">
                                    <a href="{{url('student/courses/content')}}/`+registered_courses[key]['id']+`" class="btn btn-sm btn-primary">{{__('text.word_content')}}</a> | 
                                    <a href="{{url('student/note/index')}}/`+registered_courses[key]['id']+`" class="btn btn-sm btn-primary">{{__('text.word_notes')}}</a> | 
                                    <a href="{{url('student/assignment/index')}}/`+registered_courses[key]['id']+`" class="btn btn-sm btn-success">{{__('text.word_assignments')}}</a> | 
                                    <a href="{{url('student/notification/index')}}/`+registered_courses[key]['id']+`" class="btn btn-sm btn-info">{{__('text.word_notifications')}}</a>
                                </td>
                            </tr>`
                    }
                    $('#course_table').html(html2);

                }
            }
        })
    }
</script>
@endsection