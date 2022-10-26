@extends('student.layout')
@section('section')
<div class="py-3">
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
                    <div class="btn btn-primary btn-sm mr-4 text-capitalize">{{__('text.credit_value')}} : <span id="cv-sum"></span>/<span id="cv-total"></span></button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
    let registered_courses = [];
    let class_courses = [];

    $(document).ready(function(){
        getCourses();
        loadCourses();
    })

    function loadCourses() {
        url = "{{route('student.registered_courses')}}";
        $.ajax({
            method: 'GET',
            url: url,
            success: function(data){
                console.log(data);
                if (data != null) {
                    registered_courses = data.courses;
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
                let html = ``;
                class_courses = data;
                console.log(data);
                for (const key in data) {
                    if(registered_courses.includes(data[key])){continue;}
                    html += `<tr class="border-bottom" id="modal-`+data[key]['id']+`">
                            <td class="border-left border-right">`+data[key]['code']+`</td>
                            <td class="border-left border-right">`+data[key]['name']+`</td>
                            <td class="border-left border-right">`+data[key]['coef']+`</td>
                            <td class="border-left border-right">`+data[key]['status']+`</td>
                            <td class="border-left border-right"><button class="btn btn-sm btn-primary" onclick="add(`+data[key]+`)">{{__('text.word_add')}}</button></td>
                        </tr>`
                }
                $('#modal_table').html(html);
            }
        })
    }

    function refresh(){
        let html = ``;
                class_courses;
                for (const key in class_courses) {
                    if(registered_courses.includes(class_courses[key])){continue;}
                    html += `<tr class="border-bottom `+class_courses[key]['id']+`">
                            <td class="border-left border-right">`+class_courses[key]['code']+`</td>
                            <td class="border-left border-right">`+class_courses[key]['name']+`</td>
                            <td class="border-left border-right">`+class_courses[key]['coef']+`</td>
                            <td class="border-left border-right">`+class_courses[key]['status']+`</td>
                            <td class="border-left border-right"><button class="btn btn-sm btn-primary" onclick="add(`+class_courses[key]+`)">{{__('text.word_add')}}</button></td>
                        </tr>`
                }
                $('#modal_table').html(html);
    }

    function findCourseById(key, array){
        for(const key in array){
            if (array[key]['id'] == key) {
                return array[key];
            }
        }
        return null;
    }
    
    function add(course) {

        _course = course;
        console.log(course);
        html = `<tr class="border-bottom `+_course['code']+`">
                            <input type="hidden" name="courses[]" value="`+_course['id']+`">
                            <td class="border-left border-right">`+_course['code']+`</td>
                            <td class="border-left border-right">`+_course['name']+`</td>
                            <td class="border-left border-right">`+_course['coef']+`</td>
                            <td class="border-left border-right">`+_course['status']+`</td>
                            <td class="border-left border-right"><button type="button" class="btn btn-sm btn-danger" onclick="drop(`+_course['id']+`)">{{__('text.word_drop')}}</button></td>
                        </tr>`
        
        // add row to table
        $('#course_table').append(html);
        // drop row from modal
        refresh();
    }

    function drop(course) {
        index = registered_courses.findIndex(course);
        registered_courses.splice(index, 1);
        // document.querySelector('#course_table .'+registered_courses[]).remove;
        refresh();
    }
</script>
@endsection