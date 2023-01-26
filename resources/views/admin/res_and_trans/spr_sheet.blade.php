@extends('admin.layout')
@section('section')
@if(!request()->has('class_id'))
    <form method="post">
        @csrf
        <div class="row my-3 py-3 text-capitalize">
            <div class=" col-sm-6 col-md-5 col-lg-5 px-2">
                <label for="">{{__('text.word_class')}}</label>
                <div>
                    <select name="class_id" id="" class="form-control rounded" required>
                        <option value=""></option>
                        @foreach(\App\Http\Controllers\Controller::sorted_program_levels() as $pl)
                            <option value="{{$pl['id']}}">{{$pl['name']}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class=" col-sm-6 col-md-4 col-lg-4 px-2">
                <label for="">{{__('text.word_semester')}}</label>
                <div>
                    <select name="semester_id" id="" class="form-control rounded" required>
                        <option value=""></option>
                        @foreach(\App\Models\Semester::all() as $sem)
                            <option value="{{$sem->id}}">{{$sem->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class=" col-sm-6 col-md-3 col-lg-3 px-2">
                <label for="">{{__('text.word_year')}}</label>
                <div>
                    <select name="year_id" id="" class="form-control rounded" required>
                        <option value=""></option>
                        @foreach(\App\Models\Batch::all() as $year)
                            <option value="{{$year->id}}">{{$year->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="my-2 px-0 mx-0 d-flex justify-content-end"><input type="submit" class="btn btn-sm text-capitalize btn-primary rounded" value="{{__('text.build_spread_sheet')}}"></div>
    </form>
@else
    @php
        $year = request('year_id');
        $students = \App\Models\ProgramLevel::find(request('class_id'))->_students($year)->get();
        $grades = \App\Models\ProgramLevel::find(request('class_id'))->program->gradingType->grading->sortBy('grade') ?? [];
        $courses = \App\Models\ProgramLevel::find(request('class_id'))->class_subjects_by_semester(request('semester_id')) ?? [];
        $base_pass = (\App\Models\ProgramLevel::find(request('class_id'))->program->ca_total ?? 0 + \App\Models\ProgramLevel::find(request('class_id'))->program->exam_total ?? 0)*0.5;
        // dd($grades);
        $k = 1;
        // dd($students);
    @endphp
    <div class="my-2">
        <img src="{{\App\Helpers\Helpers::instance()->getHeader()}}" alt="" class="w-100">
        <div class="text-center py-2">
            <h4 class="text-decoration text-capitalize"><b>
                {{\App\Models\ProgramLevel::find(request('class_id'))->name().' '.\App\Models\Semester::find(request('semester_id'))->name.' '.$title.' FOR '.\App\Models\Batch::find(request('year_id'))->name.' '.__('text.academic_year')}}
            </b></h4>
            <div class="d-flex overflow-auto">
                <table>
                    <thead class="text-capitalize">
                        <tr class="border-top border-bottom border-secondary">
                            <th class="border-left border-right border-secondary" colspan="2"></th>
                            @foreach ($courses as $course)
                                <th class="border-left border-right border-secondary" colspan="4">{{$course->subject->code}}</th>
                            @endforeach
                        </tr>
                        <tr class="border-top border-bottom border-secondary">
                            <th class="border-left border-right border-secondary">#</th>
                            <th class="border-left border-right border-secondary">{{__('text.word_matricule')}}</th>
                            @foreach ($courses as $course)
                                <th class="border-left border-right border-secondary">{{__('text.CA')}}</th>
                                <th class="border-left border-right border-secondary">{{__('text.EX')}}</th>
                                <!-- <th class="border-left border-right border-secondary">{{__('text.word_exams')}}</th> -->
                                <th class="border-left border-right border-secondary">{{__('text.TT')}}</th>
                                <!-- <th class="border-left border-right border-secondary">{{__('text.word_total')}}</th> -->
                                <th class="border-left border-right border-secondary">{{__('text.GR')}}</th>
                                <!-- <th class="border-left border-right border-secondary">{{__('text.word_grade')}}</th> -->
                            @endforeach
    
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @foreach($students as $student)
                            <tr class="border-top border-bottom border-secondary">
                                <td class="border-left border-right border-secondary">{{$k++}}</td>
                                <td class="border-left border-right border-secondary">{{$student->matric}}</td>
                                @foreach ($courses as $course)
                                    <td class="border-left border-secondary">{{$student->offline_ca_score($course->subject->id, request('class_id'), $year, request('semester_id'))}}</td>
                                    <td class="border-left border-right border-info">{{$student->offline_exam_score($course->subject->id, request('class_id'), $year, request('semester_id'))}}</td>
                                    <td class="border-left border-right border-info">{{$student->offline_total_score($course->subject->id, request('class_id'), $year, request('semester_id'))}}</td>
                                    <th class="border-right border-secondary {{$student->total_score($course->subject->id, request('class_id'), $year, request('semester_id')) >= 50 ? 'text-success':'text-danger'}}">{{$student->offline_grade($course->subject->id, request('class_id'), $year, request('semester_id'))}}</th>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif
@endsection
@section('script')
@endsection