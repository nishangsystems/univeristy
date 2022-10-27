@extends('student.layout')
@section('section')
<div class="py-3">

    @if($access)
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
                            <h5 class="modal-title" id="modal-title">Modal title</h5>
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
        <div class="py-4 text-center h4 text-danger bg-light my-4">
            {{trans('text.fee_access_phrase', ['amount'=>$min_fee, 'action'=>'sign up courses'])}}
        </div>
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
        url = "{{route('student.class-subjects', '__C__')}}";
        url = url.replace('__C__', value);
        $.ajax({
            method:"GET",
            url: url,
            success: function(data){
                class_courses = data;
                let html = ``;
                for (const key in data) {
                    if(registered_courses.filter(e => e['id'] == class_courses[key]['id']).length > 0){continue;}
                    html += `<tr class="border-bottom" id="modal-`+data[key]['id']+`">
                            <td class="border-left border-right">`+data[key]['code']+`</td>
                            <td class="border-left border-right">`+data[key]['name']+`</td>
                            <td class="border-left border-right">`+data[key]['cv']+`</td>
                            <td class="border-left border-right">`+data[key]['status']+`</td>
                            <td class="border-left border-right"><span class="btn btn-sm btn-primary" onclick='add(`+JSON.stringify(data[key])+`)'>{{__('text.word_add')}}</span></td>
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
                            <td class="border-left border-right">`+class_courses[key]['cv']+`</td>
                            <td class="border-left border-right">`+class_courses[key]['status']+`</td>
                            <td class="border-left border-right"><span class="btn btn-sm btn-primary" onclick='add(`+JSON.stringify(class_courses[key])+`)'>{{__('text.word_add')}}</span></td>
                        </tr>`
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
                            <td class="border-left border-right"><span class="btn btn-sm btn-danger" onclick='drop(`+JSON.stringify(registered_courses[key])+`)'>{{__('text.word_drop')}}</span></td>
                        </tr>`
                }
                $('#course_table').html(html2);
                $('#cv-sum').text(cv_sum);

    }
    
    function add(course) {
        if((cv_sum + parseInt(course['cv'])) > parseInt("{{$cv_total}}")){
            alert("Can't add this course. Maximum credits can not be exceeded");
            return;
        }
        cv_sum += parseInt(course['cv']);
        registered_courses.push(course);
        refresh();
    }

    function drop(course) {
        cv_sum -= parseInt(course['cv']);
        if(cv_sum <= 0){cv_sum = 0;}
        registered_courses = registered_courses.filter(e => e['id'] !== course['id']);
        refresh();
    }
</script>
@endsection