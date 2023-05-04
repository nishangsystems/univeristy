@extends('admin.layout')
@section('section')
    <div class="my-2">
        <table>
            <thead class="text-capitalize">
                <!-- header block 1 -->
                <tr class="border-top border-bottom border-dark">
                    <th class="border-left border-right border-dark" rowspan="2" colspan="2">school_info</th>
                    <th class="border-left border-right border-dark" rowspan="2" colspan="5">student_acc_records</th>
                    <th class="border-left border-right border-dark" colspan="2">student_matric_here</th>
                    <th class="border-left border-right border-dark" colspan="3">print_date</th>
                    <th class="border-left border-right border-dark" colspan="3">page_no</th>
                </tr>
                <tr class="border-top border-bottom border-dark">
                    <th class="border-left border-right border-dark" colspan="8">student_name</th>
                </tr>
                <!-- header block 1 ends here -->


                <!-- header block 2 -->
                <tr class="border-top border-bottom border-dark">
                    <th class="border-left border-right border-dark" colspan="2">DoB</th>
                    <th class="border-left border-right border-dark" colspan="3">PoB</th>
                    <th class="border-left border-right border-dark" colspan="2">sex</th>
                    <th class="border-left border-right border-dark" colspan="2">school_last_attended</th>
                    <th class="border-left border-right border-dark" colspan="6">validity_info</th>
                </tr>
                <!-- header block 2 ends here -->

                <!-- header block 3 -->
                <tr class="border-top border-bottom border-dark">
                    <th class="border-left border-right border-dark" colspan="3">enrolment_date</th>
                    <th class="border-left border-right border-dark" colspan="3" rowspan="2">student_address</th>
                    <th class="border-left border-right border-dark" colspan="2" rowspan="2">parent_address</th>
                    <th class="border-left border-right border-dark" colspan="1" rowspan="2">remarks</th>
                    <th class="border-left border-right border-dark" colspan="6" rowspan="2">grading_info</th>
                </tr>
                <tr class="border-top border-bottom border-dark">
                    <th class="border-left border-right border-dark" colspan="3">degree_info</th>
                </tr>
                <!-- header block 3 ends here -->
                
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
                    <td class="border-left border-right border-dark h4" colspan="2">1ST YEAR -FIRST SEMESTER 2022/2023</td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection