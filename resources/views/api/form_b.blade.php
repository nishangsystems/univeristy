@extends('api.layout')
@section('section')
    <div class="py-3">
        @php
            $current_year = \App\Helpers\Helpers::instance()->getYear();
            $current_year_name = \App\Models\Batch::find(\App\Helpers\Helpers::instance()->getYear())->name;
            $current_semester = \App\Helpers\Helpers::instance()->getSemester($student->_class($current_year)->id)->id;
            $current_semester_name = \App\Helpers\Helpers::instance()->getSemester($student->_class($current_year)->id)->name;
        @endphp
        @if($access)
            <div class="form-group">
               <div class="py-3">
                    <table class="table">
                        <thead>

                        <th id="table-title" class="text-dark h4 text-center text-capitalize fw-bolder" colspan="5"></th>
                        </thead>
                        <thead class="text-capitalize bg-secondary text-white">
                        <th class="border-left border-right">{{__('text.sn')}}</th>
                        <th class="border-left border-right">{{__('text.course_code')}}</th>
                        <th class="border-left border-right">{{__('text.course_title')}}</th>
                        <th class="border-left border-right">{{__('text.credit_value')}}</th>
                        <th class="border-left border-right">{{__('text.word_status')}}</th>
                        </thead>
                        <tbody id="course_table"></tbody>
                    </table>
                    <div class="d-flex py-3 justify-content-between">
                        <div class="btn btn-primary btn-sm mr-4 text-capitalize">{{__('text.attempted_credit_value')}} : <span id="cv-sum"></span></div>
                        <span id="action"></span>
                    </div>
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
            function loadCourses(year_id = null, semester_id = null) {
                year = year_id == null ? "{{$current_year}}" : $(year_id).val();
                semester = semester_id == null ? "{{$current_semester}}" : $(semester_id).val();

                title = "{{trans('text.formb_title', ['year'=>'__Y__', 'semester'=>'__S__'])}}";
                title = title.replace('__Y__', "{{$current_year_name}}");
                title = title.replace('__S__', "{{$current_semester_name}}");
                $('#table-title').text(title);

                url = "{{ route('student.api.courses',['year'=>"__Y__", 'semester'=>'__S__','student_id'=>'__ST__']) }}";
                url =  url.replace('__Y__', year);
                url =  url.replace('__S__', semester);
                url =  url.replace('__ST__', '{{$student->id}}');

                $.ajax({
                    method: 'GET',
                    url: url,
                    success: function(data){
                        if (data != null) {
                            action = `<a href="{{route('student.courses.download', ['__YEAR', '__SEMESTER'])}}" class="text-uppercase text-decoration-none h6 px-3 py-1 btn btn-info">{{__('text.download_formb')}}</a>`;
                            action = action.replace('__YEAR', year);
                            action = action.replace('__SEMESTER', semester);

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
                            </tr>`
                            }
                            $('#course_table').html(html2);
                            $('#action').html(action);
                            $('#cv-sum').text(cv_sum);
                        }
                    }
                })
            }

            loadCourses();
        })


    </script>
@endsection