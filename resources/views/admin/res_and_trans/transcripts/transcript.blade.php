@extends('admin.layout')
@section('section')
    <div class="my-2">
        <table>
            @foreach ($years as $year)
                <thead class="text-capitalize">
                    <!-- header block 1 -->
                    <tr class="border-top border-bottom border-dark">
                        <th class="border-left border-right border-dark" rowspan="2" colspan="2">
                            <div class="fs-3">{{$school->name??'SCHOOL NAME'}}</div>
                            <div class="h6">{{$school->address??'SCHOOL ADDRESS'}}</div>
                        </th>
                        <th class="border-left border-right border-dark" rowspan="2" colspan="5">{{__('text.student_academic_records')}}</th>
                        <th class="border-left border-right border-dark" colspan="2">{{__('text.student_no').': '.$student->matric??'----'}}</th>
                        <th class="border-left border-right border-dark" colspan="3">{{__('text.date_printed').': '. now()->format('d-m-Y')}}</th>
                        <th class="border-left border-right border-dark" colspan="3">{{__('text.word_page').' '.($year['index']??'###').' '.__('text.word_of').' '.count($years)}}</th>
                    </tr>
                    <tr class="border-top border-bottom border-dark">
                        <th class="border-left border-right border-dark" colspan="8">{{$student->name??'----'}}</th>
                    </tr>
                    <!-- header block 1 ends here -->

                    @if ($year['index'] == 1)
                        <!-- header block 2 -->
                        <tr class="border-top border-bottom border-dark">
                            <th class="border-left border-right border-dark" colspan="2">{{__('text.date_of_birth').': '.$student->dob??'----'}}</th>
                            <th class="border-left border-right border-dark" colspan="3">{{__('text.place_of_birth').': '.$student->pob??'----'}}</th>
                            <th class="border-left border-right border-dark" colspan="2">{{__('text.word_sex').': '.$student->gender??'----'}}</th>
                            <th class="border-left border-right border-dark" colspan="2">{{__('text.school_last_attended').': '.$school_last_attended??'----'}}</th>
                            <th class="border-left border-right border-dark" colspan="6">{{__('text.transcript_validity_note')}}</th>
                        </tr>
                        <!-- header block 2 ends here -->

                        <!-- header block 3 -->
                        <tr class="border-top border-bottom border-dark">
                            <th class="border-left border-right border-dark" colspan="3">{{__('text.enrolment_date')}}</th>
                            <th class="border-left border-right border-dark" colspan="3" rowspan="2">{{__('text.student_address')}}</th>
                            <th class="border-left border-right border-dark" colspan="2" rowspan="2">{{__('text.parent_address')}}</th>
                            <th class="border-left border-right border-dark" colspan="1" rowspan="2">{{__('text.remarks')}}</th>
                            <th class="border-left border-right border-dark" colspan="6" rowspan="2">
                                @foreach($grading as $grd)
                                    <span class="d-flex flex-wrap fs-5">
                                        <span class="col-2">{{($grd->grade ?? '')}}</span>
                                        <span class="col-2">{{':'.($grd->lower ?? '')}}</span>
                                        <span class="col-3">{{'-'.($grd->upper ?? '').'%  '}}</span>
                                        <span class="col-2">{{($grd->weight ?? '')}}</span>
                                        <span class="col-3">{{'  '.($grd->remark ?? '')}}</span>
                                    </span>
                                @endforeach
                            </th>
                        </tr>
                        <tr class="border-top border-bottom border-dark">
                            <th class="border-left border-right border-dark" colspan="3">degree_info</th>
                        </tr>
                        <!-- header block 3 ends here -->
                    @endif
                    
                    <!-- header block 4 -->
                    <tr class="border-top border-bottom border-dark">
                        <th class="border-left border-right border-dark">{{__('text.course_code')}}</th>
                        <th class="border-left border-right border-dark">{{__('text.course_title')}}</th>
                        <th class="border-left border-right border-dark">{{__('text.word_type')}}</th>
                        <th class="border-left border-right border-dark">{{__('text.credit_value')}}</th>
                        <th class="border-left border-right border-dark">{{__('text.word_grade')}}</th>
                        <th class="border-left border-right border-dark">{{__('text.credits_earned')}}</th>
                        <th class="border-left border-right border-dark">{{__('text.grade_point')}}</th>

                        <th class="border-left border-right border-dark">{{__('text.course_code')}}</th>
                        <th class="border-left border-right border-dark" colspan="2">{{__('text.course_title')}}</th>
                        <th class="border-left border-right border-dark">{{__('text.word_type')}}</th>
                        <th class="border-left border-right border-dark">{{__('text.credit_value')}}</th>
                        <th class="border-left border-right border-dark">{{__('text.word_grade')}}</th>
                        <th class="border-left border-right border-dark">{{__('text.credits_earned')}}</th>
                        <th class="border-left border-right border-dark">{{__('text.grade_point')}}</th>
                    </tr>

                    <!-- header block 4 ends here -->
                </thead>
                <tbody>
                    <tr>
                        <!-- <td class="border-left border-right border-dark h4" colspan="2">1ST YEAR -FIRST SEMESTER 2022/2023</td> -->
                    </tr>
                </tbody>
            @endforeach
        </table>
    </div>
@endsection