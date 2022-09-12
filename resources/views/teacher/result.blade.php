@extends('teacher.layout')

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
        $seqs = \App\Models\Sequence::all();
        $year = \App\Helpers\Helpers::instance()->getCurrentAccademicYear();
    @endphp

    @csrf
    <div class="card">
       <div class="d-flex justify-content-between">
           <div class="card-header d-flex justify-content-between align-items-center w-100">
               <h3 class=" font-weight-bold text-uppercase py-4 flex-grow-1">
                   Student Result ({{$subject->subject->name}})
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
                    <thead>
                    <tr>
                        <th style="width: 50px" class="text-center" colspan="3">Sequences</th>
                        @foreach($seqs as $seq)
                            <th class="text-center">{{$seq->name}}</th>
                        @endforeach
                    </tr>
                    <tr>
                        <th>#</th>
                        <th style="width: 200px;">Name</th>
                        <th style="width: 100px;">Matricule</th>
                        <th class="text-center" colspan="{{$seqs->count()}}">Score</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($subject->class->students($year)->get() as $student)
                        <tr data-role="student">
                            <td>1</td>
                            <td class="name" style="width: 200px; text-align: left">{{$student->name}}</td>
                            <td class="matric" style="width: 100px; text-align: left">{{$student->matric}}</td>
                            @foreach($seqs as $seq)
                                <td class="pt-3-half">
                                   @if(\App\Models\Config::where(['seq_id'=> $seq->id,'year_id'=>$year])->whereDate('start_date','<=', \Carbon\Carbon::now())->whereDate('end_date','>=', \Carbon\Carbon::now())->first() || \Auth::user()->isMaster($year, $subject->class_id))
                                        <input class="score form-control bg-white border-0" data-sequence="{{$seq->id}}" type='number' data-student="{{$student->id}}" value="{{\App\Helpers\Helpers::instance()->getScore($seq->id, $subject->subject_id, $subject->class_id,$year, $student->id)}}">
                                    @else
                                        <input class="score form-control bg-white border-0" readonly type='number'  value="{{\App\Helpers\Helpers::instance()->getScore($seq->id, $subject->subject_id, $subject->class_id,$year, $student->id)}}">
                                    @endif
                                </td>
                            @endforeach
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
            if(event.target.value < 10){
                event.target.style.color = 'red';
            }
            else{
                event.target.style.color = 'black';
            }

            let subject_url = "{{route('user.store_result',$subject->subject_id)}}";
            // $(".pre-loader").css("display", "block");

            if( $(this).val() > 20){

            }else{
                $.ajax({
                    type: "POST",
                    url: subject_url,
                    data : {
                        "student" : $(this).attr('data-student'),
                        "sequence" :$(this).attr('data-sequence'),
                        "subject" : '{{$subject->subject_id}}',
                        "year" :'{{$year}}',
                        "class_id" :'{{$subject->class_id}}',
                        "class_subject_id" : '{{$subject->id}}',
                        "coef" : {{$subject->subject->coef}},
                        "score" : $(this).val(),
                        '_token': '{{ csrf_token() }}'
                    },
                    success: function (data) {
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
