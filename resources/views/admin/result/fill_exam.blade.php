@extends('admin.layout')

@section('style')
   <style>
       input {
           border: none;
           background: transparent;
       }

       input:focus-visible {
           border: none;
           box-shadow: none;
       }
   </style>
@endsection

@section('section')
    @php
        $subject = \App\Models\Subjects::find(request('course_id'));
        $semester = \App\Helpers\Helpers::instance()->getSemester(request('class_id'));
        $year = \App\Helpers\Helpers::instance()->getCurrentAccademicYear();
        $class_id = request('class_id');
    @endphp

    @csrf
    <div class="card">
       <div class="d-flex justify-content-between">
           <div class="card-header d-flex justify-content-between align-items-center w-100">
               <h3 class=" font-weight-bold text-uppercase py-4 flex-grow-1">
                   Student Result ({{$subject->name}})
               </h3>

               <div class="input-group radius-5 overflow-hidden" data-placement="left" data-align="top"
                    data-autoclose="true">
                   <input id="searchbox" placeholder="Type to search" type="number" class="form-control bg-white border-success">
               </div>
           </div>
       </div>
        <div class="card-body">
            <div id="table table-responsive" class="table-editable">
                <table class="table table-bordered table-responsive-md table-striped text-center">
                    <thead class="text-capitalize">
                        <tr>
                            <th style="width: 50px" class="text-center" colspan="3">{{$semester->name}}</th>
                            <th class="text-center">{{__('text.CA')}}</th>
                            <th class="text-center">{{__('text.word_exams')}}</th>
                        </tr>
                        <tr>
                            <th>#</th>
                            <th style="width: 200px;">{{__('text.word_name')}}</th>
                            <th style="width: 100px;">{{__('text.word_matricule')}}</th>
                            <th class="text-center" colspan="2">{{__('text.word_score')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php($k = 1)
                        @foreach($subject->class_subject()->where('class_id', request('class_id'))->first()->class->_students($year)->get() as $student)
                            <tr data-role="student">
                                <td>{{$k++}}</td>
                                <td class="name" style="width: 200px; text-align: left">{{$student->name}}</td>
                                <td class="matric" style="width: 100px; text-align: left">{{$student->matric}}</td>
                                <td class="pt-3-half">
                                    @if($semester->ca_is_late() == false)
                                        <input class="score form-control bg-white border-0" data-score-type="ca" data-sequence="{{$semester->id}}" type='number' data-student="{{$student->id}}" ca-score="{{$student->offline_ca_score($subject->id, request('class_id'), $year)}}" exam-score="{{$student->exam_score($subject->id, request('class_id'), $year)}}" value="{{$student->ca_score($subject->id, request('class_id'), $year)}}">
                                    @else
                                        <input class="score form-control bg-white border-0" readonly type='number'  value="{{$student->offline_ca_score($subject->id, request('class_id'), $year)}}">
                                    @endif
                                </td>
                                <td class="pt-3-half">
                                    @if($semester->exam_is_late() == false)
                                        <input class="score form-control bg-white border-0" data-score-type="exam" data-sequence="{{$semester->id}}" type='number' data-student="{{$student->id}}"  ca-score="{{$student->offline_ca_score($subject->id, request('class_id'), $year)}}" exam-score="{{$student->exam_score($subject->id, request('class_id'), $year)}}" value="{{$student->exam_score($subject->id, request('class_id'), $year)}}">
                                    @else
                                        <input class="score form-control bg-white border-0" readonly type='number'  value="{{$student->offline_exam_score($subject->id, request('class_id'), $year)}}">
                                    @endif
                                </td>
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
        $('.score').on('change', function (){
            if(($(this).attr('data-score-type') == 'ca' && $(this).val() < parseFloat('{{$ca_total/2}}')) || ($(this).attr('data-score-type') == 'exam' && $(this).val() < parseFloat('{{$exam_total/2}}'))){
                event.target.style.color = 'red';
            }
            else{
                event.target.style.color = 'black';
            }

            let subject_url = "{{route('user.store_result',$subject->id)}}";
            // $(".pre-loader").css("display", "block");

            if( ($(this).attr('data-score-type') == 'ca' && $(this).val() > parseFloat('{{$ca_total}}')) || ($(this).attr('data-score-type') == 'exam' && $(this).val() > parseFloat('{{$exam_total}}'))){

            }else{
                $.ajax({
                    type: "POST",
                    url: subject_url,
                    data : {
                        "student" : $(this).attr('data-student'),
                        "semester_id" :$(this).attr('data-sequence'),
                        "subject" : '{{$subject->id}}',
                        "year" :'{{$year}}',
                        "class_id" :'{{$class_id}}',
                        "class_subject_id" : '{{$subject->_class_subject($class_id)->id}}',
                        "coef" : '{{$subject->coef}}',
                        "ca_score" : $(this).attr('data-score-type') == 'ca' ? $(this).val() : $(this).attr('ca-score'),
                        "exam_score" : $(this).attr('data-score-type') == 'exam' ? $(this).val() : $(this).attr('exam-score'),
                        '_token': '{{ csrf_token() }}'
                    },
                    success: function (data) {
                        console.log(data);
                        $(".pre-loader").css("display", "none");
                    }, error: function (e) {
                        $(".pre-loader").css("display", "none");
                    }
                });
            }

        })

        $("#searchbox").on("keyup", function() {
            console.log($(this).val());
            var value = $(this).val().toLowerCase();
            $('tr[data-role="student"]').filter(function() {
                $(this).toggle($(this).find('.name').text().toLowerCase().indexOf(value) > -1)
            });
        });
        $('.score').on('load', ColorValues(this));
        // $('.score').on('change', ColorValue(event));
        function ColorValue(evt){
            if(evt.target.value < 10){
                evt.target.style.color = 'red';
            }
            else{evt.target.style.color = 'black';}
        }
        function ColorValues(input){
            document.querySelectorAll('.score').forEach(function(elt, key, parent){
                if(elt.value < 10){
                    elt.style.color = 'red';
                }
                else{
                    elt.style.color = 'black';
                }
            })
        }
    </script>

@endsection
