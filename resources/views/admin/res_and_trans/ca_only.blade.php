@extends('admin.printable')
@section('section')

    @php
        $year = request('year_id');
        $students = \App\Models\ProgramLevel::find(request('class_id'))->_students($year)->get();
        $grades = \App\Models\ProgramLevel::find(request('class_id'))->program->gradingType->grading->sortBy('grade') ?? [];
        $courses = \App\Models\ProgramLevel::find(request('class_id'))->class_subjects_by_semester(request('semester_id')) ?? [];
        $base_pass = (\App\Models\ProgramLevel::find(request('class_id'))->program->ca_total ?? 0 + \App\Models\ProgramLevel::find(request('class_id'))->program->exam_total ?? 0)*0.5;
        // dd($grades);
        $k = 1;
    @endphp
    <div class="my-2">
        {{-- <img src="{{\App\Helpers\Helpers::instance()->getHeader()}}" alt="" class="w-100"> --}}
        <div class="text-center py-2">
            <h4 class="text-decoration text-capitalize"><b>
                {{\App\Models\ProgramLevel::find(request('class_id'))->name().' '.\App\Models\Semester::find(request('semester_id'))->name.' '.$title.' FOR '.\App\Models\Batch::find(request('year_id'))->name.' '.__('text.academic_year')}}
            </b></h4>
            <div class="d-flex overflow-auto"></div>
            <table>
                <thead class="text-capitalize">
                    <tr class="border-top border-bottom border-secondary">
                        <th class="border-left border-right border-secondary">#</th>
                        <th class="border-left border-right border-secondary">{{__('text.word_name')}}</th>
                        <th class="border-left border-right border-secondary">{{__('text.word_matricule')}}</th>
                        @foreach ($courses as $course)
                            <th class="border-left border-right border-secondary">{{$course->subject->code}}</th> 
                        @endforeach
                    </tr>
                    
                </thead>
                <tbody>
                    @foreach($students as $student)
                        <tr class="border-top border-bottom border-secondary">
                            <td class="border-left border-right border-secondary">{{$k++}}</td>
                            <td class="border-left border-right border-secondary">{{$student->name}}</td>
                            <td class="border-left border-right border-secondary">{{$student->matric}}</td>
                            @foreach($courses as $course)
                                <td class="border-left border-right border-secondary">{{$student->ca_score($course->subject->id, request('class_id'), $year, request('semester_id'))}}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
@section('script')
@endsection