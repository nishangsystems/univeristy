@extends('teacher.layout')
@section('section')

<div class="py-4">
    <div class="row py-4">
        <div class="col-sm-12 col-md-6 px-2">
            @csrf
            <input class="form-control input-lg rounded" name="matric" onchange="record_attendance(event)" autofocus>
        </div>
        <div class="col-sm-12 col-md-6 px-2">
            <div class="d-flex justify-content-center text-dark text-capitalize" style="font-size: large; font-weight:bolder;">{{ __('text.word_total') }} : <span id="total_attendance">{{ count($students) }}</span></div>
            <table class="table table-primary">
                <thead>
                    <th class="border-left border-right">{{ __('text.word_matricule') }}</th>
                    <th class="border-left border-right">{{ __('text.word_name') }}</th>
                    <th class="border-left border-right"></th>
                </thead>
                <tbody id="recorded_students">
                    @foreach($students as $student)
                        <tr>
                            <td class="border-left border-right">{{ $student->matric }}</td>
                            <td class="border-left border-right">{{  $student->name }}</td>
                            <td class="border-left border-right"><button class="btn btn-sm btn-danger" onclick="drop_attendance({{  $student->id }})">{{ __('text.word_drop') }}</button></td>
                        </tr>
                    @endforeach
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
                                    <td class="border-left border-right"><button class="btn btn-sm btn-danger" onclick="drop_attendance(`+element.id+`)">{{ __('text.word_drop') }}</button></td>
                                </tr>`;
                        });

                        $('#recorded_students').html(html);
                        $('#total_attendance').text(data.students.length);
                    }
                },
                error:function(error){
                    console.log(error);
                }
            });
        }

        function drop_attendance(att_id){
            url = "{{ route('user.course.attendance.drop', '__AID__') }}".replace('__AID__', att_id);
            $.ajax({
                method: "GET",
                url: url,
                success: function(data){
                    console.log(data);
                    if(data.students.length > 0){
                        let html = ``
                        data.students.forEach(element => {
                            html += `<tr>
                                    <td class="border-left border-right">`+element.matric+`</td>
                                    <td class="border-left border-right">`+element.name+`</td>
                                    <td class="border-left border-right"><button class="btn btn-sm btn-danger" onclick="drop_attendance(`+element.id+`)">{{ __('text.word_drop') }}</button></td>
                                </tr>`;
                        });

                        $('#recorded_students').html(html);
                        $('#total_attendance').text(data.students.length);
                    }
                }
            })
        }
    </script>
@endsection