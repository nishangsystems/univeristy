@extends('admin.layout')
@section('section')
    <div class="row py-4">
        <div class="col-md-5 col-lg-5 py-2 px-5">
            <div class="form rounded py-5 px-3 bg-light">
                <label class="text-capitalize">{{__('text.word_matricule')}}</label>
                <input type="text" id="matricule_field" class="form-control input-lg my-3" oninput="getSubjects()">
            </div>
        </div>
        <div class="col-md-7 col-lg-7 py-2 px-2">
            <table class="table">
                <thead class="text-primary h4">
                    <th class="border-l border-r border-light">#</th>
                    <th class="border-l border-r border-light">{{__('text.course_title')}}</th>
                    <th class="border-l border-r border-light">{{__('text.word_code')}}</th>
                    <th class="border-l border-r border-light"></th>
                </thead>
                <tbody id="teacher_subjects"></tbody>
            </table>
        </div>
    </div>
@endsection
@section('script')
    <script>
        function getSubjects() {
            let matric = $('#matricule_field').val();
            if(matric != null){
                url = "{{route('admin.attendance.teacher.subjects', ['matric'=>'__M_TR_C__'])}}".replace('__M_TR_C__', matric);
                $.ajax({
                    method: 'get',
                    url: url,
                    success: function(data){
                        console.log(data);
                        if(data != null){
                            k = 1;
                            let html_content = '';
                            data.subjects.forEach(element => {
                                html_content += `
                                            <tr class="border-top border-bottom border-light">
                                                <td class="border-left border-right">${k++}</td>
                                                <td class="border-left border-right">${element.name}</td>
                                                <td class="border-left border-right">${element.code}</td>
                                                <td class="border-left border-right">
                                                    <a class="btn btn-sm btn-primary" href="{{route('admin.attendance.teacher.record', ['matric'=>'__M_TR_C__', 'subject_id'=>'__SBID__'])}}">{{__('text.take_attendance')}}</a>
                                                </td>
                                            </tr>
                                            `.replace('__M_TR_C__', matric).replace('__SBID__', element.id);
                            });
                            $('#teacher_subjects').html(html_content);
                        }
                    }
                })
            }
        }
    </script>
@endsection