@extends('student.layout')
@section('section')
<div class="py-3">

    @if($access)
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
                <form method="post" action="{{route('student.resit.registration')}}" class="" id="signed_courses_form">
                    @csrf
                    <input type="hidden" name="resit_id" value="{{$resit_id}}">
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
                    <div class="d-flex justify-content-center py-3 flex-wrap">
                        <button type="submit" class="btn btn-success btn-sm mr-4 text-capitalize"><i class="fa fa-save mx-1"></i>{{__('text.word_save')}}</button>
                        <div class="btn btn-primary btn-sm mr-4 text-capitalize">{{__('text.word_amount')}} : <span id="amount-sum"></span></div>
                    </div>

                </form>
                    <hr>
                    <div id="saved_actions" class="d-flex justify-content-center py-3 flex-wrap">
                        @if(\App\Helpers\Helpers::instance()->payChannel() != null)
                            @if($unpaid??0 > 0)
                                <form method="post" action="{{ route('student.resit.registration.payment') }}">
                                    @csrf
                                    <input type="hidden" name="resit_id" value="{{ $resit_id }}">
                                    <button type="submit" class="btn btn-success btn-sm mr-4 text-capitalize"><i class="fa fa-money mx-1"></i>{{__('text.make_payment')}}</button>
                                </form>
                            @endif
                        @endif
                        <a href="{{route('student.resit.download_courses', [$resit_id])}}" class="btn btn-sm btn-primary">{{__('text.download_courses')}}</a>
                    </div>
            </div>
        </div>
    @else
    <div class="py-4 text-center h4 text-danger bg-light my-4">
        {{__('text.course_registration_over')}}
    </div>
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
            data: {'resit_id': '{{$resit_id}}'},
            success: function(data){
                if (data != null) {
                    registered_courses = data.courses;
                    console.log(data);
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
                                <td class="border-left border-right"><span class="btn btn-sm btn-danger" onclick='drop(`+registered_courses[key]['id']+`)'>{{__('text.word_drop')}}</span></td>
                            </tr>`
                    }
                    $('#course_table').html(html2);
                    $('#cv-sum').text(cv_sum);
                    $('#amount-sum').text(data.courses.length * parseInt('{{$unit_cost}}'));
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
                class_courses = data.data;
                let html = ``;
                for (const key in data.data) {
                    if(registered_courses.filter(e => e['id'] == data.data[key]['id']).length > 0){continue;}
                    html += `<tr class="border-bottom" id="modal-`+data.data[key]['id']+`">
                            <td class="border-left border-right">`+data.data[key]['code']+`</td>
                            <td class="border-left border-right">`+data.data[key]['name']+`</td>
                            <td class="border-left border-right d-none d-md-table-cell">`+data.data[key]['cv']+`</td>
                            <td class="border-left border-right d-none d-md-table-cell">`+data.data[key]['status']+`</td>
                            <td class="border-left border-right"><span class="btn btn-sm btn-primary" onclick='add(`+data.data[key]['id']+`)'>{{__('text.word_sign')}}</span></td>
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
                            <td class="border-left border-right"><span class="btn btn-sm btn-primary" onclick='add(`+class_courses[key]['id']+`)'>{{__('text.word_sign')}}</span></td>
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
                            <td class="border-left border-right"><span class="btn btn-sm btn-danger" onclick='drop(`+registered_courses[key]['id']+`)'>{{__('text.word_drop')}}</span></td>
                        </tr>`;
                }
                $('#amount-sum').html(course_cost)
                $('#course_table').html(html2);
                $('#cv-sum').text(cv_sum);

    }
    
    function add(course_id) {
        course = Object.values(class_courses).filter(crs=>crs['id']==course_id)[0]
        if((cv_sum + parseInt(course['cv'])) > parseInt("{{$cv_total}}")){
            alert("Can't add this course. Maximum credits can not be exceeded");
            return;
        }
        course_cost += parseInt("{{$unit_cost}}");
        cv_sum += parseInt(course['cv']);
        registered_courses.push(course);

        $('#saved_actions').removeClass('d-flex');
        $('#saved_actions').addClass('hidden');
        refresh();
    }

    function drop(course_id) {
        course = registered_courses.filter(crs=>crs['id']==course_id)[0];
        course_cost -= parseInt("{{$unit_cost}}");
        cv_sum -= course['cv'];
        if(cv_sum <= 0){cv_sum = 0;}
        registered_courses = registered_courses.filter(e => e['id'] !== course['id']);

        $('#saved_actions').removeClass('d-flex');
        $('#saved_actions').addClass('hidden');
        refresh();
    }
</script>
@endsection
