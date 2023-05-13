@extends('admin.layout')
@section('section')
    <div class="my-2">
        <table id="transcript_print">
            @foreach ($years as $year)
                <thead class="text-capitalize" style="font-size: smaller;">
                    <!-- header block 1 -->
                    <tr class="border-top border-bottom border-dark">
                        <th class="border-left border-right border-dark" rowspan="2" colspan="6">
                            <div class="fs-3">{{$school->name??'SCHOOL NAME'}}</div>
                            <div class="h6">{{$school->address??'SCHOOL ADDRESS'}}</div>
                        </th>
                        <th class="border-left border-right border-dark" rowspan="2" colspan="6">{{__('text.student_academic_records')}}</th>
                        <th class="border-left border-right border-dark" colspan="4">{{__('text.student_no').': '.$student->matric??'----'}}</th>
                        <th class="border-left border-right border-dark" colspan="4">{{__('text.date_printed').': '. now()->format('d-m-Y')}}</th>
                        <th class="border-left border-right border-dark" colspan="4">{{__('text.word_page').' '.($year['index']??'###').' '.__('text.word_of').' '.count($years)}}</th>
                    </tr>
                    <tr class="border-top border-bottom border-dark">
                        <th class="border-left border-right border-dark" colspan="12">{{$student->name??'----'}}</th>
                    </tr>
                    <!-- header block 1 ends here -->

                    @if ($year['index'] == 1)
                        <!-- header block 2 -->
                        <tr class="border-top border-bottom border-dark">
                            <th class="border-left border-right border-dark" colspan="6">{{__('text.date_of_birth').': '.$student->dob??'----'}}</th>
                            <th class="border-left border-right border-dark" colspan="4">{{__('text.place_of_birth').': '.$student->pob??'----'}}</th>
                            <th class="border-left border-right border-dark" colspan="2">{{__('text.word_sex').': '.$student->gender??'----'}}</th>
                            <th class="border-left border-right border-dark" colspan="4">{{__('text.school_last_attended').': '.$school_last_attended??'----'}}</th>
                            <th class="border-left border-right border-dark" colspan="8">{{__('text.transcript_validity_note')}}</th>
                        </tr>
                        <!-- header block 2 ends here -->

                        <!-- header block 3 -->
                        <tr class="border-top border-bottom border-dark">
                            <th class="border-left border-right border-dark" colspan="8">{{__('text.enrolment_date').': '.\Illuminate\Support\Facades\Date::parse($student->created_at)->format('d-m-Y')}}</th>
                            <th class="border-left border-right border-dark" colspan="3" rowspan="2">{{__('text.student_address')}}</th>
                            <th class="border-left border-right border-dark" colspan="3" rowspan="2">{{__('text.parent_address')}}</th>
                            <th class="border-left border-right border-dark" colspan="3" rowspan="2">{{__('text.word_remarks')}}</th>
                            <th class="border-left border-right border-dark" colspan="7" rowspan="2" style="font-size: x-small;">
                                @foreach($grading as $grd)
                                    <span class="d-flex">
                                        <span class="col-1">{{($grd->grade ?? '')}}</span>
                                        <span class="col-1">{{':'.($grd->lower ?? '')}}</span>
                                        <span class="col-2">{{'-'.($grd->upper ?? '').'%  '}}</span>
                                        <span class="col-1">{{($grd->weight ?? '')}}</span>
                                        <span class="col-7">{{'  '.($grd->remark ?? '')}}</span>
                                    </span>
                                    @endforeach
                                    <span class="d-flex">
                                        <span class="col-1">{{__('text.char_C')}}</span>
                                        <span class="col-11">{{':'.__('text.word_compulsery')}}</span>
                                    </span>
                                    <span class="d-flex">
                                        <span class="col-1">{{__('text.char_R')}}</span>
                                        <span class="col-11">{{':'.__('text.school_requirement')}}</span>
                                    </span>
                            </th>
                        </tr>
                        <tr class="border-top border-bottom border-dark">
                            <th class="border-left border-right border-dark text-left" colspan="8" style="padding-inline: 0; font-size:x-small">
                                <span class="d-flex text-capitalize">
                                    <span class="col-5">{{__('text.word_department')}}: </span>
                                    <span class="col-7 fw-bold">{{ $student->_class()->program->parent->name}}</span>
                                </span>
                                <span class="d-flex text-capitalize">
                                    <span class="col-5">{{__('text.degree_proposed')}}: </span>
                                    <span class="col-7 fw-bold">{{ $student->_class()->program->degree_proposed??'----'.' '.$student->_class()->program->parent->name}}</span>
                                </span>
                                <span class="d-flex text-capitalize">
                                    <span class="col-5">{{__('text.diploma_conferred')}}: </span>
                                    <span class="col-7 fw-bold">{{ $student->_class()->program->conferred_diploma??'----'.' '.$student->_class()->program->parent->name}}</span>
                                </span>
                            </th>
                        </tr>
                        <!-- header block 3 ends here -->
                    @endif
                    
                    @if ($background->background_name == 'PUBLIC HEALTH')
                    <tr class="border-top border-bottom border-dark">
                        <th class="border-left border-right border-dark" colspan="24">{{\App\Models\Batch::find($year['id'])->name??''}}</th>
                    </tr>
                    <tr class="border-top border-bottom border-dark">
                        @foreach ($background->semesters->sortBy('sem')->take(3) as $semester)
                            <th class="border-left border-right border-dark" colspan="8">{{$semester->name??''}}</th>
                        @endforeach
                    </tr>
                    <tr class="border-top border-bottom border-dark">
                        @foreach ($background->semesters->sortBy('sem')->take(3) as $semester)
                            <th class="border-left border-right border-dark">{{__('text.course_code')}}</th>
                            <th class="border-left border-right border-dark">{{__('text.course_title')}}</th>
                            <th class="border-left border-right border-dark">{{__('text.Hrs')}}</th>
                            <th class="border-left border-right border-dark">{{__('text.word_score')}}/100</th>
                            <th class="border-left border-right border-dark">{{__('text.word_grade')}}</th>
                        @endforeach
                    </tr>
                        
                    @else
                        <!-- header block 4 -->
                        <tr class="border-top border-bottom border-dark" style="font-size:x-small;">
                            <th class="border-left border-right border-dark" colspan="2">{{__('text.course_code')}}</th>
                            <th class="border-left border-right border-dark" colspan="5">{{__('text.course_title')}}</th>
                            <th class="border-left border-right border-dark">{{__('text.word_type')}}</th>
                            <th class="border-left border-right border-dark">{{__('text.credit_value')}}</th>
                            <th class="border-left border-right border-dark">{{__('text.word_grade')}}</th>
                            <th class="border-left border-right border-dark">{{__('text.credits_earned')}}</th>
                            <th class="border-left border-right border-dark">{{__('text.grade_point')}}</th>

                            <th class="border-left border-right border-dark" colspan="2">{{__('text.course_code')}}</th>
                            <th class="border-left border-right border-dark" colspan="5">{{__('text.course_title')}}</th>
                            <th class="border-left border-right border-dark">{{__('text.word_type')}}</th>
                            <th class="border-left border-right border-dark">{{__('text.credit_value')}}</th>
                            <th class="border-left border-right border-dark">{{__('text.word_grade')}}</th>
                            <th class="border-left border-right border-dark">{{__('text.credits_earned')}}</th>
                            <th class="border-left border-right border-dark">{{__('text.grade_point')}}</th>
                        </tr>
                        <!-- header block 4 ends here -->
                    @endif
                </thead>
                <tbody style="font-size: smaller;">
                    @if ($background->background_name != 'PUBLIC HEALTH')
                        <tr style="font-weight: bold; margin-bottom: 6px;">
                            @foreach ($background->semesters->sortBy('sem')->take(2) as $semester)
                                <td class="border-left border-right border-dark fw-bold" colspan="12">{{$year['label'].' - '.$semester->name??''}} - {{$year['name']}}</td>                            
                            @endforeach
                        </tr>
                    @endif
                    @if ($background->background_name == 'PUBLIC HEALTH')
                        
                    @else
                        @foreach ($year['results'] as $row)
                        <tr style="border-block: 1px solid rgba(200, 200, 200, 0.1); padding: 0.3rem auto">
                            <td class="border-left border-right border-dark" colspan="2">{{$row['s1'] == null ? null : $row['s1']->subject->code}}</td>
                            <td class="border-left border-right border-dark" colspan="5">{{$row['s1'] == null ? null : $row['s1']->subject->name}}</td>
                            <td class="border-left border-right border-dark">{{$row['s1'] == null ? null : $row['s1']->subject->status}}</td>
                            <td class="border-left border-right border-dark">{{$row['s1'] == null ? null : $row['s1']->subject->coef}}</td>
                            <td class="border-left border-right border-dark">{{$row['s1'] == null ? null : $row['s1']->grade()->grade}}</td>
                            <td class="border-left border-right border-dark">{{$row['s1'] == null ? null : ($row['s1']->passed() ? $row['s1']->subject->coef : 0)}}</td>
                            <td class="border-left border-right border-dark">{{$row['s1'] == null ? null : $row['s1']->grade()->weight}}</td>

                            @if ($row['s2'] != null)
                                <td class="border-left border-right border-dark" colspan="2">{{$row['s2'] == null ? null : $row['s2']->subject->code}}</td>
                                <td class="border-left border-right border-dark" colspan="5">{{$row['s2'] == null ? null : $row['s2']->subject->name}}</td>
                                <td class="border-left border-right border-dark">{{$row['s2'] == null ? null : $row['s2']->subject->status}}</td>
                                <td class="border-left border-right border-dark">{{$row['s2'] == null ? null : $row['s2']->subject->coef}}</td>
                                <td class="border-left border-right border-dark">{{$row['s2'] == null ? null : $row['s2']->grade()->grade}}</td>
                                <td class="border-left border-right border-dark">{{$row['s2'] == null ? null : ($row['s2']->passed() ? $row['s2']->subject->coef : 0)}}</td>
                                <td class="border-left border-right border-dark">{{$row['s2'] == null ? null : $row['s2']->grade()->weight}}</td>
                            @else
                                <td class="border-left border-right border-dark" colspan="12"></td>
                            @endif
                        </tr>
                        @endforeach
                        @if (count($year['results']) > 0)
                            <tr class="text-uppercase">
                                @foreach ($year['result_pack'] as $r)
                                    @if (count($r) > 0)
                                        <td colspan="7" class="border-left border-right border-dark">
                                            <span class="d-flex">{{__('text.total_credits_attempted')}} : {{collect($r)->sum('coef')}}</span>
                                            <span class="d-flex">{{__('text.gpa_credits_attempted')}} : {{collect($r)->filter(function($row)use($non_gpa_courses){return !in_array($row->id, $non_gpa_courses);})->sum('coef')}}</span>
                                            <span class="d-flex text-center" style="font-weight:bold;">{{__('text.semester_gpa')}} : {{number_format((collect($r)->filter(function($row)use($non_gpa_courses){return !in_array($row->id, $non_gpa_courses);})->sum(function($row){return $row->coef*$row->grade()->weight;}))/(collect($r)->filter(function($row)use($non_gpa_courses){return !in_array($row->id, $non_gpa_courses);})->sum('coef')), 2)}}</span>
                                        </td>
                                        <td colspan="5" class="border-left border-right border-dark">
                                            <span class="d-flex">{{__('text.total_credits_earned')}} : {{collect($r)->filter(function($row){return $row->ca_score + $row->exam_score >= 50;})->sum('coef')}}</span>
                                            <span class="d-flex">{{__('text.gpa_credits_earned')}} : {{collect($r)->filter(function($row)use($non_gpa_courses){return !in_array($row->id, $non_gpa_courses) and ($row->ca_score + $row->exam_score >= 50);})->sum('coef')}}</span>
                                        </td>
                                    @endif
                                @endforeach
                            </tr>
                        @endif

                        @if (count($year['resit']) > 0)
                            <tr>
                                <td colspan="24" style="padding: 1.5rem 0;"></td>
                            </tr>
                            <tr>
                                <td colspan="12" class="border-left border-right border-dark"></td>
                                <th class="border border-dark" colspan="2">{{__('text.course_code')}}</th>
                                <th class="border border-dark" colspan="5">{{__('text.course_title')}}</th>
                                <th class="border border-dark">{{__('text.word_type')}}</th>
                                <th class="border border-dark">{{__('text.credit_value')}}</th>
                                <th class="border border-dark">{{__('text.word_grade')}}</th>
                                <th class="border border-dark">{{__('text.credits_earned')}}</th>
                                <th class="border border-dark">{{__('text.grade_point')}}</th>
                            </tr>
                            <tr>
                                <td colspan="12" class="border-left border-right border-dark"></td>
                                <td colspan="12" class="border-left border-right border-dark">{{$year['label'].' - '.\App\Models\Semester::find($year['semesters'][2])->name}} - {{$year['name']}}</td>
                            </tr>

                            @foreach ($year['resit'] as $row)
                            <tr style="border-block: 1px solid rgba(200, 200, 200, 0.1); padding: 0.3rem auto">
                                <td class="border-left border-right border-dark" colspan="12"></td>

                                @if ($row != null)
                                    <td class="border-left border-right border-dark" colspan="2">{{$row == null ? null : $row->subject->code}}</td>
                                    <td class="border-left border-right border-dark" colspan="5">{{$row == null ? null : $row->subject->name}}</td>
                                    <td class="border-left border-right border-dark">{{$row == null ? null : $row->subject->status}}</td>
                                    <td class="border-left border-right border-dark">{{$row == null ? null : $row->subject->coef}}</td>
                                    <td class="border-left border-right border-dark">{{$row == null ? null : $row->grade()->grade}}</td>
                                    <td class="border-left border-right border-dark">{{$row == null ? null : ($row->passed() ? $row->subject->coef : 0)}}</td>
                                    <td class="border-left border-right border-dark">{{$row == null ? null : $row->grade()->weight}}</td>
                                @else
                                    <td class="border-left border-right border-dark" colspan="12"></td>
                                @endif
                            </tr>
                            @endforeach
                            <tr>
                                <td colspan="12" class="border-left border-right border-dark"></td>
                                <td colspan="7" class="border-left border-right border-dark">
                                    <span class="d-flex">{{__('text.total_credits_attempted')}} : {{collect($r)->sum('coef')}}</span>
                                    <span class="d-flex">{{__('text.gpa_credits_attempted')}} : {{collect($r)->filter(function($row)use($non_gpa_courses){return !in_array($row->id, $non_gpa_courses);})->sum('coef')}}</span>
                                    <span class="d-flex text-center" style="font-weight:bold;">{{__('text.semester_gpa')}} : {{number_format((collect($r)->filter(function($row)use($non_gpa_courses){return !in_array($row->id, $non_gpa_courses);})->sum(function($row){return $row->coef*$row->grade()->weight;}))/(collect($r)->filter(function($row)use($non_gpa_courses){return !in_array($row->id, $non_gpa_courses);})->sum('coef')), 2)}}</span>
                                </td>
                                <td colspan="5" class="border-left border-right border-dark">
                                    <span class="d-flex">{{__('text.total_credits_earned')}} : {{collect($r)->filter(function($row){return $row->ca_score + $row->exam_score >= 50;})->sum('coef')}}</span>
                                    <span class="d-flex">{{__('text.gpa_credits_earned')}} : {{collect($r)->filter(function($row)use($non_gpa_courses){return !in_array($row->id, $non_gpa_courses) and ($row->ca_score + $row->exam_score >= 50);})->sum('coef')}}</span>
                                </td>
                            </tr>
                        @endif

                    @endif
                </tbody>
            @endforeach
            <tfoot>
                <tr>
                    <td class="text-uppercase" colspan="7" style="padding: 2rem 0;"></td>
                    <td class="text-uppercase" colspan="10" style="padding: 2rem 0; font-weight: bold; font-size:smaller;">
                        @php
                            $all_courses = [];
                            foreach (collect($years)->pluck('results_bag') as $y_courses){
                                $all_courses = array_merge($all_courses, $y_courses->toArray());
                            }
                            $_all_courses = collect($all_courses)->pluck(['id', 'coef']);
                        @endphp
                        <span class="d-flex">{{__('text.cumm.total_credits_attempted')}} : </span>
                        <span class="d-flex">{{__('text.cumm.gpa_credits_attempted')}} : </span>
                        <span class="d-flex">{{__('text.cumm.total_credits_earned')}} : </span>
                        <span class="d-flex">{{__('text.cummgpa_credits_earned')}} : </span>
                        <span class="d-flex" style="padding: 1rem 0 0 0;">
                            {{__('text.cumm.gpa')}} : 
                            @if ($background->background_name == 'PUBLIC HEALTH')
                            @else

                            @endif
                        </span>
                    </td>
                    <td class="text-uppercase" colspan="7" style="padding: 2rem 0;"></td>
                </tr>
            </tfoot>
        </table>
    </div>
@endsection