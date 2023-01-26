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
        <div class="my-2 px-0 mx-0 d-flex justify-content-end"><input type="submit" class="btn btn-sm text-capitalize btn-primary rounded" value="{{__('text.build_frequency_distribution')}}"></div>
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
    @endphp
    <div class="my-2">
        <img src="{{\App\Helpers\Helpers::instance()->getHeader()}}" alt="" class="w-100">
        <div class="text-center py-2">
            <h4 class="text-decoration text-capitalize"><b>
                {{\App\Models\ProgramLevel::find(request('class_id'))->name().' '.\App\Models\Semester::find(request('semester_id'))->name.' '.$title.' FOR '.\App\Models\Batch::find(request('year_id'))->name.' '.__('text.academic_year')}}
            </b></h4>
            <div class="d-flex overflow-auto"></div>
            <table>
                <thead class="text-capitalize">
                    <tr class="border-top border-bottom border-secondary">
                        <th class="border-left border-right border-secondary" colspan="6"></th>
                        <th class="border-left border-right border-secondary" colspan="4">{{__('text.word_number')}}</th>
                        <th class="border-left border-right border-secondary" colspan="1"></th>
                        <th class="border-left border-right border-secondary" colspan="8">{{__('text.grade_and_number')}}</th>
                    </tr>
                    <tr class="border-top border-bottom border-secondary">
                        <th class="border-left border-right border-secondary">###</th>
                        <th class="border-left border-right border-secondary">{{__('text.word_code')}}</th>
                        <th class="border-left border-right border-secondary">{{__('text.course_title')}}</th>
                        <th class="border-left border-right border-secondary">{{__('text.CV')}}</th>
                        <th class="border-left border-right border-secondary">{{__('text.ST')}}</th>
                        <th class="border-left border-right border-secondary">{{__('text.course_coverage')}}</th>
                        <th class="border-left border-right border-secondary">{{__('text.CR')}}</th>
                        <th class="border-left border-right border-secondary">{{__('text.CE')}}</th>
                        <th class="border-left border-right border-secondary">{{__('text.word_passed')}}</th>
                        <th class="border-left border-right border-secondary">{{__('text.word_failed')}}</th>
                        <th class="border-left border-right border-secondary">{{__('text.percent_pass')}}</th>
                        @foreach($grades as $grade)
                            <th class="border-left border-right border-secondary">{{$grade->grade}}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($courses as $course)
                        <tr class="border-top border-bottom border-secondary">
                            <td class="border-left border-right border-secondary">{{$k++}}</td>
                            <td class="border-left border-right border-secondary">{{$course->subject->code}}</td>
                            <td class="border-left border-right border-secondary">{{$course->subject->name}}</td>
                            <td class="border-left border-right border-secondary">{{$course->coef}}</td>
                            <td class="border-left border-right border-secondary">{{$course->status}}</td>
                            <td class="border-left border-right border-secondary">missing</td>
                            <td class="border-left border-right border-secondary">{{count($students)}}</td>
                            <td class="border-left border-right border-secondary">{{$course->offline_results()->distinct()->count()}}</td>
                            <td class="border-left border-right border-secondary">{{$course->offline_passed($year, request('semester_id'))}}</td>
                            <td class="border-left border-right border-secondary">{{$course->offline_results()->distinct()->count() - $course->offline_passed($year, request('semester_id'))}}</td>
                            <td class="border-left border-right border-secondary">{{
                                number_format(
                                    100*$course->offline_passed($year, request('semester_id'))/
                                    (($course->offline_results()->distinct()->count() == 0) ?
                                    1 :($course->offline_results()->distinct()->count())) , 2
                                    )
                            }}</td>
                            @foreach($grades as $grade)
                                <td class="border-left border-right border-secondary">{{$course->offline_passed_with_grade($grade->grade, $year, request('semester_id'))}}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection
@section('script')
@endsection