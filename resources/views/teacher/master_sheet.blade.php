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
        
        $year = \App\Helpers\Helpers::instance()->getCurrentAccademicYear();
        $subjects = request('class_id') ? \App\Models\ClassSubject::where('class_id', request('class_id'))->get() : null;
        $students = request('class_id') ? \Illuminate\Support\Facades\DB::table('student_classes')->where('class_id', '=', request('class_id'))->join('students', 'students.id', '=', 'student_classes.student_id')->get() : null;
    @endphp


    <div class="w-100">
        @if(!request('class_id'))
        <div class="col-9 mx-auto border-0">
            <form action="{{\Request::url()}}" method="get" class="w-100" target="_blank">
                @csrf
                <div class="py-3">
                    <label class=" bg-transparent border-0 fs-3 rounded-0">select class</label>
                    <select name="class_id" class="form-control" required id="" onchange="load_master_sheet(event)">
                        <option value="" disabled selected>select class</option>
                        @forelse(Auth::user()->classR($year) as $class)
                        <option value="{{$class->id}}">{{\App\Http\Controllers\Admin\StudentController::baseClasses()[$class->id]}}</option>
                        @empty
                        <p>No classes set</p>
                        @endforelse
                    </select>
                </div>
                <div class="py-3">
                    <label class=" bg-transparent border-0 fs-3 rounded-0">select term</label>
                    <select name="term_id" class="form-control" required id="" onchange="load_master_sheet(event)">
                        <option value="" disabled selected>Select Term</option>
                        @foreach(\App\Models\Term::all() as $term)
                        <option value="{{$term->id}}">{{$term->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="d-flex justify-content-end py-3">
                    <input type="submit" class="btn btn-sm btn-primary" name="" value="next" id="">
                </div>

            </form>
        </div>
        @endif
        @if(request('class_id') != null)
        @php
            $seqs = \App\Models\Sequence::where('term_id', request('term_id'))->get();
            $counter = 1;
        @endphp
        <div class="w-100 py-3 d-flex justify-content-end"><button class="btn btn-light rounded px-3" onchange="print()">print</button></div>
        <!-- <div class="w-100 d-flex overflow-scroll w-100 h-100"> -->
        
            <table class="table table-responsive">
                <thead class="border">
                    <tr>
                        <th colspan="4" class="text-center">Subjects</th>
                            @foreach($subjects as $subject)
                                <th class="text-center text-uppercase border border-top-0 border-bottom-0" colspan="2">{{\App\Models\Subjects::find($subject->subject_id)->name}}</th>
                            @endforeach
                    </tr>
                    <tr>
                        <th colspan="4" class="text-center">Coefficients</th>
                        @foreach($subjects as $subject)
                            <th class="text-center text-uppercase border border-top-0 border-bottom-0" colspan="2">{{\App\Models\Subjects::find($subject->subject_id)->coef}}</th>
                        @endforeach
                    </tr>
                    <tr>
                        <th class="text-center" colspan="4">Sequence</th>
                        @foreach($subjects as $subject)
                        @foreach($seqs as $seq)
                        <th class="text-center border border-4 border-top-0 border-bottom-0">{{substr($seq->name, 0, 1)}}</th>
                        @endforeach
                        @endforeach
                    </tr>
                    <tr>
                        <th class="border text-center">S/N</th>
                        <th class="border text-center" colspan="2">Name</th>
                        <th class="border text-center" colspan="1">Matricule</th>
                        <th class="border text-center" colspan="{{2*count($subjects)}}">Score</th>
                    </tr>
                </thead>
                <tbody>
    
                    @foreach($students as $student)
                        <tr>
                            <td class="border">{{$counter++}}</td>
                            <td class="border" colspan="2">{{$student->name}}</td>
                            <td class="border" colspan="1">{{$student->matric}}</td>
                            @foreach($subjects as $subject)
                                @foreach($seqs as $sq)
                                <td class="border score">{{ \App\Models\Subjects::find($subject->subject_id)->coef*(\App\Models\Result::where('batch_id', $year)->where('student_id', $student->id)->where('class_id', request('class_id'))->where('sequence', $sq->id)->where('subject_id', \App\Models\Subjects::find($subject->subject_id)->id)->first()['score'] ?? null) ?? ''}}</td>
                                @endforeach
                            @endforeach
                        </tr>
                        
                    @endforeach
                </tbody>
           </table>
        <!-- </div> -->
       @endif
    </div>
@endsection
@section('script')
    <script>
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
                if(elt.textContent < 10){
                    elt.style.color = 'red';
                }
                else{
                    elt.style.color = 'black';
                }
            })
        }
    </script>

@endsection
