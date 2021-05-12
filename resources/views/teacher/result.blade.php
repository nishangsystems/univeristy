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
        <h3 class="card-header text-center font-weight-bold text-uppercase py-4">
            Student Result ({{$subject->subject->name}})
        </h3>
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
                        <tr>
                            <td>1</td>
                            <td style="width: 200px; text-align: left">{{$student->name}}</td>
                            <td style="width: 100px; text-align: left">{{$student->matric}}</td>
                            @foreach($seqs as $seq)
                                <td class="pt-3-half">
                                   <input class="score" data-sequence="{{$seq->id}}" data-student="{{$student->id}}" value="{{\App\Helpers\Helpers::instance()->getScore($seq->id, $subject->subject_id, $subject->class_id,$year, $student->id)}}">
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
            let subject_url = "{{route('user.store_result',$subject->subject_id)}}";
            // $(".pre-loader").css("display", "block");
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
        })
    </script>
@endsection
