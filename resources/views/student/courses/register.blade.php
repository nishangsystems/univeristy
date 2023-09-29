@extends('student.layout')
@section('section')
<div class="py-3">

    @if($access && ((isset($on_time) && $on_time) || !isset($on_time)))
        <div class="form-group">
            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modelId">
                <i class="fa fa-plus mx-1"></i>{{__('text.sign_course')}}
            </button>
            
            <!-- Modal -->
            <div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-primary d-flex justify-content-around">
                            <h5 class="modal-title text-uppercase" id="modal-title">{{__('text.course_bank')}}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="false">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group form-group-merged d-flex border">
                                <select name="level" class="form-control border-0" id="modal-level" required>
                                    <option value="">{{__('text.select_level')}}</option>
                                    @foreach(\App\Models\Level::all() as $level)
                                        <option value="{{$level->id}}">{{$level->level}}</option>
                                    @endforeach
                                </select>
                                <button class="btn btn-sm btn-light text-capitalize border-0" onclick="getCourses('#modal-level')">{{__('text.view_courses')}}</button>
                            </div>
                            <div id="modal_table"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="py-3">
                <form method="post">
                    @csrf
                    <table>
                        <thead class="text-capitalize bg-secondary text-white">
                            <th class="border-left border-right">{{__('text.course_code')}}</th>
                            <th class="border-left border-right">{{__('text.course_title')}}</th>
                            <th class="border-left border-right">{{__('text.credit_value')}}</th>
                            <th class="border-left border-right">{{__('text.word_status')}}</th>
                            <th class="border-left border-right">{{__('text.word_action')}}</th>
                        </thead>
                        <tbody id="course_table"></tbody>
                    </table>
                    <div class="d-flex py-3">
                        <button type="submit" class="btn btn-success btn-sm mr-4 text-capitalize"><i class="fa fa-save mx-1"></i>{{__('text.word_save')}}</button>
                        <div class="btn btn-primary btn-sm mr-4 text-capitalize">{{__('text.credit_value')}} : <span id="cv-sum"></span>/<span id="cv-total">{{$cv_total}}</span></button>
                    </div>
                </form>
            </div>
        </div>
    @else
        @if(isset($on_time) && !$on_time)
        <div class="py-4 text-center h4 text-danger bg-light my-4 text-capitalize">
            {{__('text.course_registration_closed')}}
        </div>
        @else
        <div class="py-4 text-center h4 text-danger bg-light my-4 text-capitalize">
            {{trans('text.fee_access_phrase', ['amount'=>$min_fee, 'action'=>'sign up courses'])}}
        </div>
        @endif
    @endif
