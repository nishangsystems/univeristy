@extends('student.layout')
@section('section')
<div class="py-3">

    @if($access && ((isset($on_time) && $on_time) || !isset($on_time)))
        <div class="form-group">

            <div class="rounded py-3 px-1 bg-light">
                <!-- <div class="py-2">
                    <button type="button" class="form-control btn btn-md btn-primary fw-bolder text-sm" onclick="$('#course_bank').toggleClass('hidden')">{{__('text.sign_resit_course')}}</button>
                </div> -->
                <div class="py-3 " id="course_bank">
                    <div class="form-group form-group-merged d-flex border">
                        <input type="text" name="level" class="form-control border-0" id="modal-level" placeholder="{{__('text.prase_course_search')}}" oninput="getCourses('#modal-level')">
                    </div>
                    <table>
                        <thead class="text-capitalize bg-light text-primary">
                            <th class="border-left border-right">{{__('text.course_code')}}</th>
                            <th class="border-left border-right">{{__('text.course_title')}}</th>
                            <th class="border-left border-right d-none d-md-table-cell">{{__('text.credit_value')}}</th>
                            <th class="border-left border-right d-none d-md-table-cell">{{__('text.word_status')}}</th>
                            <th class="border-left border-right">{{__('text.word_action')}}</th>
                        </thead>
                        <tbody id="modal_table"></tbody>
                    </table>
                </div>
            </div>
            
            <div class="py-3">
                <form method="post" action="{{route('student.resit.registration.payment')}}" class="" id="signed_courses_form">
                    @csrf
                    <table>
                        <thead class="text-capitalize bg-secondary text-white">
                            <th class="border-left border-right">{{__('text.course_code')}}</th>
                            <th class="border-left border-right">{{__('text.course_title')}}</th>
                            <th class="border-left border-right d-none d-md-table-cell">{{__('text.credit_value')}}</th>
                            <th class="border-left border-right d-none d-md-table-cell">{{__('text.word_status')}}</th>
                            <th class="border-left border-right">{{__('text.word_action')}}</th>
                        </thead>
                        <tbody id="course_table"></tbody>
                    </table>
                    <div class="d-flex py-3">
                        <button type="submit" class="btn btn-success btn-sm mr-4 text-capitalize"><i class="fa fa-save mx-1"></i>{{__('text.word_proceed')}}</button>
                        <div class="btn btn-primary btn-sm mr-4 text-capitalize">{{__('text.credit_value')}} : <span id="cv-sum"></span>/<span id="cv-total">{{$cv_total}}</span></div>
                        <div class="btn btn-primary btn-sm mr-4 text-capitalize">{{__('text.word_amount')}} : <span id="amount-sum"></span></div>
                    </div>
                </form>
            </div>
        </div>
    @else
        @if(isset($on_time) && !$on_time)
        <div class="py-4 text-center h4 text-danger bg-light my-4">
            {{__('text.course_registration_over')}}
        </div>
        @else
        <div class="py-4 text-center h4 text-danger bg-light my-4">
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
    let course_cost = 0;
    let cv_total = parseInt("{{$cv_total}}");

 
    $(document).ready(function(){
        if ("{{$access}}") {
            loadCourses();
        }
    })

    function loadCourses() {
        url = "{{route('student.resit.registered_courses')}}";
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
                                <td class="border-left border-right d-none d-md-table-cell">`+registered_courses[key]['cv']+`</td>
                                <td class="border-left border-right d-none d-md-table-cell">`+registered_courses[key]['status']+`</td>
                                <td class="border-left border-right"><span class="btn btn-sm btn-danger" onclick='drop(`+JSON.stringify(registered_courses[key])+`)'>{{__('text.word_drop')}}</span></td>
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
        // console.log('____');
        url = "{{route('student.search_course')}}";
        $.ajax({
            method:"GET",
            url: url,
            data: {'value' : value},
            success: function(data){
                // console.log(data);
                class_courses = data;
                let html = ``;
                for (const key in data) {
                    if(registered_courses.filter(e => e['id'] == class_courses[key]['id']).length > 0){continue;}
                    html += `<tr class="border-bottom" id="modal-`+data[key]['id']+`">
                            <td class="border-left border-right">`+data[key]['code']+`</td>
                            <td class="border-left border-right">`+data[key]['name']+`</td>
                            <td class="border-left border-right d-none d-md-table-cell">`+data[key]['cv']+`</td>
                            <td class="border-left border-right d-none d-md-table-cell">`+data[key]['status']+`</td>
                            <td class="border-left border-right"><span class="btn btn-sm btn-primary" onclick='add(`+JSON.stringify(data[key])+`)'>{{__('text.word_sign')}}</span></td>
                        </tr>`
                }
                $('#modal_table').html(html);
            }
        })
    }

    function refresh(){
        let html = ``;
        for (const key in class_courses) {
                    if(registered_courses.filter(e => e['id'] == class_courses[key]['id']).length > 0){continue;}
                    html += `<tr class="border-bottom" id="modal-`+class_courses[key]['id']+`">
                            <td class="border-left border-right">`+class_courses[key]['code']+`</td>
                            <td class="border-left border-right">`+class_courses[key]['name']+`</td>
                            <td class="border-left border-right d-none d-md-table-cell">`+class_courses[key]['cv']+`</td>
                            <td class="border-left border-right d-none d-md-table-cell">`+class_courses[key]['status']+`</td>
                            <td class="border-left border-right"><span class="btn btn-sm btn-primary" onclick='add(`+JSON.stringify(class_courses[key])+`)'>{{__('text.word_sign')}}</span></td>
                        </tr>`;
                }
                $('#modal_table').html(html);

                let html2 = ``;
                for ( key = registered_courses.length-1; key >= 0; key--) {
                    html2 += `<tr class="border-bottom" id="modal-`+registered_courses[key]['id']+`">
                            <input type="hidden" name="courses[]" value="`+registered_courses[key]['id']+`">
                            <td class="border-left border-right">`+registered_courses[key]['code']+`</td>
                            <td class="border-left border-right">`+registered_courses[key]['name']+`</td>
                            <td class="border-left border-right d-none d-md-table-cell">`+registered_courses[key]['cv']+`</td>
                            <td class="border-left border-right d-none d-md-table-cell">`+registered_courses[key]['status']+`</td>
                            <td class="border-left border-right"><span class="btn btn-sm btn-danger" onclick='drop(`+JSON.stringify(registered_courses[key])+`)'>{{__('text.word_drop')}}</span></td>
                        </tr>`;
                }
                $('#amount-sum').html(course_cost)
                $('#course_table').html(html2);
                $('#cv-sum').text(cv_sum);

    }
    
    function add(course) {
        if((cv_sum + parseInt(course['cv'])) > parseInt("{{$cv_total}}")){
            alert("Can't add this course. Maximum credits can not be exceeded");
            return;
        }
        course_cost += parseInt("{{$unit_cost}}");
        cv_sum += parseInt(course['cv']);
        registered_courses.push(course);
        refresh();
    }

    function drop(course) {
        course_cost -= parseInt("{{$unit_cost}}");
        cv_sum -= parseInt(course['cv']);
        if(cv_sum <= 0){cv_sum = 0;}
        registered_courses = registered_courses.filter(e => e['id'] !== course['id']);
        refresh();
    }
</script>
@endsection