<div class="p-5">
    @section('action')
        <button class="btn btn-primary no-print" onclick="window.print()">Print</button>
    @endsection
    @section('style')
        <style>

            @media print {
                @page {
                    size: A4 landscape;
                }

                button.no-print, form.no-print, div.no-print {
                    display: none !important;
                }
            }


        </style>

    @endsection
    <h3 class="text-capitalize font-weight-bold no-print">{{$title}}</h3>
    <div class="no-print">
        <div class="row my-3 py-3 text-capitalize">

            <div class=" col-sm-6 col-md-5 col-lg-2 ">
                <label for="">{{__('Program')}}</label>
                <div>
                    <select  wire:model="filters.program_id" id="" class="form-control rounded" required>
                        <option value="">Select Program</option>
                        @foreach($programs as $k=>$prog)
                            <option value="{{$k}}">{{$prog}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class=" col-sm-6 col-md-5 col-lg-2 ">
                <label for="">{{__('Level')}}</label>
                <div>
                    <select wire:model="filters.level_id" id="" class="form-control rounded" required>
                        <option value="">Select Level</option>
                        @foreach($levels as $k=>$level)
                            <option value="{{$k}}">{{\App\Models\Level::find($level)->level}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class=" col-sm-6 col-md-4 col-lg-2">
                <label for="">{{__('text.word_semester')}}</label>
                <div>
                    <select wire:model="filters.semester_id" id="" class="form-control rounded" required>
                        <option value="">Select Semester</option>
                        @foreach(\App\Models\Semester::all() as $sem)
                            <option value="{{$sem->id}}">{{$sem->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class=" col-sm-6 col-md-3 col-lg-2">
                <label for="">{{__('text.word_year')}}</label>
                <div>
                    <select wire:model="filters.year_id" id="" class="form-control rounded" required>
                        <option value="">Select Year</option>
                        @foreach(\App\Models\Batch::all() as $year)
                            <option value="{{$year->id}}">{{$year->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class=" col-sm-6 col-md-3 col-lg-2 d-flex align-items-end">

                <div class=" px-0 mx-0 d-flex justify-content-end">
                    <button type="button" wire:click="getData()"
                            wire:target="getData"
                            class="btn btn-sm text-capitalize btn-primary rounded"
                            wire:loading.attribute = 'disabled'
                    >
                        <i class="fa fa-spinner d-none" wire:loading.class.remove="d-none"   wire:target="getData"></i>
                        {{__('text.build_frequency_distribution')}}</button>
                </div>
            </div>
        </div>

    </div>
    @php
        $k = 1;

        $courses = \App\Models\ClassSubject::whereIn('id', count($subjects)>0?$subjects->pluck('id'): [])->get();

        $subject_ids = $courses->pluck('subject_id');


    @endphp
    @if($courses->count() > 0)
        @php
            $results = \App\Models\Result::where([
                'batch_id'=>$filters['year_id'],
                'semester_id'=>$filters['semester_id']
            ])->where('exam_score' ,'>',0)
            ->whereIn('subject_id', $subject_ids )
        @endphp

        <div class="my-2">
            <img src="{{ $helpers->getHeader() }}" alt="" class="w-100 d-none">
            <div class="text-center py-2">
                <h4 class="text-decoration text-capitalize"><b>
                        {{ $_title }}
                    </b></h4>
                <div class="d-flex overflow-auto"></div>
                <table class="text-left">
                    <thead class="text-capitalize">
                    <tr class="border-top border-bottom border-secondary">
                        <th class="border-left border-right border-secondary" colspan="6"></th>
                        <th class="border-left border-right border-secondary" colspan="1"></th>
                        <th class="border-left border-right border-secondary"
                            colspan="3">{{__('text.word_number')}}</th>
                        <th class="border-left border-right border-secondary" colspan="1"></th>
                        <th class="border-left border-right border-secondary" colspan="1"></th>
                        <th class="border-left border-right border-secondary"
                            colspan="8">{{__('text.grade_and_number')}}</th>
                    </tr>
                    <tr class="border-top border-bottom border-secondary">
                        <th class="border-left border-right border-secondary">###</th>
                        <th class="border-left border-right border-secondary">{{__('text.word_code')}}</th>
                        <th class="border-left border-right border-secondary">{{__('text.course_title')}}</th>
                        <th class="border-left border-right border-secondary">{{__('text.CV')}}</th>
                        <th class="border-left border-right border-secondary">{{__('text.ST')}}</th>
                        <th class="border-left border-right border-secondary">{{__('text.course_masters') }}</th>
                        <th class="border-left border-right border-secondary">%CC</th>
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
                            <td class="border-left border-right border-secondary">
                                 @php
                                    $students = $course?->class?->_students($filters['year_id'])->where('active', 1)->get()->pluck('id');
                                    \App\Models\User::whereHas('subject', function ($q) use ($course, $filters){
                                        return $q->where(['subject_id'=>$course->subject_id,'batch_id'=>$filters['year_id']]);
                                    })->first()?->name;
                                 @endphp
                            </td>
                            <td class="border-left border-right border-secondary">??</td>
                            <td class="border-left border-right border-secondary">{{ $students->count() }}</td>
                            @php
                                $res = $results->whereIn('student_id',$students);
                                $ce = isset(collect($res->get())->groupBy('subject_id')[$course->subject_id])?collect($res->get())->groupBy('subject_id')[$course->subject_id]->count():0;
                            @endphp

                            <td class="border-left border-right border-secondary">{{
                               $ce
                            }}</td>

                            @php
                                    $data = (isset(collect($res->get())->groupBy('subject_id')[$course->subject_id])?collect($res->get())->groupBy('subject_id')[$course->subject_id]:collect([]))->map(function ($result) use ($grades) {
                                          foreach ($grades as $key => $grade) {
                                                    $total = (($result->ca_score ?? 0 )+ ($result->exam_score ?? 0));
                                                    $passed = $total >= 50;
                                                    if ($total >= $grade->lower && $total <= $grade->upper) {
                                                        return collect([
                                                            'passed'=>$passed,
                                                            'cv' => $result->subject->coef,
                                                            'grade' => ($grade != "") ? $grade->grade : "-",
                                                        ]);


                                            }
                                        }
                                    });
                                    $passed = $data->filter(function ($item){
                                        return $item?$item['passed']:false;
                                    })->count()
                            @endphp
                            <td class="border-left border-right border-secondary">{{ $passed }}</td>
                            <td class="border-left border-right border-secondary">{{$ce - $passed}}</td>
                            <td class="border-left border-right border-secondary">{{
                                    $ce > 0 ? number_format(100*$passed/$ce , 2):0
                                }}</td>
                            @foreach($grades as $grade)
                                <td class="border-left border-right border-secondary">
                                    @php
                                        $gradeCount = $data->filter(function ($item) use ($grade){

                                            return $item?($item['grade'] == $grade->grade):false;
                                        })->count()
                                    @endphp
                                    {{$gradeCount}}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                    <tr class="border-top border-bottom text-capitalize">
                        <th class="border-left border-right border-secondary" colspan="3">@lang('text.grand_total')</th>
                        <th class="border-left border-right border-secondary">{{ $courses->sum('coef') }}</th>
                        <th class="border-left border-right border-secondary" colspan="2"></th>
                        <th class="border-left border-right border-secondary"></th>
                        <th class="border-left border-right border-secondary"></th>
                        <th class="border-left border-right border-secondary"></th>
                        <th class="border-left border-right border-secondary"></th>
                        <th class="border-left border-right border-secondary"></th>
                        <th class="border-left border-right border-secondary"></th>
                        @foreach($grades as $grade)
                            <th class="border-left border-right border-secondary"
                                class="border-left border-right border-secondary">{{$course->passed_with_grade($grade->grade, $year, request('semester_id'))}}</th>
                        @endforeach
                    </tr>
                    <tr>
                        <td class="border-top border-bottom" colspan="{{ 12+$grades->count() }}">
                            <div class="d-flex justify-content-around text-capitalize">
                                <span>CV=@lang('text.credit_value');</span>
                                <span>ST=@lang('text.word_status')</span>
                                <span>%CC=@lang('text.percentage_course_coverage')</span>
                                <span>C=@lang('text.word_compulsery')</span>
                                <span>CR=@lang('text.candidates_registered')</span>
                                <span>CE=@lang('text.candidates_examined')</span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="border-top border-bottom" colspan="{{ 12+$grades->count() }}">
                            <div class="d-flex flex-wrap justify-content-around text-capitalize">
                                @foreach ($course_masters as $cmaster)
                                    <div class="text-center my-5 mx-5">____________________ <br>
                                        <div class="margin-top-4 padding-top-1"
                                             style="max-width: 12rem;">{{ $cmaster->user->name??"NO-NAME" }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
