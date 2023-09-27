@extends('teacher.layout')
@section('section')

<div class="py-4">
    <div class="row py-4">
        <div class="col-sm-12 col-md-6 px-2">
            @csrf
            <input class="form-control input-lg rounded" name="matric" onchange="record_attendance(event)">
        </div>
        <div class="col-sm-12 col-md-6 px-2">
            <table class="table table-primary">
                <thead>
                    <th class="border-left border-right">{{ __('text.word_matricule') }}</th>
                    <th class="border-left border-right">{{ __('text.word_name') }}</th>
                </thead>
                <tbody id="recorded_students">

                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
@section('script')
    <script>
        function record_attendance(event){
            let matric = event.target.value;
            url = "{{ route('user.course.attendance.record', $attendance_id) }}";
            // console.log(matric);
            $.ajax({
                method: "POST",
                url: url,
                data: {'matric': matric, 'course_id': "{{ $course->id }}"},
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data){
                    console.log(data);
                    if(data.students.length > 0){
                        let html = ``
                        data.students.forEach(element => {
                            html += `<tr>
                                    <td class="border-left border-right">`+element.matric+`</td>
                                    <td class="border-left border-right">`+element.name+`</td>
                                </tr>`;
                        });

                        $('#recorded_students').html(html);
                    }
                },
                error:function(error){
                    console.log(error);
                }
            })
        }
    </script>
@endsection