</div>
@endsection
@section('script')
<script>
    let registered_courses = [];
    let class_courses = [];
    let cv_sum = 0;
    let cv_total = parseInt("{{$cv_total}}");

    $(document).ready(function(){
        if ("{{$access}}") {
            loadCourses();
            getCourses();
        }
    })

    function loadCourses() {
        url = "{{route('student.registered_courses')}}";
        $.ajax({
            method: 'GET',
            url: url,
            success: function(data){
                if (data != null) {
                    registered_courses = data.courses;
                    cv_sum = data.cv_sum;
                    if(cv_sum > cv_total){alert("Problem encountered. Maximum credits excceded");}
                    let html2 = ``;
                    for (const key in registered_courses) {
                        html2 += `<tr class="border-bottom" id="modal-`+registered_courses[key]['id']+`">
                                <input type="hidden" name="courses[]" value="`+registered_courses[key]['id']+`">
                                <td class="border-left border-right">`+registered_courses[key]['code']+`</td>
                                <td class="border-left border-right">`+registered_courses[key]['name']+`</td>
                                <td class="border-left border-right">`+registered_courses[key]['cv']+`</td>
                                <td class="border-left border-right">`+registered_courses[key]['status']+`</td>
                                <td class="border-left border-right"><span class="btn btn-sm btn-danger" onclick='drop(`+registered_courses[key]['id']+`)'>{{__('text.word_drop')}}</span></td>
                            </tr>`
                    }
                    $('#course_table').html(html2);
                    $('#cv-sum').text(cv_sum);
                }
            }
        })
    }
    
    function getCourses(div = null){
        if (div == null) {
            value = "{{$student_class->level()->first()->id}}";
        }
        else{
            value = $(div).val();
        }
        url = "{{route('student.class-subjects', '__C__')}}";
        url = url.replace('__C__', value);
        $.ajax({
            method:"GET",
            url: url,
            success: function(data){
                console.log(data);
                class_courses = data;
                let html = ``;
                for (const key in data) {
                    if(registered_courses.filter(e => e['id'] == class_courses[key]['id']).length > 0){continue;}
                    html += `<div class="border rounded bg-light py-3 px-2" id="modal-`+data[key]['id']+`">
                            <div class="row"><span class="col-4">{{__('text.course_code')}}</span>`+data[key]['code']+`</div>
                            <div class="row"><span class="col-4">{{__('text.course_title')}}</span>`+data[key]['name']+`</div>
                            <div class="row"><span class="col-4">{{__('text.credit_value')}}</span>`+data[key]['cv']+`</div>
                            <div class="row"><span class="col-4">{{__('text.word_status')}}</span>`+data[key]['status']+`</div>
                            <div class="d-flex justify-content-end"><span class="btn btn-sm btn-primary rounded" onclick='add(`+data[key]['id']+`)'>{{__('text.word_sign')}}</span></div>
                        </div>`
                }
                $('#modal_table').html(html);
            }
        })
    }

    function refresh(){
        let html = ``;
        for (const key in class_courses) {
                    if(registered_courses.filter(e => e['id'] == class_courses[key]['id']).length > 0){continue;}
                    html += `<div class="border rounded bg-light py-3 px-2" id="modal-`+class_courses[key]['id']+`">
                            <div class="row"><span class="col-4">{{__('text.course_code')}}</span>`+class_courses[key]['code']+`</div>
                            <div class="row"><span class="col-4">{{__('text.course_title')}}</span>`+class_courses[key]['name']+`</div>
                            <div class="row"><span class="col-4">{{__('text.credit_value')}}</span>`+class_courses[key]['cv']+`</div>
                            <div class="row"><span class="col-4">{{__('text.word_status')}}</span>`+class_courses[key]['status']+`</div>
                            <div class="d-flex justify-content-end"><span class="btn btn-sm btn-primary rounded" onclick='add(`+class_courses[key]['id']+`)'>{{__('text.word_sign')}}</span></div>
                        </div>`
                }
                $('#modal_table').html(html);

                let html2 = ``;
                for (const key in registered_courses) {
                    html2 += `<tr class="border-bottom" id="modal-`+registered_courses[key]['id']+`">
                            <input type="hidden" name="courses[]" value="`+registered_courses[key]['id']+`">
                            <td class="border-left border-right">`+registered_courses[key]['code']+`</td>
                            <td class="border-left border-right">`+registered_courses[key]['name']+`</td>
                            <td class="border-left border-right">`+registered_courses[key]['cv']+`</td>
                            <td class="border-left border-right">`+registered_courses[key]['status']+`</td>
                            <td class="border-left border-right"><span class="btn btn-sm btn-danger" onclick='drop(`+registered_courses[key]['id']+`)'>{{__('text.word_drop')}}</span></td>
                        </tr>`
                }
                $('#course_table').html(html2);
                $('#cv-sum').text(cv_sum);

    }
    
    function add(course_id) {
        console.log(Object.values(class_courses) );
        
        course = Object.values(class_courses).filter(crs=>crs['id']==course_id)[0]
        if((cv_sum + course['cv']) > parseInt("{{$cv_total}}")){
            alert("Can't add this course. Maximum credits can not be exceeded");
            return;
        }
        cv_sum += course['cv'];
        registered_courses.push(course);
        refresh();
    }

    function drop(course_id) {
        course = registered_courses.filter(crs=>crs['id']==course_id)[0];
        cv_sum -= course['cv'];
        if(cv_sum <= 0){cv_sum = 0;}
        registered_courses = registered_courses.filter(e => e['id'] !== course['id']);
        refresh();
    }
</script>
@endsection